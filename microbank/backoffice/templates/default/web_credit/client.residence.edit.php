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

    #select_area .col-sm-6:nth-child(2n+1) {
        padding-left: 0;
        padding-right: 3px;
        margin-bottom: 10px;
    }
    #select_area .col-sm-6:nth-child(2n) {
        padding-right: 0;
        padding-left: 3px;
        margin-bottom: 10px;
    }
</style>
<?php
$client_info=$output['client_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Residence</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Residence</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 900px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Residence</h5>
                </div>
                <div class="content">
                    <form class="form" id="frm_residence" method="POST" action="<?php echo getUrl('web_credit', 'editMemberResidence', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="obj_guid" value="<?php echo $client_info['obj_guid']?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Location' ?></label>
                            <div class="col-sm-9" id="select_area">
                                <?php if (!empty($output['region_list'])) { ?>
                                    <?php foreach ($output['region_list'] as $area) { ?>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="id<?php echo reset($area)['node_level']?>">
                                                <option value="0">Please Select</option>
                                                <?php foreach ($area as $val) { ?>
                                                    <option value="<?php echo $val['uid'] ?>" is-leaf="<?php echo $val['is_leaf'] ?>" <?php echo $val['selected']?'selected':''?>><?php echo $val['node_text'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php }?>
                                <?php }?>
                            </div>

                            <div class="col-sm-9 col-sm-offset-3 form-group">
                                <label for="" class="form-label">Group</label>
                                <input type="text" class="form-control" name="address_group" value="<?php echo $output['residence']['address_group']; ?>">
                            </div>


                            <div class="col-sm-9 col-sm-offset-3 form-group">
                                <label for="" class="form-label">Street</label>
                                <input type="text" class="form-control" name="street" value="<?php echo $output['residence']['street']; ?>">
                            </div>


                            <div class="col-sm-9 col-sm-offset-3 form-group">
                                <label for="" class="form-label">House No.</label>
                                <input type="text" class="form-control" name="house_number" value="<?php echo $output['residence']['house_number']; ?>">
                            </div>


                           <!-- <div class="col-sm-9 col-sm-offset-3">
                                <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="<?php /*echo $output['residence']['address_detail']*/?>">
                            </div>-->

                        </div>
                    </form>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3" style="margin-top: 15px">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger">
                                <i class="fa fa-check"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        if('<?php echo !$output['region_list']?>'){
            getArea(0);
        }
    });
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
//        var _address_region = '';
//        $('#select_area select').each(function () {
//            if ($(this).val() != 0) {
//                _address_region += $(this).find('option:selected').text() + ' ';
//            }
//        })
//        var address_detail = $.trim($('input[name="address_detail"]').val());
//        if (address_detail) {
//            _address_region += address_detail;
//        }
//        $('input[name="full_text"]').val(_address_region);
        $('#frm_residence').submit();
    }

    $('#select_area').delegate('select', 'change', function () {
        var _value = $(this).val();
        $('input[name="address_id"]').val(_value);
        $(this).closest('div').nextAll().remove();

        if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
            getArea(_value);
        }
    })

    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "setting/area.list",
            dynamic: {
                api: "setting",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        })
    }

</script>






