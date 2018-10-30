<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/5
 * Time: 10:48
 */
class staffControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("staff");
        Language::read('staff');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('staff');

    }

    /**
     * 指纹页
     */
    public function registerFingerprintOp()
    {
        Tpl::showPage('fingerprint.collection');
    }

    /**
     * 获取staff信息和指纹
     * @param $p
     * @return ormResult|result
     */
    public function getStaffFingermarkOp($p)
    {
        $search_type = trim($p['search_type']);
        $search_text = trim($p['search_text']);
        $filter = array();
        if ($search_type == 'id_card') {
            $filter['id_card_number'] = $search_text;
        } elseif ($search_type == 'cid') {
            $filter['obj_guid'] = $search_text;
        } else {
            return new result(false, 'Invalid type.');
        }
        $filter['staff_status'] = array('<=', staffStatusEnum::REGULAR_EMPLOYEE);
        $rt = staffClass::getStaffFingermark($filter);
        return $rt;
    }

}