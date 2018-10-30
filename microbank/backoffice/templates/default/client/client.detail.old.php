<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=5" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
      <?php $item = $output['detail'];?>
      <div class="client-detail-wrap clearfix">
        <dl class="account-basic clearfix">
					<dt class="pull-left">
  					<p class="account-head">
  						<img src="http://khbuy.b0.upaiyun.com/shop/avatar/avatar_47.jpg" class="avatar-lg" alt="">
  					</p>
					</dt>
					<dd class="pull-left margin-large-left">
  					<p class="text-small">
  						<span class="show pull-left base-name marginright10">Member Name</span>:<span class="marginleft10"><?php echo $item['display_name'];?></span>
  					</p>
  					<p class="text-small">
  						<span class="show pull-left base-name marginright10">GUID</span>:
  						<span class="marginleft10"><?php echo $item['obj_guid'];?></span>
  						<!-- <a class="margin-left text-main text-underline" href="#">立即认证</a> -->
  					</p>
            <p class="text-small">
  						<span class="show pull-left base-name marginright10">Login Code</span>:
  						<span class="marginleft10"><?php echo $item['login_code'];?></span>
  					</p>
  					<p class="text-small">
  						<span class="show pull-left base-name marginright10">Create Time</span>:<span class="marginleft10"><?php echo $item['create_time'];?></span>
  					</p>
					</dd>
				</dl>
        <div class="left-wrap">
          <div class="client-info clearfix">
            <div class="avatar-wrap">
              <img src="http://khbuy.b0.upaiyun.com/shop/avatar/avatar_47.jpg" class="avatar-lg" alt="">
              <div class="info">
                <p class="name"><?php echo $item['display_name'];?></p>
                <p>GUID: <?php echo $item['obj_guid'];?></p>
                <p>Login Code: <?php echo $item['login_code'];?></p>
              </div>
            </div>
            <div class="credit-info clearfix">
              <div class="item">
                Loan Credit
                <p><?php echo $item['credit'];?></p>
                <a href="<?php echo getUrl('loan', 'editCredit', array('uid'=>$item['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="label label-success">Edit Credit</span></a>
              </div>
              <div class="item arrears">
                Loan Balance
                <p>1520.37</p>
              </div>
              <div class="item">
                Account Type
                <p>Member</p>
              </div>
            </div>
            <!--
            -->
          </div>
          <div class="member-info">
            <div class="ibox-title">
              <h5>Base Info</h5>
            </div>
            <div class="ibox-content" style="padding:0;">
              <table class="table">
                  <tbody class="table-body">
                      <tr>
                        <td><label class="control-label">NO.</label></td><td><?php echo $item['uid'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Alias Name</label></td><td><?php
                          $arr = json_decode($item['alias_name'],true);
                          $str = '';
                          foreach ($arr as $key => $value) {
                            $str .= $key.': '.$value.'; ';
                          }
                          echo $str;
                        ?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Family Name</label></td><td><?php echo $item['family_name'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Given Name</label></td><td><?php echo $item['given_name'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Birthday</label></td><td><?php echo $item['birthday'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Gender</label></td><td><?php echo ucwords($item['gender']);?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Staff</label></td><td><?php if($item['is_staff']){echo 'Yes';}else{echo 'No';};?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Civil Status</label></td><td><?php echo $item['civil_status'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Member Grade</label></td><td><?php echo $item['member_grade'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Member Status</label></td><td><?php if($item['member_state']){echo 'On';}else{echo 'Off';};?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Phone</label></td><td><?php echo $item['phone_id'];?><?php if($item['is_verify_phone']){?><span class="verify-status alr">Verified</span><?php }else{?><span class="verify-status">Not Verified</span><?php }?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Email</label></td><td><?php echo $item['email'];?><?php if($item['is_verify_phone']){?><span class="verify-status alr">Verified</span><?php }else{?><span class="verify-status">Not Verified</span><?php }?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Open Source</label></td><td><?php switch ($item['open_source']) {
                          case 0:
                            echo 'Network';
                            break;
                          case 1:
                            echo 'Teller';
                            break;
                          case 10:
                            echo 'Third Party';
                            break;
                          default:
                          echo '网络';
                            break;
                        }?></td>
                        <tr>
                          <tr>
                            <td><label class="control-label">Open Org</label></td><td><?php echo $item['open_org'];?></td>
                            <tr>
                          <td><label class="control-label">Open Addredd</label></td><td><?php echo $item['open_addr'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Update Time</label></td><td><?php echo $item['update_time'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Time</label></td><td><?php echo $item['create_time'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Name</label></td><td><?php echo $item['creator_name'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Time</label></td><td><?php echo $item['create_time'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Name</label></td><td><?php echo $item['creator_name'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Member Property</label></td><td colspan="3"><?php echo $item['member_property'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Member Profile</label></td><td colspan="3"><?php echo $item['member_profile'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Member Verification</label></td><td colspan="3"><?php echo $item['member_verification'];?></td>
                        </tr>
                      </tr>
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="wrap-right">
          <div class="verify-wrap">
            <div>
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Verification</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Guarantee</a></li>
              </ul>

              <div class="tab-content client-verify-info">
                <div role="tabpanel" class="tab-pane active" id="home">
                  <div class="activity-list">
                    <?php $verifys = $output['verifys'];?>
                    <div class="item">
                      <div>ID<!--$verifys[1]-->
                        <?php if($verifys[1]){ ?>
                          <?php if($verifys[1]['verify_state'] == -1){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checking"><i class="fa fa-check"></i>Audit...</span></a>
                          <?php }elseif($verifys[1]['verify_state'] == 10){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checked"><i class="fa fa-check"></i>Have Passed</span></a>
                          <?php }elseif($verifys[1]['verify_state'] == 100){ ?>
                            <span class="checking"><i class="fa fa-remove"></i>Not Pass</span>
                          <?php }else{ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[1]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span><i class="fa fa-edit"></i>Pending Audit</span></a>
                          <?php } ?>
                        <?php }else{ ?>
                          <span><i class="fa fa-edit"></i>Not Verified</span>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="item">
                      <div>Registered Residence
                        <?php if($verifys[2]){ ?>
                          <?php if($verifys[2]['verify_state'] == -1){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checking"><i class="fa fa-check"></i>Audit...</span></a>
                          <?php }elseif($verifys[2]['verify_state'] == 10){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checked"><i class="fa fa-check"></i>Have Passed</span></a>
                          <?php }elseif($verifys[2]['verify_state'] == 100){ ?>
                            <span class="checking"><i class="fa fa-remove"></i>Not Pass</span>
                          <?php }else{ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span><i class="fa fa-edit"></i>Pending Audit</span></a>
                          <?php } ?>
                        <?php }else{ ?>
                          <span><i class="fa fa-edit"></i>Not Verified</span>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="item">
                      <div>Passports
                        <?php if($verifys[3]){ ?>
                          <?php if($verifys[3]['verify_state'] == -1){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checking"><i class="fa fa-check"></i>Audit...</span></a>
                          <?php }elseif($verifys[3]['verify_state'] == 10){ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[2]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span class="checked"><i class="fa fa-check"></i>Have Passed</span></a>
                          <?php }elseif($verifys[3]['verify_state'] == 100){ ?>
                            <span class="checking"><i class="fa fa-remove"></i>Not Pass</span>
                          <?php }else{ ?>
                            <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[3]['uid']), false, BACK_OFFICE_SITE_URL)?>"><span><i class="fa fa-edit"></i>Pending Audit</span></a>
                          <?php } ?>
                        <?php }else{ ?>
                          <span><i class="fa fa-edit"></i>Not Verified</span>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                  <div class="no-record">
                    No Record
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="contract-wrap">
            <div class="contract-list">
              <div class="ibox-title">
                <h5>Contract List</h5>
              </div>
              <div class="ibox-content">
                <div class="activity-list">
                  <div class="item">
                    <div>
                        <small class="pull-right text-navy">6 period</small>
                        <strong>NO9913758473</strong>
                        <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                        <div class="b">
                          <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-primary">ongoing</span>
                          <a class="pull-right" href="<?php echo getUrl('loan', 'contractDetail', array('uid'=>1), false, BACK_OFFICE_SITE_URL)?>">Detail>></a>
                        </div>
                    </div>
                  </div>
                  <div class="item">
                    <div>
                        <small class="pull-right text-navy">6 period</small>
                        <strong>NO9913758473</strong>
                        <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                        <div class="b">
                          <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-success">delay</span>
                          <a class="pull-right" href="#">Detail>></a>
                        </div>
                    </div>
                  </div>
                  <div class="item">
                    <div>
                        <small class="pull-right text-navy">3 period</small>
                        <strong>NO9913758473</strong>
                        <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                        <div class="b">
                          <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-danger">delay</span>
                          <a class="pull-right" href="#">Detail>></a>
                        </div>
                    </div>
                  </div>
                  <div class="item">
                    <div>
                        <small class="pull-right text-navy">6 period</small>
                        <strong>NO9913758473</strong>
                        <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                        <div class="b">
                          <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-default">Cancel</span>
                          <a class="pull-right" href="#">Detail>></a>
                        </div>
                    </div>
                  </div>
                  <div class="item">
                    <div>
                        <small class="pull-right text-navy">12 period</small>
                        <strong>NO9913758473</strong>
                        <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                        <div class="b">
                          <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-success">Done</span>
                          <a class="pull-right" href="#">Detail>></a>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--<div class="contract-wrap">
          <div class="contract-list">
            <div class="ibox-title">
              <h5>Contract List</h5>
            </div>
            <div class="ibox-content">
              <div class="activity-list">
                <div class="item">
                  <div>
                      <small class="pull-right text-navy">6期</small>
                      <strong>NO9913758473</strong>
                      <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                      <div class="b">
                        <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-primary">ongoing</span>
                        <a class="pull-right" href="#">Detail>></a>
                      </div>
                  </div>
                </div>
                <div class="item">
                  <div>
                      <small class="pull-right text-navy">6期</small>
                      <strong>NO9913758473</strong>
                      <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                      <div class="b">
                        <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-success">delay</span>
                        <a class="pull-right" href="#">Detail>></a>
                      </div>
                  </div>
                </div>
                <div class="item">
                  <div>
                      <small class="pull-right text-navy">3期</small>
                      <strong>NO9913758473</strong>
                      <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                      <div class="b">
                        <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-danger">delay</span>
                        <a class="pull-right" href="#">Detail>></a>
                      </div>
                  </div>
                </div>
                <div class="item">
                  <div>
                      <small class="pull-right text-navy">6期</small>
                      <strong>NO9913758473</strong>
                      <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                      <div class="b">
                        <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-default">Cancel</span>
                        <a class="pull-right" href="#">Detail>></a>
                      </div>
                  </div>
                </div>
                <div class="item">
                  <div>
                      <small class="pull-right text-navy">12期</small>
                      <strong>NO9913758473</strong>
                      <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum</div>
                      <div class="b">
                        <small class="text-muted">2017-11-10 14:57:08</small><span class="label label-success">Done</span>
                        <a class="pull-right" href="#">Detail>></a>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="right-wrap">
          <div>
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Verification</a></li>
              <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Guarantee</a></li>
            </ul>

            <div class="tab-content client-verify-info">
              <div role="tabpanel" class="tab-pane active" id="home">
                <div class="activity-list">
                  <div class="item">
                    <div>身份证<span class="checked"><i class="fa fa-check"></i>已验证</span></div>
                  </div>
                  <div class="item">
                    <div>户口<span><i class="fa fa-check"></i>去验证</span></div>
                  </div>
                  <div class="item">
                    <div>职业<span><i class="fa fa-edit"></i>去完善</span></div>
                  </div>
                  <div class="item">
                    <div>联系人<span><i class="fa fa-edit"></i>去完善</span></div>
                  </div>
                </div>
              </div>
              <div role="tabpanel" class="tab-pane" id="profile">
                <div class="no-record">
                  No Record
                </div>
              </div>
            </div>

          </div>
        </div>-->
      </div>
    </div>
</div>
<script>
    $(function () {

    });

</script>
