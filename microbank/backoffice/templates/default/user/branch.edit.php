<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
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
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'addBranch', array(), false, BACK_OFFICE_SITE_URL)?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['branch_info']['uid']?>">
            <input type="hidden" name="address_id" value="<?php echo $output['branch_info']['address_id']?>">
            <input type="hidden" name="address_region" value="<?php echo $output['branch_info']['address_region']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="branch_code" placeholder="" value="<?php echo $output['branch_info']['branch_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="branch_name" placeholder="" value="<?php echo $output['branch_info']['branch_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Contact phone' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="contact_phone" value="<?php echo $output['branch_info']['contact_phone'] ?>" placeholder="">
                    <div class="error_msg"></div>
                </div>
            </div>



            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Location' ?></label>
                <div class="col-sm-9" id="select_area">
                    <?php if (!empty($output['region_list'])) { ?>
                        <?php foreach ($output['region_list'] as $area) { ?>
                            <div class="col-sm-6">
                                <select class="form-control">
                                    <option value="0">Please Select</option>
                                    <?php foreach ($area as $val) { ?>
                                        <option value="<?php echo $val['uid'] ?>" is-leaf="<?php echo $val['is_leaf'] ?>" <?php echo $val['selected']?'selected':''?>><?php echo $val['node_text'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="col-sm-9 col-sm-offset-3">
                    <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="<?php echo $output['branch_info']['address_detail']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Map</label>
                <div class="col-sm-9" style="width: 500px;height: 300px;border: 1px solid #9e9e9e">
                    <?php
                        $point=array('x'=>$output['branch_info']['coord_x'],'y'=>$output['branch_info']['coord_y']);
                        include_once(template('widget/google.map.marking'));
                    ?>

                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Is HQ';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <input type="checkbox" value="1" name="is_hq" <?php if($output['branch_info']['is_hq']){ echo 'checked'; } ?> >

                </div>
            </div>



            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="status" <?php echo $output['branch_info']['status'] == 1?'checked':''?>><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="status" <?php echo $output['branch_info']['status'] == 0?'checked':''?>><?php echo 'Invalid'?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger" id="submit_save"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var _address_region;
    $(function () {
        if(!'<?php echo $output['branch_info']['address_id']?>'){
            getArea(0);
        }

        $('#select_area').delegate('select', 'change', function () {
            var _value = $(this).val();
            $('input[name="address_id"]').val(_value);
            $(this).closest('div').nextAll().remove();
            _address_region = '';
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region += $(this).find('option:selected').text() + ' ';
                }
            })
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
            if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
                getArea(_value);
            }
        })

        $('input[name="address_detail"]').change(function () {
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
        })

        $('#submit_save').click(function () {
            if (!$(".form-horizontal").valid()) {
                return;
            }
            var _address_region_1 = [];
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region_1.push($(this).find('option:selected').text());
                }
            })
            var _address_detail_1 = $.trim($('input[name="address_detail"]').val());
            if (_address_detail_1) {
                _address_region_1.push(_address_detail_1);
            }
            var _address = _address_region_1.reverse().join(', ');
            $('input[name="address_region"]').val(_address);

            $('.form-horizontal').submit();
        })
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            branch_code: {
                required: true,
                checkNumAndStr:true
            },
            branch_name: {
                required: true
            }
        },
        messages: {
            branch_code: {
                required: '<?php echo 'Required!'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            },
            branch_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

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

    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });
</script>