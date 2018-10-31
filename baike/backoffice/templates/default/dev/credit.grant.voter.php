<style>
    .voter-limit-item{
        margin-bottom: 10px;
    }
</style>
<?php
$limit_list=$output['limit_list'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Industry</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Credit Voter</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 800px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Limit Voter For Granting Credit'?></label>
                <div class="col-sm-9">
                    <div style="height: 34px;line-height: 34px;width: 30px;float: left">
                        <i class="fa fa-plus add-survey" onclick="btn_add_item_onclick(this)" style="margin-top: 10px;margin-left: 5px;cursor: pointer;" title="Add"></i>
                    </div>
                    <div class="error_msg survey_error" style="width: 330px;float: left;line-height: 30px"></div>
                </div>
                <?php foreach ($limit_list as $key => $item) { ?>
                    <div class="col-sm-offset-3 col-sm-9 voter-limit-item">
                        <div class="col-sm-11">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="min_credit[]" placeholder="Min-Credit" value="<?php echo $item['min_credit']?>">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="max_credit[]" placeholder="Max-Credit" value="<?php echo $item['max_credit']?>">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="voter[]" placeholder="Voter Numbers" value="<?php echo $item['voter']?>">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <i class="fa fa-minus" style="cursor: pointer;" onclick="btn_remove_item(this)"></i>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="submit" class="btn btn-danger"  style="min-width: 80px;"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/html" id="survey-info">
    <div class="col-sm-offset-3 col-sm-9 voter-limit-item">
        <div class="col-sm-11">
            <div class="col-sm-4">
                <input type="text" class="form-control" name="min_credit[]" placeholder="Min-Credit" value="">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="max_credit[]" placeholder="Max-Credit" value="">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="voter[]" placeholder="Voter Numbers" value="">
            </div>
        </div>
        <div class="col-sm-1">
            <i class="fa fa-minus"  style="cursor: pointer;" onclick="btn_remove_item(this)"></i>
        </div>
    </div>
</script>
<script>

    function btn_add_item_onclick(_e){
        var tpl = $('#survey-info').html();
        $(_e).closest('.form-group').append(tpl);
        //产生随机数code，低频操作，重复概率很小
        /*
        var _new_svr_code=Math.floor(Math.random()*100000);
        var _mix_up=(new Date()).getSeconds();
        _new_svr_code=_new_svr_code.toString()+_mix_up.toString();
        $(this).closest('.form-group').find("input[item_key=survey_code]").last().val(_new_svr_code);
        */
    }
    function btn_remove_item(_e) {
        $(_e).closest('.voter-limit-item').remove();
    }
</script>
