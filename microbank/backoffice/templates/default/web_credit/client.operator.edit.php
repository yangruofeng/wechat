<style>
    .btn {
        border-radius: 0;
    }

    .table > tbody > tr > td {
        background-color: #ffffff;
    !important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

    .operator-list .col-sm-3:first-child {
        padding-left: 0!important;
    }

    .operator-list .radio {
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }
</style>
<?php
$client_info = $output['client_info'];
$operator_list = $output['operator_list'];
$member_operator = $output['member_operator'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                <li><a  class="current"><span>Operator</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Operator</h5>
                </div>
                <div class="content">
                    <form id="frm_operator" method="POST" action="<?php echo getUrl('web_credit', 'editMemberOperator', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="uid" value="<?php echo $client_info['uid']?>">
                        <table class="table">
                            <tr>
                                <td style="width:130px;"><label class="control-label">Current Operator</label></td>
                                <td>
                                    <?php echo $member_operator['user_name']?$member_operator['user_name'].'('.$member_operator['user_code'].')':'None';?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:130px;"><label class="control-label">Change Operator</label></td>
                                <td class="operator-list">
                                    <?php foreach ($operator_list as $k => $v) {?>
                                        <div class="col-sm-3">
                                            <div class="radio">
                                                <label title="<?php echo $v['user_name'] . '(' . $v['user_code'] . ')'; ?>">
                                                    <input type="radio" name="officer_id" value="<?php echo $v['uid'];?>" <?php if($v['uid'] == $member_operator['officer_id']){echo 'checked';}?>>
                                                    <?php echo $v['user_name'] . '(' . $v['user_code'] . ')'; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php }?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                    <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger">
                                        <i class="fa fa-check"></i>
                                        Submit
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        $('#frm_operator').submit();
    }

</script>






