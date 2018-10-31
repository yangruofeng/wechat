<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=8" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
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
      <input type="hidden" name="credit" id="credit" value="<?php echo $item['credit']?:0;?>">
      <input type="hidden" name="balance" id="balance" value="<?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance']?:0; ?>">
      <div class="client-detail-wrap clearfix">
        <dl class="account-basic clearfix">
					<dt class="pull-left">
  					<p class="account-head">
  						<img src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg" alt="">
  					</p>
					</dt>
					<dd class="pull-left margin-large-left">
  					<!--<p class="text-small">
  						<span class="show pull-left base-name marginright10">Member Name</span>:<span class="marginleft10"><?php echo $item['display_name'];?></span>
  					</p>-->
  					<p class="text-small">
  						<span class="show pull-left base-name marginright10">CID</span>:
  						<span class="marginleft10"><?php echo $item['obj_guid'];?></span>
              <input type="hidden" name="obj_guid" id="obj_guid" value="<?php echo $item['obj_guid'];?>">
              <input type="hidden" name="uid" id="uid" value="<?php echo $item['uid'];?>">
  						<!-- <a class="margin-left text-main text-underline" href="#">立即认证</a> -->
  					</p>
            <p class="text-small">
  						<span class="show pull-left base-name marginright10">Login Account</span>:
  						<span class="marginleft10"><?php echo $item['login_code'];?></span>
  					</p>
            <p class="text-small">
  						<span class="show pull-left base-name marginright10">Phone</span>:
  						<span class="marginleft10"><?php echo $item['phone_id'];?></span>
  					</p>
  					<p class="text-small">
  						<span class="show pull-left base-name marginright10">Status</span>:<span class="marginleft10"><?php echo $item['member_state']==0?'Inactive':'Active';?></span>
  					</p>
					</dd>
				</dl>

        <div class="credit-detail clearfix">
          <div class="pull-left clearfix">
            <div class="credit-basic clearfix">
              <div class="fact-data fact-data-1">
                <div class="epie-chart easyPieChart" data-percent="45" style="width: 150px; height: 150px;">
                  <div class="credit-lan">
                    <p class="base-name">Credit Balance</p>
                    <p class="balance"><?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance']; ?></p>
                  </div>
                  <canvas id="myCanvas" width="150" height="150"></canvas>
                </div>
              </div>
              <div class="fact-data no-padding text-shadow">
                <h4 class="text-small" style="margin-top: 12px;">
                  <span class="show pull-left base-name marginright10">Credit</span>:<span class="marginleft10"><?php echo $item['credit']?:'-';?></span>
                </h4>
                <h4 class="text-small">
                  <span class="show pull-left base-name marginright10">Credit Balance</span>:<span class="marginleft10"><?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance'] ?></span>
                </h4>
                <h4 class="text-small">
                  <span class="show pull-left base-name marginright10">Loan Balance</span>:<span class="marginleft10"><?php echo memberClass::getLoanBalance($item['member_id'])->DATA?:'0.00'; ?></span>
                </h4>
                <div class="custom-btn-group approval-btn-group" style="margin-top: 5px;">
                  <button type="button" class="btn btn-info" onclick="javascript:window.location.href='<?php echo getUrl('loan', 'editCredit', array('obj_guid'=>$item['obj_guid'], 'show_menu'=>'loan-credit'), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-edit"></i>Edit Credit</button>
                  <button type="button" class="btn btn-info" onclick="javascript:window.location.href='<?php echo getUrl('client', 'creditReport', array('obj_guid'=>$item['obj_guid']), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Report</button>
                </div>
              </div>
            </div>
            <div class="black-info">
              <p>Black List</p>
              <div class="list clearfix">
                <?php $black = $output['black']; $count = count($black);?>
                <?php if($count > 0){ ?>
                  <?php foreach ($black as $key => $val) { $label; $state; $field;
                    switch ($val['type']) {
                      case blackTypeEnum::LOGIN :
                        $label = 'Login';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                      case blackTypeEnum::DEPOSIT :
                        $label = 'Deposit';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                      case blackTypeEnum::INSURANCE :
                        $label = 'Insurance';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                      case blackTypeEnum::CREDIT_LOAN :
                        $label = 'Credit Loan';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                      case blackTypeEnum::MORTGAGE_LOAN :
                        $label = 'Mortgage Loan';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                      default:
                        $label = 'Login';
                        $state = $val['check'];
                        $type = $val['type'];
                        break;
                    }
                  ?>
                  <span class="<?php echo $state == 1 ? 'disabled' : '';?>">
                    <i class="fa <?php echo $state == 1 ? 'fa-remove' : 'fa-check';?>"></i><?php echo $label;?>
                    <em onclick="_confirm(<?php echo $type;?>, <?php echo $state == 1 ? : 0;?>);"><?php echo $state == 1 ? '<i class="fa fa-plus"></i>' : '<i class="fa fa-minus"></i>';?></em>
                  </span>
                  <?php } ?>
                <?php }else{ ?>
                  <span><i class="fa fa-check"></i>登录<em onclick="_confirm('<?php echo blackTypeEnum::LOGIN;?>', 0);"><i class="fa fa-minus"></i></em></span>
                  <span><i class="fa fa-check"></i>存款<em onclick="_confirm('<?php echo blackTypeEnum::DEPOSIT;?>', 0);"><i class="fa fa-minus"></i></em></span>
                  <span><i class="fa fa-check"></i>保险<em onclick="_confirm('<?php echo blackTypeEnum::INSURANCE;?>', 0);"><i class="fa fa-minus"></i></em></span>
                  <span><i class="fa fa-check"></i>信用贷<em onclick="_confirm('<?php echo blackTypeEnum::CREDIT_LOAN;?>', 0);"><i class="fa fa-minus"></i></em></span>
                  <span><i class="fa fa-check"></i>抵押贷<em onclick="_confirm('<?php echo blackTypeEnum::MORTGAGE_LOAN;?>', 0);"><i class="fa fa-minus"></i></em></span>
                <?php } ?>

              </div>
            </div>
          </div>
          <div class="pull-right">
            <div class="verify-wrap">
              <div>
                <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Cerification</a></li>
                  <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Guarantee</a></li>
                </ul>

                <div class="tab-content client-verify-info">
                  <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="activity-list">
                      <?php $verify_field = $output['verify_field']; $verifys = $output['verifys'];?>
                      <?php foreach ($verify_field as $key => $value) { ?>
                        <div class="item">
                          <div>
                            <?php if($verifys[$key]){ ?>
                              <?php if($verifys[$key]['verify_state'] == -1){ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $value;?></a>
                              <?php }elseif($verifys[$key]['verify_state'] == 10){ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $value;?></a>
                              <?php }elseif($verifys[$key]['verify_state'] == 100){ ?>
                                <?php echo $value;?>
                              <?php }else{ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $value;?></a>
                              <?php } ?>
                            <?php }else{ ?>
                              <?php echo $value;?>
                            <?php } ?>

                            <?php if($verifys[$key]){ ?>
                              <?php if($verifys[$key]['verify_state'] == -1){ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><span class="checking"><i class="fa fa-check"></i>Audit...</span></a>
                              <?php }elseif($verifys[$key]['verify_state'] == 10){ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><span class="checked"><i class="fa fa-check"></i>Have Passed</span></a>
                              <?php }elseif($verifys[$key]['verify_state'] == 100){ ?>
                                <span class="checking"><i class="fa fa-remove"></i>Refuse</span>
                              <?php }else{ ?>
                                <a href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$verifys[$key]['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>"><span><i class="fa fa-edit"></i>Pending Audit</span></a>
                              <?php } ?>
                            <?php }else{ ?>
                              <span><i class="fa fa-edit"></i>Not Verified</span>
                            <?php } ?>
                          </div>
                        </div>
                      <?php } ?>
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
          </div>
        </div>

        <div class="left-wrap">

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
                        <td><label class="control-label">Member Type</label></td><td>Member</td>
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
                        <td><label class="control-label">Civil Status</label></td><td><?php echo ucwords($item['civil_status']);?></td>
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
                        <td><label class="control-label">Email</label></td><td><?php echo $item['email'];?><?php if($item['is_verify_email']){?><span class="verify-status alr">Verified</span><?php }else{?><span class="verify-status">Not Verified</span><?php }?></td>
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
                          <td><label class="control-label">Update Time</label></td><td><?php echo timeFormat($item['update_time']);?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Time</label></td><td><?php echo timeFormat($item['create_time']);?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Name</label></td><td><?php echo $item['creator_name'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Create Time</label></td><td><?php echo timeFormat($item['create_time']);?></td>
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
          <div class="contract-wrap">
            <?php $contracts = $output['contracts'];$count = count($contracts);?>
            <div class="contract-list">
              <div class="ibox-title">
                <h5>Loan Contract (<?php echo $count;?>)</h5>
              </div>
              <div class="ibox-content">
                <div class="activity-list">

                  <?php if($count > 0){ ?>
                    <?php foreach ($contracts as $key => $value) { ?>
                      <div class="item">
                        <div>
                            <small class="pull-right text-navy"><?php echo $value['loan_cycle'];?> period</small>
                            <strong><?php echo $value['contract_sn']?></strong>
                            <div><?php echo $value['product_name']?>&nbsp;&nbsp;&nbsp;<?php echo $value['currency']?>&nbsp;&nbsp;&nbsp;<?php echo $value['apply_amount']?></div>
                            <div class="b">
                              <small class="text-muted"><?php echo timeFormat($value['create_time'])?></small>
                              <?php $class; $label; switch ($value['state']) {
                                case loanContractStateEnum::CREATE :
                                  $class = 'label-primary';
                                  $label = 'Create';
                                  break;
                                case loanContractStateEnum::PENDING_APPROVAL :
                                  $class = 'label-success';
                                  $label = 'Pending Approval';
                                  break;
                                case loanContractStateEnum::PENDING_DISBURSE :
                                  $class = 'label-success';
                                  $label = 'Pending Disburse';
                                  break;
                                case loanContractStateEnum::PROCESSING :
                                  $class = 'label-success';
                                  $label = 'Ongoing';
                                  break;
                                case loanContractStateEnum::PAUSE :
                                  $class = 'label-success';
                                  $label = 'Pause';
                                  break;
                                case loanContractStateEnum::COMPLETE :
                                  $class = 'label-warning';
                                  $label = 'Complete';
                                  break;
                                case loanContractStateEnum::WRITE_OFF :
                                  $class = 'label-default';
                                  $label = 'Write Off';
                                  break;
                                default:
                                  $class = 'label-default';
                                  $label = 'Write Off';
                                  break;
                              }?>
                              <span class="label <?php echo $class;?>"><?php echo $label;?></span>
                              <a class="pull-right" href="<?php echo getUrl('loan', 'contractDetail', array('uid'=>$value['uid'], 'show_menu'=>'loan-contract'), false, BACK_OFFICE_SITE_URL)?>">Detail>></a>
                            </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php }else{ ?>
                    <div class="no-record">
                      No Record
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="contract-wrap" style="margin-top: 20px;">
            <?php $contracts = $output['insurance_contracts'];$count = count($contracts);?>
            <div class="contract-list">
              <div class="ibox-title">
                <h5>Insurance Contract (<?php echo $count;?>)</h5>
              </div>
              <div class="ibox-content">
                <div class="activity-list">
                  <?php if($count > 0){ ?>
                    <?php foreach ($contracts as $key => $value) { ?>
                      <div class="item">
                        <div>
                            <small class="pull-right text-navy"></small>
                            <strong><?php echo $value['contract_sn']?></strong>
                            <div><?php echo $value['product_name']?>&nbsp;&nbsp;&nbsp;<?php echo $value['currency']?>&nbsp;&nbsp;&nbsp;<?php echo $value['price']?></div>
                            <div class="b">
                              <small class="text-muted"><?php echo timeFormat($value['create_time'])?></small>
                              <?php $class; $label; switch ($value['state']) {
                                case loanContractStateEnum::CREATE :
                                  $class = 'label-primary';
                                  $label = 'Create';
                                  break;
                                case loanContractStateEnum::PROCESSING :
                                  $class = 'label-success';
                                  $label = 'Ongoing';
                                  break;
                                case loanContractStateEnum::FAILURE :
                                  $class = 'label-danger';
                                  $label = 'Failure';
                                  break;
                                case loanContractStateEnum::COMPLETE :
                                  $class = 'label-warning';
                                  $label = 'Complete';
                                  break;
                                case loanContractStateEnum::WRITE_OFF :
                                  $class = 'label-default';
                                  $label = 'Write Off';
                                  break;
                                default:
                                  $class = 'label-default';
                                  $label = 'Write Off';
                                  break;
                              }?>
                              <span class="label <?php echo $class;?>"><?php echo $label;?></span>
                              <a class="pull-right" href="<?php echo getUrl('insurance', 'contractDetail', array('uid'=>1, 'show_menu'=>'insurance-contract'), false, BACK_OFFICE_SITE_URL)?>">Detail>></a>
                            </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php }else{ ?>
                    <div class="no-record">
                      No Record
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=1"></script>
<script>
    var credit = $('#credit').val(), balance = $('#balance').val();
    var ring = parseFloat(balance) / parseFloat(credit) * 100;
    drawRing(150, 150, ring);

    function drawRing(w, h, val) {
        //先创建一个canvas画布对象，设置宽高
        var c = document.getElementById('myCanvas'), ctx = c.getContext('2d'), lineWidth = 8;
        ctx.canvas.width = w;
        ctx.canvas.height = h;
        //圆环有两部分组成，底部灰色完整的环，根据百分比变化的环
        //先绘制底部完整的环
        //起始一条路径
        ctx.beginPath();
        //设置当前线条的宽度
        ctx.lineWidth = lineWidth;
        //设置笔触的颜色
        ctx.strokeStyle = '#f1f1f1';
        //arc()方法创建弧/曲线（用于创建圆或部分圆）arc(圆心x,圆心y,半径,开始角度,结束角度)
        ctx.arc(75, 75, 67, 0, 2 * Math.PI);
        //绘制已定义的路径
        ctx.stroke();

        //绘制根据百分比变动的环
        ctx.beginPath();
        ctx.lineWidth = lineWidth;
        ctx.strokeStyle = '#E84F34';
        //设置开始处为0点钟方向（-90*Math.PI/180）
        //x为百分比值（0-100）
        ctx.arc(75, 75, 67, -90 * Math.PI / 180, (val * 3.6 - 90) * Math.PI / 180);
        ctx.stroke();
        //绘制中间的文字
        /*ctx.font='20px Arial';
         ctx.fillStyle='#747474';
         ctx.textBaseline='middle';
         ctx.textAlign='center';
         ctx.fillText(val+'%',75,75);*/
    }
    function _confirm(type, state) {
      msg = state == 0 ? 'Are you sure to add to black-list?' : 'Are you sure to remove from black-list？';
      yo.confirm('', msg, function (_r) {
          if(!_r) return false;
        var uid = $('#uid').val();
        state = state == 0 ? 1 : 0;
          //提交修改
        yo.loadData({
           _c: 'client',
           _m: 'updateBlackClientType',
           param: {uid: uid, type: type, state: state},
           callback: function (_o) {
             if (_o.STS) {
               alert('Saved successfully',1,function(){
                   window.location.reload();
               });
             } else {
               alert(_o.MSG, 2);
             }
           }
       });
      });
    }
</script>
