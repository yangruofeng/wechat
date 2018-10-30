<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/28
 * Time: 13:36
 */
class um_user_trackModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('um_user_track');
    }

    public function getTrackList($user_id, $filters)
    {
        $sql = "SELECT * FROM um_user_track WHERE user_id = " . intval($user_id);
        if ($filters['start_date']) {
            $start_date = date('Y-m-d', strtotime($filters['start_date']));
            $sql .= " AND sign_day >= '$start_date' ";
        }

        if ($filters['end_date']) {
            $end_date = date('Y-m-d', strtotime($filters['end_date']));
            $sql .= " AND sign_day <= '$end_date' ";
        }

        $rows = $this->reader->getRows($sql);
        $track_arr = array();
        foreach ($rows as $row) {
            $track_arr[] = array(
                'x' => $row['coord_x'],
                'y' => $row['coord_y']
            );
        }
        return $track_arr;
    }

    public function getDayTraceList($user_id,$day)
    {
        $user_id = intval($user_id);
        $day = date('Y-m-d',strtotime($day));
        $sql = "select * from um_user_track where user_id='$user_id' and sign_day='$day'
        order by sign_time asc ";
        $list = $this->reader->getRows($sql);
        return $list;
    }
}