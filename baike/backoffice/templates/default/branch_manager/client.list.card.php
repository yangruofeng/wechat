<div>
    <?php foreach($data['data'] as $row){ $credit_info = memberClass::getCreditBalance($row['member_id']); ?>
        <div class="single-member effect-1">
            <div class="member-image">
                <img src="<?php echo getImageUrl($row['member_icon']);?>" alt="Member">
            </div>
            <div class="member-info">
                <h3><?php echo $row['display_name'] ? : $row['login_code'];?></h3>
                <h5>
                    <em title="Credit Balance"><?php echo ncAmountFormat($credit_info['balance']);?></em>
                    <em title="Credit">(<?php echo ncAmountFormat($credit_info['credit']);?>)</em>
                </h5>
                <p>
                    Phone: <span><?php echo $row['phone_id']; ?></span><br/>
                    Open Source: <span><?php echo $lang['source_type_' . $row['open_source']]; ?></span><br/>
                    Address: <?php echo $row['address_detail']; ?>
                </p>
                <div class="social-touch">
                    <a href="<?php echo getUrl('branch_manager', 'showMemberCo', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                       title="Credit Officer"><i class="fa fa-group fa-lg"></i></a>
                    <a href="<?php echo getUrl('branch_manager', 'showCreditProcess', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                       title="Credit Process"><i class="fa fa-credit-card fa-lg"></i></a>
                    <a href="<?php echo getUrl('branch_manager', 'showCBCDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                       title="CBC"><i class="fa fa-search fa-lg"></i></a>
                    <a href="<?php echo getUrl('branch_manager', 'showMemberDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                       title="Detail"><i class="fa fa-address-card-o fa-lg"></i></a>
                </div>
            </div>
        </div>
    <?php }?>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
