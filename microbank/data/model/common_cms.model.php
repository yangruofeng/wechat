<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class common_cmsModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('common_cms');
    }

    public function getHelpList($params = array())
    {

        $type = intval($params['type']);
        $category = trim($params['category']);

        $page_num = intval($params['page_num']) ?: 1;
        $page_size = intval($params['page_size']) ?: 20;
        if ($type == 1) {
            $sql = "SELECT * FROM common_cms WHERE is_system = 1 AND state = 100";
        } else {
            $member_id = intval($params['member_id']);
//            $sql = "SELECT * FROM common_cms WHERE questioner_id = $member_id AND is_system = 0 AND state = 100";
            $sql = "SELECT * FROM common_cms WHERE is_system = 0 AND state = 100";
        }
        if ($category) {
            $sql .= " AND category = '" . $category . "'";
        }
        $sql .= " ORDER BY uid DESC";
        $page = $this->reader->getPage($sql, $page_num, $page_size);

        $count = $page->count;
        $page_count = $page->pageCount;
        $helps = $page->rows;

        foreach ($helps as $key => $help) {
            $help['create_time'] = dateFormat($help['create_time']);
            $helps[$key] = $help;
        }

        return new result(true, 'success', array(
            'total_num' => $count,
            'total_pages' => $page_count,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $helps ?: null
        ));
    }

    public function getHelpDetail($uid)
    {
        $detail = $this->find(array('uid' => $uid));
        return new result(true, 'success', array(
            'detail' => $detail,
        ));
    }
}