<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>CIV Ext.Trade Type</h3>
          <ul class="tab-base">
            <li><a href="<?php echo getUrl('treasure', 'settingCIVExtraType', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
            <li><a class="current"><span>Edit</span></a></li>
          </ul>
      </div>
  </div>
    <div class="container" style="width: 600px;">
    <form class="form-horizontal" method="post">
        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" name="uid" value="<?php echo $output['info']['uid']?>">
        <div class="form-group">
            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Trade Type' ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="trade_type" placeholder="" value="<?php echo $output['info']['trade_type']?>">
                <div class="error_msg"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Ext Type' ?></label>
            <div class="col-sm-8">
                <label class="radio-inline"><input type="radio" name="ext_type" value="<?php echo flagTypeEnum::INCOME?>" <?php echo $output['info']['ext_type'] == flagTypeEnum::INCOME ? 'checked' : ''?>>Cash In</label>
                <label class="radio-inline"><input type="radio" name="ext_type" value="<?php echo flagTypeEnum::PAYOUT?>" <?php echo $output['info']['ext_type'] == flagTypeEnum::PAYOUT ? 'checked' : ''?>>Cash Out</label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 20px">
                <button type="button" class="btn btn-danger" id="btn_save"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
            </div>
        </div>
    </form>
  </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $('#btn_save').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            trade_type: {
                required: true
            },
            ext_type: {
                required: true
            }
        },
        messages: {
            trade_type: {
                required: '<?php echo 'Required'?>'
            },
            ext_type: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>
