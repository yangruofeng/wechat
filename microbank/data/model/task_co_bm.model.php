<?php
/**
 * 消息类型的任务，只需要判断消息有没有读取
 * Created by PhpStorm.
 * User: PC
 * Date: 7/12/2018
 * Time: 2:40 PM
 */
class task_co_bmModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('task_co_bm');
    }
    public function cancelOldTask($member_id,$co_id){
        $sql="update ".$this->name." set state=0 where member_id='".$member_id."' and co_id=".qstr($co_id);
        return $this->conn->execute($sql);
    }
    public function getCoSubmitTaskByMemberId($member_id){
        $rows=$this->select(array(
            "member_id"=>$member_id,
            "state"=>array(">=",commonApproveStateEnum::APPROVING)
        ));
        $rows=resetArrayKey($rows,"co_id");
        return $rows;
    }
    public function getCoSubmitTaskByBranchId($branch_id){
        $sql="SELECT tum.*,tcb.co_id,tcb.co_name,tcb.member_id,cm.`display_name`,cm.`obj_guid`,cm.`login_code` ";
        $sql.=" FROM task_user_msg tum";
        $sql.=" INNER JOIN task_co_bm tcb ON tum.`task_id`=tcb.uid";
        $sql.=" INNER JOIN client_member cm ON tcb.`member_id`=cm.`uid`";
        $sql.=" WHERE tum.task_state='".userTaskStateTypeEnum::RUNNING."' AND tum.`receiver_id`='".$branch_id."' AND tum.`receiver_type`='".objGuidTypeEnum::SITE_BRANCH."'";
        $rows=$this->reader->getRows($sql);
        return $rows;
    }
    public function cancelOldTaskOfMemberId($member_id){
        $sql="update ".$this->name." set state=0 where member_id='".$member_id."'";
        return $this->conn->execute($sql);
    }
    public function getAllMsgTaskOfMemberIdAndCoId($member_id,$co_id){
        //先取出co针对该member做的所有task
        $rows=$this->select(array("co_id"=>$co_id,"member_id"=>$member_id));
        $rows=resetArrayKey($rows,"uid");
        $ret=array();
        foreach($rows as $item){
            $ret[]=array(
                "operator_name"=>"Me",
                "msg"=>$item['submit_comment'],
                "msg_time"=>$item['submit_time']
            );
            if($item['handle_comment']){
                $ret[]=array(
                    "operator_name"=>$item['handler_name'],
                    "msg"=>$item['handle_comment'],
                    "msg_time"=>$item['handle_time']
                );
            }
        }
        return $ret;
    }
}

