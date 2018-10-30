<div class="ibox-title">
    <h5><i class="fa fa-id-card-o"></i>Member Information</h5>
</div>
<?php if(!$client_info) $client_info=$output['client_info']?>
<div class="content">
    <div class="col-sm-7">
        <dl class="account-basic clearfix">
            <dt class="pull-left">
            <p class="account-head">
                <img id="member-icon" src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR)?>" class="avatar-lg">
            </p>
            </dt>
            <dd class="pull-left margin-large-left">
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Login Account</span>:
                    <span class="marginleft10" id="login-account"><?php echo $client_info["login_code"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Client CID</span>:
                    <span class="marginleft10" id="login-account"><?php echo $client_info["obj_guid"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                    <span class="marginleft10" id="khmer-name"><?php echo $client_info["kh_display_name"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">English Name</span>:
                    <span class="marginleft10" id="english-name"><?php echo $client_info['display_name']?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Member Grade</span>:
                    <span class="marginleft10" id="member-grade"><?php echo $client_info['grade_code']?:''?></span>
                </p>
            </dd>
        </dl>
    </div>
    <div class="col-sm-5">
        <dl class="account-basic clearfix">
            <dt class="pull-left">
            </dt>
            <dd class="pull-left margin-large-left">
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Phone</span>:
                    <span class="marginleft10" id="login-account"><?php echo $client_info["phone_id"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">ID</span>:
                    <span class="marginleft10" id="khmer-name"><?php echo $client_info["id_sn"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Email</span>:
                    <span class="marginleft10" id="khmer-name"><?php echo $client_info["email"]?></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Create Time</span>:
                    <span class="marginleft10" id="english-name"><?php echo $client_info['create_time'] ?></span>
                </p>
            </dd>
        </dl>
    </div>
</div>