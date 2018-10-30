<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .account-basic dd p {
       margin-bottom: 1px!important;
    }
</style>
<div class="basic-info container">
    <div class="ibox-title" style="background-color: #DDD">
        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Staff Information</h5>
    </div>
    <div class="content" style="padding-bottom: 0px">
        <div class="col-sm-6 mincontent">
            <p class="idnum" style="margin-top: 8px">
                <span class="show pull-left base-name marginright3  redstar">Mobile-Phone</span>:
                <span class="marginleft10  redstar"><?php echo $staff_info['mobile_phone']; ?></span>
            </p>
            <p class="idnum" style="margin-top: 15px">
                <span class="show pull-left base-name marginright3">Identity Number</span>:
                <span class="marginleft10  redstar"><?php echo $staff_info['id_number']; ?></span>
            </p>

            <p class="idnum" style="margin-top: 15px">
                <span class="show pull-left base-name marginright3">Status</span>:
                <span class="marginleft10 redstar"><?php echo $lang['staff_status_' . $staff_info['staff_status']]; ?></span>
            </p>
        </div>
        <div class="col-sm-6 mincontent">
            <dl class="account-basic clearfix">
                <dt class="pull-left">
                <p class="account-head">
                    <img id="member-icon" src="<?php echo getImageUrl($staff_info['staff_icon'], null, null); ?>" class="avatar-lg">
                </p>
                </dt>
                <dd class="pull-left margin-large-left">
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">CID</span>:
                        <span class="marginleft10" id="login-account"><?php echo $staff_info['obj_guid']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Display Name</span>:
                        <span class="marginleft10" id="khmer-name"><?php echo $staff_info['display_name']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Branch/ Department</span>:
                        <span class="marginleft10" id="english-name"><?php echo $staff_info['branch_name'] . '/ ' . $staff_info['depart_name'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Um Account</span>:
                        <span class="marginleft10" id="member-grade"><?php echo $staff_info['um_account']; ?></span>
                    </p>
                </dd>
            </dl>
        </div>
    </div>
</div>