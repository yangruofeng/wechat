<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

     .ibox-title {
         padding-top: 12px!important;
         min-height: 40px;
     }
</style>
<?php
$client_info=$output['client_info'];
$member_co_list=$output['member_co_list'];
$co_list=$output['co_list'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                <li><a  class="current"><span>Credit Officer</span></a></li>
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
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Credit Officer</h5>
                </div>
                <div class="content">
                    <form id="frm_co" method="POST" action="<?php echo getUrl('web_credit', 'editMemberCo', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <table class="table">
                            <tr>
                                <td><label class="control-label">List</label></td>
                                <td>
                                    <?php
                                    $user_co = array_column($member_co_list, 'officer_id');
                                    foreach ($co_list as $co) { ?>
                                        <div class="col-sm-4">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="co_id[]" value="<?php echo $co['uid']; ?>" <?php echo in_array($co['uid'],$user_co)? 'checked' : ''?>>
                                                <?php echo $co['user_name']; ?>
                                            </label>
                                        </div>
                                    <?php } ?>
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
        $('#frm_co').submit();
    }

</script>






