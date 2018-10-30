<?php
/**
 * 处理用户自定义的科目voucher
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 16:59
 */
class bizManualVoucherClass extends bizBaseClass
{

    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::MANUAL_VOUCHER;
        $this->bizModel = new biz_manual_voucherModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function execute($args)
    {
        /*args:
         *biz_amount"=>$biz_amount,
            "biz_currency"=>$biz_currency,
            "biz_remark"=>$biz_remark,
            "biz_date"=>Now(),//要前端输入
            "dr_list"=>$dr_list,
            "cr_list"=>$cr_list
         * */


        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;

        $biz->amount = $args['biz_amount'];
        $biz->currency = $args['biz_currency'];
        $biz->state = bizStateEnum::CREATE;
        $biz->remark = $args['biz_remark'];
        $biz->create_time = Now();
        $biz->is_outstanding = 0;
        $biz->branch_id = $args['branch_id']?:0;
        $biz->operator_id=$args['operator_id'];
        $biz->operator_name=$args['operator_name'];

        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }
        //插入biz-detail
        $dm_biz=new biz_manual_voucher_detailModel();
        $dr_list=$args['dr_list'];
        $dr_book=array();
        foreach($dr_list as $item){
            $detail_row=$dm_biz->newRow(
                array(
                    'biz_id'=>$biz->uid,
                    'is_debit'=>$item['is_debit'],
                    "gl_code"=>$item['gl_code'],
                    "gl_subject"=>$item['gl_subject'],
                    "gl_name"=>$item['gl_name'],
                    "gl_amount"=>$item['gl_amount']
                )
            );
            $dr_book[]=array(
                'gl_code'=>$item['gl_code'],
                'gl_amount'=>$item['gl_amount'],
                'gl_subject'=>$item['gl_subject'],
                'direction'=>accountingDirectionEnum::DEBIT
            );
            $ret_detail=$detail_row->insert();
            if(!$ret_detail->STS){
                return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }
        $cr_list=$args['cr_list'];
        $cr_book=array();
        foreach($cr_list as $item){
            $detail_row=$dm_biz->newRow(
                array(
                    'biz_id'=>$biz->uid,
                    'is_debit'=>$item['is_debit'],
                    "gl_code"=>$item['gl_code'],
                    "gl_subject"=>$item['gl_subject'],
                    "gl_name"=>$item['gl_name'],
                    "gl_amount"=>$item['gl_amount']
                )
            );
            $cr_book[]=array(
                'gl_code'=>$item['gl_code'],
                'gl_amount'=>$item['gl_amount'],
                'gl_subject'=>$item['gl_subject'],
                'direction'=>accountingDirectionEnum::CREDIT
            );
            $ret_detail=$detail_row->insert();
            if(!$ret_detail->STS){
                return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        // 更新账本
        $trading=new manualVoucherTradingClass($dr_book,$cr_book,$args['biz_currency'],$args['biz_remark']);
        $rt = $trading->execute();
        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $trade_id = intval($rt->DATA);
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $biz->passbook_trading_id = $trade_id;
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }
        $biz->biz_id = $biz->uid;

        return new result(true,'success',$biz);

    }


}