<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/18
 * Time: 10:27
 */
class partner_limit_settingModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('partner_limit_setting');
    }

    public function editSetting($params)
    {
        $partner_code = $params['partner_code'];
        $biz_type = $params['biz_type'];
        $per_time = intval($params['per_time']);
        $per_day = intval($params['per_day']);
        if( !$partner_code || !$biz_type ){
            return new result(false,'Empty param.',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $per_time < 0 || $per_day < 0 ){
            return new result(false,'Invalid amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }
        $partner_info = (new partnerModel())->getRow(array(
            'partner_code' => $partner_code
        ));
        if( !$partner_info ){
            return new result(false,'No partner info:'.$partner_code,null,errorCodesEnum::NO_DATA);
        }
        $row = $this->getRow(array(
            'partner_code' => $partner_code,
            'biz_type' => $biz_type
        ));
        if( $row ){
            $row->per_time = $per_time;
            $row->per_day = $per_day;
            $row->update_time = Now();
            $up = $row->update();
            return $up;
        }else{
            $row = $this->newRow();
            $row->partner_code = $partner_code;
            $row->partner_name = $partner_info['partner_name'];
            $row->biz_type = $biz_type;
            $row->per_time = $per_time;
            $row->per_day = $per_day;
            $row->create_time = Now();
            $insert = $row->insert();
            return $insert;
        }
    }

}