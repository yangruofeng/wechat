<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_memberControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('certification,operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_member");
    }

    /**
     * 列表页
     */
    public function indexOp()
    {
        $filter = array();
        $user_position = $this->user_position;
        if( $user_position == userPositionEnum::BRANCH_MANAGER ){
            $filter['branch_id'] = $this->branch_id;
        }
        $summary = memberDataClass::getMemberSummary($filter);
        Tpl::output("summary", $summary);
        Tpl::showPage("index");
    }



    public function showClientListByStatePageOp()
    {
        Tpl::output("type", $_GET['type']);
        Tpl::output("title", $_GET['title']);
        Tpl::output("count", $_GET['count']);
        Tpl::showPage("client.state.index");
    }

    /**
     * 获取会员数据
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $currency = trim($p['currency']);
        $ck = intval($p['ck']);
        $type = trim($p['type']);

        $filters = array(
            'search_text' => $search_text,
            'currency' => $currency,
            'ck' => $ck,
            'type' => $type
        );

        $user_position = $this->user_position;
        // 如果是BM查看，需要过滤分行
        if( $user_position == userPositionEnum::BRANCH_MANAGER ){
            $filters['branch_id'] = $this->branch_id;
        }


        $data = memberDataClass::getMemberList($pageNumber, $pageSize, $filters);
        return $data;
    }

}