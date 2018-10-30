<html>
<head>
    <title><?php echo $output['html_title'] ?: "Test Page"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/themes/metro/easyui.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/main-style.css?v=17" rel="stylesheet" type="text/css"/>

    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/js/bootstrap.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.json.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/jquery.easyui.min.js"></script>
</head>
<style>
    html {
        font-size: 21.33333333px !important;
    }

    p {
        margin-bottom: 4px!important;
    }

    .home-header {
        height: 2rem;
        background-color: #66165d;
    }
    .home-header h2 {
        margin-top: 0;
        line-height: 2rem;
        text-align: center;
        color: #FFF;
        font-size: 22px;
    }

    .wrap {
        margin: 0;
        border: 0;
        padding: 0;
        font-style: normal;
    }

    .verify-wrapper {
        padding: .3rem .3rem;
    }

    .vote {
        padding: 0rem .3rem 1rem;
    }

    .verify-wrapper .member-info {
        background: #e8d6ba;
        padding: .5rem .8rem;
        border-radius: 10px 10px 0 0;
        display: flex;
    }

    .verify-wrapper .member-info .avatar {
        width: 3rem;
        height: 3rem;
        flex: 0 0 3rem;
    }

    .verify-wrapper .member-info .main {
        padding-left: 1rem;
        flex: 1;
    }

    .verify-wrapper .member-info .phone {
        color: #cc0000;
    }

    .verify-wrapper ul,.vote ul {
        padding-left: 0!important;
        background-color: #FFF;
    }

    .credit-info li {
        /*height: 1.8rem;*/
        /*line-height: 1.8rem;*/
        border-bottom: 1px solid #DDD;

    }

    .credit-info li {
        border-bottom: 1px solid #DDD;
        padding: .3rem 0;
    }

    .credit-info li:after {
        display:block;
        clear:both;
        content:"";
        visibility:hidden;
        height:0;
    }

    .vote ul li {
        border-bottom: 1px solid #DDD;
        padding: .3rem 0;
    }

    .vote ul li:after {
        display:block;
        clear:both;
        content:"";
        visibility:hidden;
        height:0;
    }

    .h30 {
        height: 30px;
        width: 100%;
    }

    .credit-info .title {
        width: 65%;
        float: left;
        text-align: left;
        padding-left: .6rem;
    }

    .credit-info .value {
        width: 35%;
        float: right;
        text-align: right;
        padding-right: .6rem;
    }

    .vote .title {
        width: 40%;
        height: 30px;
        line-height: 30px;
        float: left;
        text-align: left;
        padding-left: .6rem;
        font-weight: 700;
    }

    .vote .value {
        width: 60%;
        float: right;
        padding-right: .6rem;
    }

    .vote .value {
       margin-top: 5px;
    }

    em {
        font-weight: 700;
    }

</style>
<body>
    <header class="home-header">
        <h2 class="title"><?php echo $output['html_title']; ?></h2>
    </header>
    <?php $client_info = $output['client_info'];?>
    <?php $credit_suggest = $output['credit_suggest'];?>
    <div class="wrap">
        <div class="verify-wrapper">
            <div class="member-info" id="memberInfo">
                <img src="<?php echo getImageUrl($client_info['member_icon'])?>" class="avatar">
                <div class="main">
                    <p class="name"><?php echo $client_info['display_name'] . '(' . $client_info['obj_guid'] . ')'?></p>
                    <p class="code"><?php echo $client_info['login_code']?></p>
                    <p class="phone"><?php echo $client_info['phone_id']?></p>
                </div>
            </div>
            <ul class="credit-info">
                <li>
                   <div class="title">Default Credit</div>
                   <div class="value"><em><?php echo ncPriceFormat($credit_suggest['default_credit'])?></em></div>
                </li>
                <li>
                    <div class="title">Max Credit</div>
                    <div class="value"><em><?php echo ncPriceFormat($credit_suggest['max_credit'])?></em></div>
                </li>
                <li>
                    <div class="title">Invalid Terms</div>
                    <div class="value"><em><?php echo $credit_suggest['credit_terms'] . 'Months'?></em></div>
                </li>
<!--                <li>-->
<!--                    <div class="title">Interest rate without mortgage</div>-->
<!--                    <div class="value"><em>--><?php //echo $credit_suggest['interest_with_mortgage'] . '%'?><!--</em></div>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <div class="title">Interest rate with mortgage</div>-->
<!--                    <div class="value"><em>--><?php //echo $credit_suggest['interest_without_mortgage'] . '%'?><!--</em></div>-->
<!--                </li>-->
                <li>
                    <div class="title">Interest Package</div>
                    <div class="value"><?php echo $output['package']['package']?></div>
                </li>
                <li>
                    <div class="title" style="width: 40%">Remark</div>
                    <div class="value" style="width: 60%"><?php echo $credit_suggest['remark']?></div>
                </li>
            </ul>
        </div>

        <div class="vote">
            <form class="form-horizontal" method="post" action="<?php echo getUrl('committee_vote', 'submitVoteCreditApplication', array(), false, BACK_OFFICE_SITE_URL)?>">
                <input type="hidden" name="grant_id" value="<?php echo $credit_suggest['uid'] ?>">
                <ul>
                    <li>
                        <div class="title">Vote</div>
                        <div class="value">
                            <label><input type="radio" name="vote_state" value="<?php echo commonApproveStateEnum::PASS; ?>">Agree</label>
                            <label style="margin-left: 10px"><input type="radio" name="vote_state" value="<?php echo commonApproveStateEnum::REJECT; ?>">Reject</label>
                        </div>
                    </li>
                    <li>
                        <div class="title">Account</div>
                        <div class="value">
                            <input class="h30 form-control" type="text" name="account">
                        </div>
                    </li>
                    <li>
                        <div class="title">Password</div>
                        <div class="value">
                            <input class="h30 form-control" type="password" name="password">
                        </div>
                    </li>
                    <li>
                        <div style="text-align: center;padding: 0 .6rem">
                            <a class="btn btn-danger" style="width: 100%; border-radius: 0">Submit</a>
                        </div>
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <script>
        $('.btn-danger').click(function () {
            if($('input[name="vote_state"]:checked').length == 0){
                alert('Please select vote opinion!');
                return;
            }

            if(!$('input[name="account"]').val()){
                alert('Please input account!');
                return;
            }

            if(!$('input[name="password"]').val()){
                alert('Please input password!');
                return;
            }

            $('.form-horizontal').submit();
        })
    </script>
</body>
</html>

