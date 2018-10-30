<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class client_blackModel extends tableModelBase{

    public function __construct(){
        parent::__construct('client_black');
    }


    public function getBlackClientByType($type,$page_num,$page_size,$filter=array())
    {
        $r = $this->reader;

        $where = array();
        $where[] = " b.type='$type' ";

        if ( $filter['obj_guid']) {
            $where[] = " m.obj_guid = " . qstr($filter['obj_guid']);
        }
        if ( $filter['member_name']) {
            $where[] = ' m.display_name like "%' . qstr2($filter['member_name']) . '%"';
        }

        $sql = "select m.* from client_black b left join  client_member m on m.uid=b.member_id  ";
        $sql .= " where ".implode('and',$where);

        $data = $r->getPage($sql, $page_num, $page_size);
        return $data;
    }

    public function getClientListNotInBlackOfType($type,$page_num,$page_size,$filter=array())
    {
        $r = $this->reader;

        $where = array();
        $where[] = " 1=1 ";

        if ( $filter['obj_guid']) {
            $where[] = " m.obj_guid = " . qstr($filter['obj_guid']);
        }
        if ( $filter['member_name']) {
            $where[] = ' m.display_name like "%' . qstr2($filter['member_name']) . '%" ';
        }

        $where[] = " ( b.type is null) ";  // 过滤在黑名单中的

        $sql = "select m.*,b.type from client_member m left join ( select * from client_black where type='$type' )  b  on m.uid=b.member_id ";
        $sql .= " where ".implode('and',$where);
        $sql .= " group by m.uid ";
        $data = $r->getPage($sql, $page_num, $page_size);
        return $data;

    }


    public function updateClientBlack($member_id,$type,$state,$user_info)
    {
        if( $state ){
            //添加到黑名单
            $row = $this->getRow(array(
                'member_id' => $member_id,
                'type' => $type
            ));
            if( $row ){
                // 已经在黑名单
                return new result(true);
            }
            $row = $this->newRow();
            $row->member_id = $member_id;
            $row->type = $type;
            $row->auditor_id = $user_info['user_id'];
            $row->auditor_name = $user_info['user_name'];
            $row->update_time = Now();
            $insert = $row->insert();
            if( !$insert->STS ){
                return new result(false,'Handle fail.');
            }

        }else{

            // 移出黑名单
            $sql = "delete from client_black where member_id='$member_id' and type='$type' ";
            $del = $this->conn->execute($sql);
            if( !$del->STS ){
                return new result(false,'Handle fail.');
            }
        }

        return new result(true);

    }

    /**
     * 获取role信息
     * @param $uid
     * @return result
     */
    public function getBlackInfo($member_id){
        $info = $this->find(array('member_id' => $member_id));
        if (empty($info)) {
            return new result(false, 'No info');
        }
        return new result(true, '', $info);
    }


}
