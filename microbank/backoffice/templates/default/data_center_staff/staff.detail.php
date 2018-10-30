<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
     .table {
        margin-bottom: 20px;
    }
    .data-center-btn {
        margin-bottom: 20px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Staff Center</h3>
            <ul class="tab-base">
                <li><a  href="<?php echo getUrl('data_center_staff','index',array(),false,BACK_OFFICE_SITE_URL); ?>"><span>List</span></a></li>
                <li><a  class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php $info = $output['info'];?>
    <input type="hidden" id="staff_id" value="<?php echo $info['uid'];?>">
    <div class="container">
        <div class="business-content">
            <div class="">
                <table class="table table-bordered table-no-background">
                    <tr>
                        <td><label for="">Staff ID</label></td>
                        <td><?php echo $info['uid'];?></td>
                        <td><label for="">Staff Code</label></td>
                        <td><?php echo $info['user_code'];?></td>
                        <td><label for="">Staff Name</label></td>
                        <td><?php echo $info['user_name'];?></td>
                    </tr>
                    <tr>
                        <td><label for="">Phone</label></td>
                        <td><?php echo $info['mobile_phone'];?></td>
                        <td><label for="">Position</label></td>
                        <td><?php echo $info['user_position'];?></td>
                        <td><label for="">Branch</label></td>
                        <td><?php echo $info['branch_name'];?></td>
                    </tr>
                    <tr>
                        <td><label for="">Last Login Ip</label></td>
                        <td><?php echo $info['last_login_ip'];?></td>
                        <td><label for="">Last Login Area</label></td>
                        <td><?php echo $info['last_login_area'];?></td>
                        <td><label for="">Last Login Time</label></td>
                        <td><?php echo timeFormat($info['last_login_time']);?></td>
                    </tr>
                    <tr>
                        <td><label for="">Status</label></td>
                        <td class="<?php echo $info['user_status'] ? 'green' : 'red';?>"><?php echo $info ? $info['user_status'] ? 'Active' : 'Inactive' : '';?></td>
                        <td><label for="">Remark</label></td>
                        <td><?php echo $info['remark'];?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div class="data-center-btn">
                    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php $info['uid']?:'disabled';?>" onclick="btn_controller_op(this, 'log')">Log</button>
                    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php $info['uid']?:'disabled';?>" onclick="btn_controller_op(this, 'transactions')">Transactions</button>
                    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php $info['uid']?:'disabled';?>" onclick="btn_controller_op(this, 'voucher')">Journal Voucher</button>
                    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php $info['uid']?:'disabled';?>" onclick="btn_controller_op(this, 'authorization')">Authorization List</button>
                </div>
                <div class="data-center-list"></div>
            </div>
        </div>
    </div>
</div>
<script>
    var STAFF_ID = $('#staff_id').val();
    function btn_controller_op(el, type){
        if(!STAFF_ID) return;
        $(el).addClass("btn-success current").siblings().removeClass("btn-success current");
        var tpl = "", api = "", method = "", param = {};
        switch(type){
            case "log":
                tpl = "data_center_staff/staff.log.index";
                api = "data_center_staff";
                method = "showStaffLogPage";
                param.staff_id = STAFF_ID;
                break;
            case "transactions":
                tpl = "common/passbook.account.flow.index";
                api = "common";
                method = "passbookAccountFlowPage";
                param.obj_uid = STAFF_ID;
                param.obj_type = <?php echo objGuidTypeEnum::UM_USER;?>;
                param.is_ajax = 1;
                break;
            case "voucher":
                tpl = "common/passbook.voucher.index";
                api = "common";
                method = "passbookJournalVoucherPage";
                param.obj_uid = STAFF_ID;
                param.obj_type = <?php echo objGuidTypeEnum::UM_USER;?>;
                param.is_ajax = 1;
                break;
            case "authorization":
                tpl = "data_center_staff/staff.authorization";
                api = "data_center_staff";
                method = "showAuthorization";
                param.staff_id = STAFF_ID;
                break;
            default:
                break;
        }

        yo.dynamicTpl({
            tpl: tpl,
            dynamic: {
                api: api,
                method: method,
                param: param
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }
</script>
