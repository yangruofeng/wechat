<style>
    #top-counter .balance tr td span{
        font-weight: 100;
    }
</style>
<div id="header">
    <div>
        <h1>SAMRITHISAK</h1>
    </div>
</div>
<div id="top-counter" style="top: 0px;">
    <div class="counter-icon">
        <img src="<?php echo getUserIcon($output['user_info']['user_icon'])?>">
    </div>
    <div class="counter-info">
        <div class="name">
            <?php echo $output['user_info']['user_code'] ."/". $output['user_info']['user_name'] ?>

            <?php if(!$output['trading_password']){ ?>
                &nbsp;<i id="set_trading_password_tip" title="Please Setting Trading Password" class="fa fa-warning" style="color: red"></i>
            <?php }?>
        </div>
        <div class="department"><?php echo $output['department_info']['branch_name'] . '  ' . $output['department_info']['depart_name'];?></div>
    </div>
    <?php if($output['user_info']['user_position']!=userPositionEnum::CUSTOMER_SERVICE){?>
        <div class="balance">
            <table class="table">
                <tbody class="table-body">
                <tr class="cash_in_hand">
                    <td><label class="control-label">
                            <?php if($output['user_info']['user_position']==userPositionEnum::TELLER){?>
                                Cash On Hand
                            <?php }else{?>
                                Cash In Vault
                            <?php }?>
                        </label></td>
                    <?php foreach ($output['currency'] as $key => $currency) { ?>
                        <td>
                            <span style="font-size: 1rem"><?php echo $currency;?>: </span>
                            <span class="cash-in-hand" currency="cash_<?php echo $key?>" style="font-weight: bold"></span>
                        </td>
                    <?php }?>
                    <td>
                        <div style="margin-left: 30px;width: 120px;float: left">
                            Sell-Price :
                        </div>
                        <div style="float: left">
                            1 USD = <?php echo ncPriceFormat($output['exchange_rate'] ? $output['exchange_rate']['buy_rate'] : $output['exchange_rate_1']['sell_rate_unit']) ?> KHR
                        </div>
                    </td>

                </tr>
                <tr class="outstanding">
                    <td><label class="control-label">Outstanding</label></td>
                    <?php foreach ($output['currency'] as $key => $currency) { ?>
                        <td>
                            <span  style="font-size:1rem"><?php echo $currency;?>: </span>
                            <span class="cash-outstanding" currency="out_<?php echo $key?>" style="font-weight: bold"></span>
                        </td>
                    <?php }?>
                    <td>
                        <div style="margin-left: 32px;float: left;width: 120px;height: 1px">
                            Buy-Price :
                        </div>
                        <div style="float: left">
                            1 USD = <?php echo ncPriceFormat($output['exchange_rate'] ? $output['exchange_rate']['sell_rate_unit'] : $output['exchange_rate_1']['buy_rate']) ?> KHR
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php }?>
    <div style="float: right;margin:15px 230px 0px 0px">Version : <span id="version"></span> </div>
</div>


<div id="tools" style="float: right;display: inline-block;position: absolute;right: 0px;top: 15px;z-index: 20" class="navbar">
    <ul class="list-inline">
        <li class="dropdown open" id="profile-messages" style="display: inline-block">
            <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle" style="padding: 20px;display: block;font-size: 16px" aria-expanded="true">
                <span class="text user_name"><i class="fa fa-cog"></i> Settings</span>&nbsp;
                <b class="caret" style="margin-left: 0"></b>
            </a>
            <ul class="dropdown-menu" style="left:auto;right: 0;">
                <li>
                    <a href="#" id="tool_fullscreen" data-sts="no" class="full_screen"><i class="fa fa-arrows-alt"></i>Full Screen</a>
                </li>
                <li title="Lock Screen">
                    <a href="#" id="tool_lockscreen" onclick="callWin_lock_screen()"><i class="fa fa-lock"></i>Lock Screen</a>
                </li>
                <li class="divider"></li>
                <li><a href="#" id="my_profile"><i class="fa fa-user"></i> My Profile</a></li>
                <li><a href="#" id="change_login_password"><i class="fa fa-pencil"></i> Change Login Password</a></li>
                <li><a href="#" id="set_trading_password"><i class="fa fa-pencil-square-o"></i> Setting Trading Password</a></li>
                <li><a href="#" id="reset_security_card"><i class="fa fa-shield"></i> Reset Security Card</a></li>
                <li><a onclick="clearCache()"><i class="fa fa-refresh"></i> Clear Cache</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo getUrl('login','loginOut', array(), false, ENTRY_COUNTER_SITE_URL)?>"><i class="fa fa-key"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</div>
<script>

    $(function () {
        if(window.external){
            try{
               var version = window.external.getCurrentClientVersion();
               $('#version').text(version);
            }catch (ex){
                alert(ex.Message);
            }
        }
    });

    var USER_ID = '<?php echo $output['user_info']['uid']; ?>';
    $(function () {
        $('#tool_fullscreen').on('click', function () {
            if($(this).data('sts')=='no'){
                callWin_set_fullscreen();
                $(this).data('sts','yes');
            }else{
                callWin_unset_fullscreen();
                $(this).data('sts','no');
            }

        });
        $('#tool_fullscreen').trigger('click');

        <?php if($output['user_info']['user_position']!=userPositionEnum::CUSTOMER_SERVICE){ ?>
            setInterval(getBalance, 2000);
        <?php }?>
    });

    function callWin_lock_screen(){
        if(window.external){
            try{
                window.external.lockScreen();
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function callWin_set_fullscreen(){
        if(window.external){
            try{
                if(window.external.setFullScreen){
                    window.external.setFullScreen();
                }
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function callWin_unset_fullscreen(){
        if(window.external){
            try{
                window.external.unsetFullScreen();
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function getBalance() {
        <?php if($output['user_info']['user_position']==userPositionEnum::TELLER){?>
            var _api_m="getTellerBalance";
        <?php }else{?>
            var _api_m="getBranchBalance";
        <?php }?>
        yo.loadData({
            _c: 'entry_index',
            _m: _api_m,
            param: '',
            callback: function (_o) {
                if (_o.STS) {

                    var data = _o.DATA;
                    $('.cash-in-hand').each(function () {
                        var currency = $(this).attr('currency');
//                        currency = 'cash_' + currency;
                        $(this).text(data[currency]);
                    });

                    $('.cash-outstanding').each(function () {
                        var currency = $(this).attr('currency');
//                        currency = 'out_' + currency;
                        $(this).text(data[currency]);
                    })
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function clearCache() {
        if(window.external){
            try{
                window.external.clearCache();
            }catch (ex){
                alert(ex.toString());
            }
        }
    }


</script>
