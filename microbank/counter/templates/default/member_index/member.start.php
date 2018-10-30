<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="container">
        <div class="row" style="max-width: 1300px">
            <div class="col-sm-12 col-md-10 col-lg-7">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Search Client</h5>
                    </div>
                    <div class="content" id="div_client_box" data-client_id="0">
                        <div class="col-sm-6">
                            <div class="input-group" style="width: 400px">
                                <span id="span_phone_country" class="input-group-addon" style="padding: 0;border: 0;">
                                    <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                        <option <?php if($client_info['phone_country'] == 855){ echo 'selected';}?> value="855">+855</option>
                                        <option <?php if($client_info['phone_country'] == 66){ echo 'selected';}?> value="66">+66</option>
                                        <option <?php if($client_info['phone_country'] == 86){ echo 'selected';}?> value="86">+86</option>
                                    </select>
                                </span>
                                <input type="text" class="form-control" id="txt_search_phone" name="s_phone" value="<?php echo $client_info['phone_number']; ?>" placeholder="">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" onclick="btnSearch_onclick()" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                </span>
                            </div>
                            <div class="btn-group" data-toggle="buttons" style="padding-top: 10px;width: 400px">
                                <label class="btn btn-default active">
                                    <i class="fa fa-phone"></i>
                                    <input type="radio" onchange="btn_search_by_onclick(this)" value="1" name="rbn_search_by" id="rbn_option1" autocomplete="off" checked>Phone
                                </label>
                                <label class="btn btn-default">
                                    <i class="fa fa-id-card"></i>
                                    <input type="radio"  onchange="btn_search_by_onclick(this)" value="2" name="rbn_search_by" id="rbn_option2" autocomplete="off"> CID
                                </label>
                                <label class="btn btn-default">
                                    <i class="fa fa-at"></i>
                                    <input type="radio"  onchange="btn_search_by_onclick(this)" value="3" name="rbn_search_by" id="rbn_option3" autocomplete="off"> LoginAccount
                                </label>
                                <label class="btn btn-default">
                                    <i class="fa fa-at"></i>
                                    <input type="radio"  onchange="btn_search_by_onclick(this)" value="4" name="rbn_search_by" id="rbn_option4" autocomplete="off"> Name
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="max-width: 1300px;padding-top: 50px">
            <div class="panel-tab custom-panel-tab">
                <ul class="nav nav-tabs record-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab_pending_sign" aria-controls="tab_pending_sign" role="tab" data-toggle="tab"><?php echo 'Pending Sign Agreement';?></a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#tab_pending_disburse" aria-controls="tab_pending_disburse" role="tab" data-toggle="tab"><?php echo 'Pending Disburse';?></a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#tab_pending_repay" aria-controls="tab_pending_repay" role="tab" data-toggle="tab"><?php echo 'Pending Repayment';?></a>
                    </li>
                </ul>
                <div class="tab-content" style="background-color: #FFFFFF">
                    <div role="tabpanel" class="tab-pane active" id="tab_pending_sign">
                        <?php include(template("member_index/start.pending.sign"))?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_pending_disburse">
                        <?php include(template("member_index/start.pending.disburse"))?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_pending_repay">
                        <?php include(template("member_index/start.pending.repay"))?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(window).ready(function(){
        $("#txt_search_phone").focus();
    });
    function btn_search_by_onclick(_e){
        var _item=$(_e).val();
        if(_item=='1'){
            $("#span_phone_country").show();
        }else{
            $("#span_phone_country").hide();
        }

    }
    $('#txt_search_phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
            btnSearch_onclick();
        }
    });
    function btnSearch_onclick() {
        var _country_code = $('select[name="country_code"]').val();
        var _phone = $('#txt_search_phone').val();
        var _search_by=$('input:radio[name="rbn_search_by"]:checked').val();


        if (!$.trim(_phone)) {
            return;
        }
        var _url="<?php echo ENTRY_COUNTER_SITE_URL?>/index.php";
        _url=_url+"?&act=member_index&op=index&country_code="+_country_code+"&phone_number="+_phone+"&search_by="+_search_by;
        document.location.href = _url;
    }

</script>