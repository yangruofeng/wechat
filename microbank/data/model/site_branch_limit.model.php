<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/26
 * Time: 10:14
 */
class site_branch_limitModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('site_branch_limit');
    }

    public function fillLimitInfoForBranches(&$branchList) {
        if(!$branchList) return array();
        $branch_ids = array_column($branchList, 'uid');
        $branch_id_str = '(' . implode(',', $branch_ids) . ')';
        $sql = 'SELECT * FROM site_branch_limit WHERE branch_id IN ' . $branch_id_str;
        $limit_list = $this->reader->getRows($sql);
        $limit_arr = array();
        foreach ($limit_list as $limit) {
            $limit_arr[$limit['branch_id']][$limit['limit_key']] = array(
                'max_per_day' => $limit['max_per_day'],
                'max_per_time' => $limit['max_per_time']
            );
        }

        foreach ($branchList as $key => $row) {
            $limit = $limit_arr[$row['uid']];
            $branchList[$key]['limit_arr'] = $limit;
        }

        return $branchList;
    }
}