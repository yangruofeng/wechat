<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .account-basic dd p {
       margin-bottom: 1px!important;
    }
</style>
<?php
$memberStateLang = enum_langClass::getMemberStateLang();
?>
<div class="basic-info container">
    <div class="ibox-title" style="background-color: #DDD">
        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Member Information</h5>
    </div>
    <div class="content" style="padding-bottom: 0px">
        <div class="col-sm-6 mincontent">
            <p class="idnum" style="margin-top: 8px">
                <span class="show pull-left base-name marginright3  redstar">Mobile-Phone</span>:
                <span class="marginleft10  redstar"><?php echo '(+'.$client_info['phone_country'].')'.$client_info['phone_number']?></span>
            </p>
            <p class="idnum" style="margin-top: 15px">
                <span class="show pull-left base-name marginright3"> Identity Number</span>:
                <span class="marginleft10  redstar"><?php echo $client_info['id_sn']?></span>
            </p>

            <p class="idnum" style="margin-top: 15px">
                <span class="show pull-left base-name marginright3"> Member States</span>:
                <span class="marginleft10 redstar"><?php echo $memberStateLang[$client_info['member_state']]?></span>
            </p>
        </div>
        <div class="col-sm-6 mincontent">
            <dl class="account-basic clearfix">
                <dt class="pull-left">
                <p class="account-head">
                    <img id="member-icon" src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR)?>" class="avatar-lg">
                </p>
                </dt>
                <dd class="pull-left margin-large-left">
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
                        <span class="marginleft10" id="member-grade"><?php echo $client_info['grade_code']?></span>
                    </p>
                </dd>
            </dl>
        </div>
    </div>
</div>