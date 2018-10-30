<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .account-basic dd p {
        margin-bottom: 1px !important;
    }

    .base-name {
        font-weight: 600;
    }
</style>
<?php $identity = memberIdentityClass::getIdInfoByMemberId($member_id)?>
<div class="basic-info container">
    <div class="ibox-title" style="background-color: #DDD">
        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Identity Information</h5>
    </div>
    <div class="content" style="padding-bottom: 0px">
        <div class="col-sm-6 mincontent">
            <p class="text-small">
                <span class="show pull-left base-name marginright3">Id Sn</span>:
                <span class="marginleft10" id="login-account"><?php echo $identity['id_sn']?></span>
            </p>
            <p class="text-small">
                <span class="show pull-left base-name marginright3">Nationality</span>:
                <span class="marginleft10" id="khmer-name"><?php echo ($identity['nationality'])?></span>
            </p>
            <p class="text-small">
                <span class="show pull-left base-name marginright3">Expire Date</span>:
                <span class="marginleft10" id="english-name"><?php echo $identity['id_expire_time']?></span>
            </p>
            <p class="text-small">
                <span class="show pull-left base-name marginright3">Address</span>:
                <span class="marginleft10" id="member-grade"><?php echo $identity['address_detail']?></span>
            </p>
        </div>
        <div class="col-sm-6 mincontent">
            <dl class="account-basic clearfix">
                <dt class="pull-left">
                    <p class="account-head" style="padding-top: 3px">
                        <img id="member-icon" src="<?php echo getImageUrl($identity['member_icon'], imageThumbVersion::AVATAR)?>" class="avatar-lg">
                    </p>
                </dt>
                <dd class="pull-left margin-large-left">
                    <p class="idnum">
                        <span class="show pull-left base-name marginright3 redstar">Khmer Name</span>:
                        <span class="marginleft10 redstar"><?php echo $identity['id_kh_name']?></span>
                    </p>
                    <p class="idnum" style="margin-top: 15px">
                        <span class="show pull-left base-name marginright3">English Name</span>:
                        <span class="marginleft10 redstar"><?php echo $identity['id_en_name']?></span>
                    </p>
                    <p class="idnum" style="margin-top: 15px">
                        <span class="show pull-left base-name marginright3">Mobile-Phone</span>:
                        <span class="marginleft10 redstar"><?php echo $identity['phone_id']?></span>
                    </p>
                </dd>
            </dl>
        </div>
    </div>
</div>