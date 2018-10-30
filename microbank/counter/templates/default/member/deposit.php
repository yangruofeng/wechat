<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
    }

    .account-basic{
        margin-bottom: 0;
    }

    .text-small {
        margin-bottom: 0;
    }

    .basic-info {
        margin-bottom: 20px;
    }
    .limitbox td{
        background-color: white;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6 mincontent">
                        <div class="input-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                    <option value="855" <?php echo $client_info['phone_country'] == 855 ? 'selected' : ''?>>+855</option>
                                    <option value="66" <?php echo $client_info['phone_country'] == 66 ? 'selected' : ''?>>+66</option>
                                    <option value="86" <?php echo $client_info['phone_country'] == 86 ? 'selected' : ''?>>+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $client_info['phone_number'];?>" placeholder="">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                            </span>
                        </div>
                        <div class="search-other">
                            <img src="resource/img/member/phone.png">
                            <img src="resource/img/member/qr-code.png">
                            <img src="resource/img/member/bank-card.png">
                        </div>
                    </div>
                    <div class="col-sm-6 mincontent">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                            <p class="account-head">
                                <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                            </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
                                <input type="hidden" id="client_id" name="client_id" value="<?php echo intval($client_info['uid'])?>">
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Login Account</span>:
                                    <span class="marginleft10" id="login-account"><?php echo $client_info['login_code']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                    <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">English Name</span>:
                                    <span class="marginleft10" id="english-name"><?php echo $client_info['display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Member Grade</span>:
                                    <span class="marginleft10" id="member-grade"><?php echo $client_info['member_grade']?></span>
                                </p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Deposit</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6 mincontent">
                        <form id='client_deposit'>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="currency" onchange="showLimit()">
                                        <?php $ccy_list=(new currencyEnum())->Dictionary();
                                            foreach($ccy_list as $k=>$v){?>
                                                <option value="<?php echo $v;?>"><?php echo $k;?></option>
                                            <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="amount" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 20px">
                                    <button type="button" class="btn btn-primary btn-block" onclick="lockAmount()">NEXT</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-6 mincontent">
                        <?php $i = 0 ;foreach($ccy_list as $key => $currency){ ++$i; ?>
                            <table class="table table-bordered limitbox" id="limit_<?php echo $key?>" style="display: <?php echo $i == 1 ? '' : 'none'?>">
                                <tr>
                                    <td colspan="2" style="text-align: center;font-size: 15px;">
                                        <label><?php echo $key;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_branch_per_day'];?>
                                    </td>
                                    <td><?php echo $output['branch_limit'][$key];?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_per_member_per_day'];?>
                                    </td>
                                    <td class="member_per_day" currency="<?php echo $key?>"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_per_member_per_time'];?>
                                    </td>
                                    <td class="member_per_time" currency="<?php echo $key?>"></td>
                                </tr>
                            </table>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        search_click();
        btn_search_onclick();
        $('#btn_search').click(function () {
            search_click()
        })

    })

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientInfo',
            param: {
                country_code: country_code,
                phone: phone,
                limit_key: '<?php echo bizCodeEnum::MEMBER_DEPOSIT_BY_CASH;?>'
            },
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#english-name').html(data.display_name);
                    $('#member-grade').html(data.grade_code);

                    var per_day = data.member_limit.per_day;
                    var per_time = data.member_limit.per_time;
                    $('.member_per_day').each(function(){
                        var _currency = $(this).attr('currency');
                        $(this).text(per_day[_currency])
                    })
                    $('.member_per_time').each(function(){
                        var _currency = $(this).attr('currency');
                        $(this).text(per_time[_currency])
                    })
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
    
    function lockAmount() {
        var currency =  $('select[name=currency]').val();
        var amount = $('input[name=amount]').val();
        var values = $('#client_deposit').getValues();
        var client_id =  $("#client_id").val();
        values.client_id = client_id;
        if($('#client_id').val() != 0){
            if($('select[name=currency]').val() && $('input[name=amount]').val()){
                yo.loadData({
                    _c: 'member_cash',
                    _m: 'createClientDeposit',
                    param: values,
                    callback: function (_o) {
                        if (_o.STS) {
                            var _biz_id=_o.DATA;
                            window.location.href = "<?php echo getUrl('member_cash', 'depositCheck', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id="+_biz_id;
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }else {
                alert('Please Input Currency/Amount ')
            }
        }else {
            alert('Please Select Client');
        }
    }


    $('#phone').focus(function(){
        $('#phone').val('');
    });

    $('#phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
                search_click();
        }
    });

    //  展示成功deposit列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_cash/deposit.list",
            control:'counter_base',
            dynamic: {
                api: "member_cash",
                method: "getDepositList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }


    function showLimit() {
        var limit_currency = $('select[name="currency"]').val()
        $('.limitbox').hide();
        $('#limit_' + limit_currency).show();
    }

    
    function print_deposit(biz_id) {
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printDeposit', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id="+biz_id);
    }






</script>



