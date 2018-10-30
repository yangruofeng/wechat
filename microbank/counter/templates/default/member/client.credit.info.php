<div>
    <?php $detail = $data['detail'];$client_authorized_history = $data['client_authorized_history'];?>
    <?php $verify_field = enum_langClass::getCertificationTypeEnumLang();?>
    <input type="hidden" name="max_credit" id="max_credit" value="<?php echo $detail['max_credit'];?>">
    <?php if($detail){ ?>
        <div class="record-info">
            <table class="table table-condensed record-base-table">
                <tr class="warning">
                    <td><label class="control-label">Credit</label></td>
                    <td><?php echo ncPriceFormat($detail['credit']);?></td>
                </tr>
                <?php if(count($detail['assets']) > 0){ ?>
                    <?php foreach ($detail['assets'] as $k => $v) { ?>
                        <tr class="warning">
                            <td>
                                <label class="control-label child-label">
                                    <?php echo $verify_field[$v['asset_type']]?>
                                </label>
                            </td>
                            <td><?php echo ncPriceFormat($v['credit']);?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <td><label class="control-label">Max Credit</label></td>
                    <td><?php echo ncPriceFormat($detail['max_credit']);?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Grant Time</label></td>
                    <td><?php echo timeFormat($detail['grant_time']);?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Authorized Time</label></td>
                    <td>
                        <?php echo $detail['contract'] ? timeFormat($detail['contract']['create_time']) : 'Not Yet';?>
                        <input type="hidden" id="is_contract" value="<?php echo $detail['contract'] ? 1 : 0;?>">
                        <input type="hidden" id="credit" value="<?php echo $detail['credit'];?>">
                    </td>
                </tr>
                <tr class="info">
                    <td><label class="control-label">Authorized Contract</label></td>
                    <td>
                        <div class="custom-btn-group">
                                    <a title="" class="custom-btn custom-btn-secondary quick-detail <?php if(!$detail['contract']){ echo 'disabled';} ?>" href="javascript:;" <?php if($detail['contract']){ ?>onclick="showDetail(<?php echo $detail['contract']['uid'];?>);"<?php } ?>>
                                <span><i class="fa fa-vcard-o"></i>Detail</span>
                            </a>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="panel-tab custom-panel-tab">
                <ul class="nav nav-tabs record-tabs" role="tablist">
                    <li role="presentation" class="authorize-li active">
                        <a href="#tab_authorize" aria-controls="tab_authorize" role="tab" data-toggle="tab"><?php echo 'Authorize';?></a>
                    </li>
                    <li role="presentation" class="history-li">
                        <a href="#tab_history" aria-controls="tab_history" role="tab" data-toggle="tab"><?php echo 'History';?></a>
                    </li>
                    <li role="presentation" class="history-li">
                        <a href="#tab_takeout" aria-controls="tab_takeout" role="tab" data-toggle="tab"><?php echo 'Mortgage TakeOut';?></a>
                    </li>
                    <li role="presentation" class="tab-detail-li" style="display: none;">
                        <a href="#tab_detail" aria-controls="tab_detail" role="tab" data-toggle="tab"><?php echo 'Authorized Contract Detail';?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab_authorize">
                        <div class="authorize-form clearfix">
                            <form action="<?php echo getUrl('member', 'submitClientAuthorize', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post" id="authorizeForm">
                                <input type="hidden" name="form_submit" value="ok">
                                <input type="hidden" name="grant_id" id="grant_id" value="<?php echo $detail['uid'];?>">
                                <input type="hidden" name="member_id" id="member_id" value="<?php echo $detail['member_id'];?>">
                                <input type="hidden" name="member_image" id="member_image" value="">
                                <input type="hidden" name="mortgage_list" id="mortgage_list" value="">
                                <input type="hidden" name="contract_images" id="contract_images" value="">
                                <input type="hidden" name="country_code" id="country_code" value="<?php echo $detail['country_code'];?>">
                                <input type="hidden" name="phone" id="phone" value="<?php echo $detail['phone'];?>">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Scene Photo</label>
                                    <div class="col-sm-4">
                                        <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                                            <img id="img_slave" src="resource/img/member/photo.png">
                                        </div>
                                    </div>
                                </div>
                                <?php if(count($detail['assets']) > 0 || count($detail['is_assets']) > 0){ ?>
                                    <div class="col-sm-12 form-group">
                                        <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Mortgage List</label>
                                        <div class="col-sm-10">
                                            <?php if(count($detail['is_assets']) > 0){ ?>
                                                <table class="table table-bordered asset-info-wrap" style="margin-bottom:10px;">
                                                    <thead>
                                                        <tr class="table-header">
                                                            <td>Type</td>
                                                            <td>Credit</td>
                                                            <td>File Type</td>
                                                            <td>Time</td>
                                                            <td>State</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-body">
                                                    <?php foreach ($detail['is_assets'] as $k => $v) {?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $verify_field[$v['asset_type']] ?>
                                                                <em class="n"><?php echo $v['asset_name'] ?></em>
                                                            </td>
                                                            <td><?php echo ncPriceFormat($v['credit']);?></td>
                                                            <td><?php echo $v['mortgage_file_type'] == 1?'Soft':'Hard';?></td>
                                                            <td><?php echo timeFormat($v['operator_time']);?></td>
                                                            <td><?php echo $v['is_mortgage'] ? '【Mortgaged】' : '【Not Mortgaged】';?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            <?php } ?>
                                           <?php foreach ($detail['assets'] as $k => $v) { ?>
                                                <div class="asset-info-wrap clearfix">
                                                    <div class="col-sm-2 form-group left">
                                                        <label class="col-sm-12 control-label mortgage-label">
                                                            <div class="ck_wrap">
                                                                <p>
                                                                    <?php echo $verify_field[$v['asset_type']] ?>
                                                                    <em class="n"><?php echo $v['asset_name'] ?></em>
                                                                </p>
                                                                <p>Credit: <span class=""><?php echo ncPriceFormat($v['credit']);?></span></p>
                                                                <input type="checkbox" name="ck_mortgage" uid="<?php echo $v['member_asset_id'];?>" val="<?php echo $v['credit'];?>" /> 
                                                                <span class="c-asset-state"><?php echo 'Mortgage';?></span>
                                                            </div>
                                                            <div class="mortgage-type" style="display: none;">
                                                                <label class="control-label">File Type</label>
                                                                <div class="radio" style="margin-top:0;margin-bottom: 0;">
                                                                    <label>
                                                                        <input type="radio" name="mortgage_file_type_<?php echo $v['uid'];?>" value="<?php echo assetsCertTypeEnum::SOFT;?>" checked>
                                                                        Soft
                                                                    </label>
                                                                    <label>
                                                                        <input type="radio" name="mortgage_file_type_<?php echo $v['uid'];?>"  value="<?php echo assetsCertTypeEnum::HARD;?>">
                                                                        Hard
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-10 form-group right">
                                                            <div class="image-list asset-image-list-<?php echo $v['uid'];?> clearfix">
                                                                <?php if($v['is_mortgage']){ ?> 
                                                                    <?php foreach ($v['mortgage_images'] as $k => $v) { ?>
                                                                        <div class="image-item"><img class="img asset-img" src="<?php echo getImageUrl($v);?>"></div>
                                                                    <?php }?>
                                                                <?php } ?> 
                                                                <div class="image-item snapshot_div" onclick="<?php if(!$v['is_mortgage']){ ?>callWin_snapshot_master('asset-image-list-<?php echo $v['uid'];?>','mortgage_list');<?php }?>"  >
                                                                    <img class="asset-img" src="resource/img/member/photo.png">
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-sm-6 form-group">
                                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Activate Credit</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="amount" value="<?php echo $detail['contract'] ? 0 : $detail['credit'];?>" readonly>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Loan Fee</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="loan_fee" id="loan_fee" value="0" readonly>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Trading Password</label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="trading_password" id="trading_password" value="" >
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-12 control-label"><span class="required-options-xing">*</span>Contract Image</label>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <div class="col-sm-12">
                                        <div class="image-list contract-image-list clearfix">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_master('contract-image-list','contract_images');">
                                                <img src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <div class="operation">
                                        <a class="btn btn-primary" onclick="submitAuthorize();">Submit</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_history">
                        <?php if(count($client_authorized_history) > 0){?>
                            <table class="table table-bordered authorized-history">
                                <thead>
                                <tr class="table-header">
                                    <td>Scene Photo</td>
                                    <td>Mortgages</td>
                                    <td>Loan Fee</td>
                                    <td>Authorized Type</td>
                                    <td>Authorized Time</td>
                                    <td>Function</td>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                    <?php foreach ($client_authorized_history as $k => $v) { ?>
                                        <tr>
                                            <td>
                                                <img class="scene" src="<?php echo getImageUrl($v['member_img']);?>" alt="">
                                            </td>
                                            <td>
                                                <?php foreach ($v['mortgages'] as $mk => $mv) {
                                                    $temp = $verify_field[$mv];
                                                    $str = $str ? $str . ',' . $temp : $str . $temp;
                                                }
                                                echo $str ?: 'Not Mortgaged';
                                                ?> 
                                            </td>
                                            <td>
                                                <?php echo $v['fee']; ?>
                                            </td>
                                            <td>
                                                <?php echo $v['mortgage_type']==1?'Mortgaged':'Redeem'; ?>
                                            </td>
                                            <td>
                                                <?php echo timeFormat($v['create_time']); ?>
                                            </td>
                                            <td>
                                                <div class="custom-btn-group">
                                                    <a title="" class="custom-btn custom-btn-secondary" href="javascript:;" onclick="showDetail(<?php echo $v['uid'];?>);">
                                                        <span><i class="fa fa-vcard-o"></i>Detail</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php }else{?>
                                <div class="no-record">No history</div>
                        <?php }?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_takeout">
                        <div class="takeout-wrap"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane tab-detail-pane" id="tab_detail" style="display: none;">
                        <div class="contract-detail"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div class="no-record">No Credit Record</div>
    <?php } ?>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    function callWin_snapshot_slave() {
        /*var img = 'avator/05747074622691598.jpg';
         $("#img_slave").attr("src", getUPyunImgUrl(img, "180x120"));
         $('#member_image').val(img);
         return*/
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }

    function callWin_snapshot_master(el, input){
        /*var img = 'avator/05747074622691598.jpg';
         var item = '<div class="image-item"><img class="img" src="'+getUPyunImgUrl(img)+'"><input type="hidden" class="i_url" value="'+img+'"></div>';
         $('.'+el).prepend(item);
         if(input == 'contract_images'){
         var imgs = $('#'+input).val();
         imgs = imgs ? imgs+','+img : img;
         $('#'+input).val(imgs);
         }else{
         calculationAssetsList();
         }
         return*/
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var item = '<div class="image-item1"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="i_url" value="'+_img_path+'"></div>';
                    $('.' + el).prepend(item);
                    if(input == 'contract_images'){
                        var imgs = $('#'+input).val();
                        imgs = imgs ? imgs +',' + _img_path : _img_path;
                        $('#'+input).val(imgs);
                    }else{
                        calculationAssetsList();
                    }

                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }

</script>
<script>
    // 已经弃用了，如果要重新使用此模板需要修改loan fee的计算规则 allics
    var rate = '<?php echo json_encode(global_settingClass::memberAuthorizedContractFeeRate());?>', max_credit = $('#max_credit').val();
    rate = eval('('+rate+')');
    showTakeOutPage();

    function calculation() {
        var checks = document.querySelectorAll('input[name=ck_mortgage]');
        var amount = 0;
        for(var i =0;i<checks.length;i++){
            if(checks[i].checked && !checks[i].disabled){
                amount = FloatAdd(amount, $(checks[i]).attr('val'));
            }
        }
        return amount;
    }
    var checks = document.querySelectorAll('input[name=ck_mortgage]');
    var credit = $('#credit').val(), amount = 0;
    var is_contract = $('#is_contract').val();

    var fee = 0;
    var rate_type = rate.follow_sign_contract_rate_type;
    var rate_value = rate.follow_sign_contract_rate_value;
    var min_rate_value = rate.min_follow_sign_contract_fee;
    if(is_contract == 0){
        amount = credit;
        rate_type = rate.first_sign_contract_rate_type;
        rate_value = rate.first_sign_contract_rate_value;
        min_rate_value = rate.min_first_sign_contract_fee;
    }
    amount = amount > max_credit ? max_credit : amount;
    if(rate_type == 1){
        fee = rate_value;
    }else{
        fee = FloatMul(amount, rate_value);
    }
    fee = fee < min_rate_value ? min_rate_value : fee;
    $('#loan_fee').val(amount==0?0:fee);
    
    for (var j = 0;j<checks.length;j++){
        checks[j].onclick = function () {
            var type_wrap = $(this).parents('.mortgage-label').find('.mortgage-type');
            $(this).prop('checked') ? type_wrap.show() : type_wrap.hide();
            var amount = calculation();
            if(is_contract == 0){
                amount = FloatAdd(amount, credit);
                amount = amount > max_credit ? max_credit : amount;
                fee = FloatMul(amount, rate_value/100);
            }
            amount = amount > max_credit ? max_credit : amount;
            if(rate_type == 1){
                fee = rate_value;
            }else{
                fee = FloatMul(amount, rate_value/100);
            }
            fee = fee < min_rate_value ? min_rate_value : fee;
            $('#amount').val(amount);
            $('#loan_fee').val(amount==0?0:fee);
            calculationAssetsList();
        }
    }
    $('input[name^="mortgage_file_type_"]').change(function(){
        calculationAssetsList();
    });

    function calculationAssetsList(){
        var checks = document.querySelectorAll('input[name=ck_mortgage]');
        var assets = [];
        for(var i = 0;i<checks.length;i++){
            if(checks[i].checked && !checks[i].disabled){
                var member_asset_id = $(checks[i]).attr('uid');
                var mortgage_file_type = $(checks[i]).parents('.mortgage-label').find('input[type=radio]:checked').val();
                var imgs = $(checks[i]).parents('.asset-info-wrap').find('.i_url');
                var item = {};
                var imgs_arr = [];
                for(var j = 0;j<imgs.length;j++){
                    var m = $(imgs[j]).val();
                    imgs_arr.push(m);
                }
                item.member_asset_id = member_asset_id;
                item.asset_images = imgs_arr;
                item.mortgage_file_type = mortgage_file_type;
                assets.push(item);
            }
        }
        assets = encodeURI(JSON.stringify(assets));
        $('#mortgage_list').val(assets);
    }

    $('.authorize-li,.history-li').click(function(){
        $('.tab-detail-pane, .tab-detail-li').hide();
    });

     function submitAuthorize(){
         var mortgage_list = $('#mortgage_list').val(), is_contract = $('#is_contract').val(), 
             trading_password = $('#trading_password').val();
         if(is_contract!=0 && !mortgage_list){
            alert('Please select mortgage');
             return false;
         }
         if(!trading_password){
            alert('Please input trading password');
             return false;
         }
        $('#authorizeForm').submit();
    }

    function showDetail(uid){
        $('.tab-detail-pane, .tab-detail-li').show();
        $('.record-tabs > li, .tab-content > div').removeClass('active');
        $('.tab-detail-li, .tab-detail-pane').addClass('active');
        yo.dynamicTpl({
            tpl: "member/authorize.contract.detail",
            control:'counter_base',
            dynamic: {
                api: "member",
                method: "getAuthorizeContractDetail",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".contract-detail").html(_tpl);
            }
        });
    }

    function showTakeOutPage(){
        var member_id = $('#member_id').val();
        yo.dynamicTpl({
            tpl: "member/authorize.mortgage.takeout",
            control:'counter_base',
            dynamic: {
                api: "member",
                method: "showAuthorizeMortgageTakeout",
                param: {member_id: member_id, grant_id: $('#grant_id').val(), country_code: $('#country_code').val(), phone: $('#phone').val()}
            },
            callback: function (_tpl) {
                $(".takeout-wrap").html(_tpl);
            }
        });
    }
   
    //浮点数加法运算
    function FloatAdd(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    }
    //浮点数乘法运算
    function FloatMul(arg1,arg2){
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
    }
</script>