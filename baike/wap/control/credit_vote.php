<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/10/25
 * Time: 11:01
 */
class credit_voteControl
{
    public function __construct()
    {
        Tpl::setLayout('weui_layout');
        Tpl::setDir('credit_vote');
    }


    public function votePageOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_credit_grant = M('member_credit_grant');
        $credit_grant = $m_member_credit_grant->find(array('uid' => $uid));
        if( !$credit_grant ){
            showMessage('Invalid param of grant id:'.$uid);
        }
        Tpl::output('credit_suggest', $credit_grant);

        $member_id = $credit_grant['member_id'];

        $member_category = loan_categoryClass::getMemberCreditCategoryList($member_id);
        $credit_product = (new member_credit_grant_productModel())->select(array(
            'grant_id' => $uid
        ));

        foreach( $credit_product as $k=>$v ){
            $credit_product[$k]['credit_category_info'] = $member_category[$v['member_credit_category_id']];
        }
        //print_r($credit_product);
        Tpl::output('credit_product',$credit_product);


        $client_info = (new memberModel())->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        Tpl::output('html_title','Credit Vote');

        Tpl::showPage('credit.vote.page');
    }

    public function voteSubmitOp()
    {
        $params = array_merge($_GET,$_POST);
        $class_credit_grant = new member_credit_grantClass();
        $rt = $class_credit_grant->submitVoteCreditApplication($params);
        if ($rt->STS) {
            showMessage('Vote success!');
        } else {
            showMessage('Vote fail:'.$rt->MSG,'',20);
        }
    }
}