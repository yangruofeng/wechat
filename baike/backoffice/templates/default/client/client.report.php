<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=3" rel="stylesheet" type="text/css"/>
<div class="page">
  <?php $item = $output['detail'];?>
  <input type="hidden" name="credit" id="credit" value="<?php echo $item['credit']?:0;?>">
  <input type="hidden" name="balance" id="balance" value="<?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance']?:0; ?>">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Client</h3>
          <ul class="tab-base">
              <li><a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
              <li><a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$item['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Detail</span></a></li>
              <li><a class="current"><span>Report</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container">
    <div class="report-basic clearfix">
      <div class="credit-basic clearfix">
        <div class="fact-data fact-data-1">
          <div class="epie-chart easyPieChart" data-percent="45" style="width: 150px; height: 150px;">
            <div class="credit-lan">
              <p class="base-name">Credit Balance</p>
              <p class="balance"><?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance']?:0; ?></p>
            </div>
            <canvas id="myCanvas" width="150" height="150"></canvas>
          </div>
        </div>
        <div class="fact-data no-padding text-shadow">
          <h4 class="text-small" style="margin-top: 32px;">
            <span class="show pull-left base-name marginright10">Credit</span>:<span class="marginleft10"><?php echo $item['credit'];?></span>
          </h4>
          <h4 class="text-small">
            <span class="show pull-left base-name marginright10">Credit Balance</span>:<span class="marginleft10"><?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance']?:'0.00'; ?></span>
          </h4>
          <h4 class="text-small">
            <span class="show pull-left base-name marginright10">Loan Balance</span>:<span class="marginleft10"><?php echo memberClass::getLoanBalance($item['member_id'])->DATA?:'0.00'; ?></span>
          </h4>
        </div>
      </div>
      <div class="basic-block client-info">
        <div class="avatar">
          <img src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg" alt="">
        </div>
        <div class="info">
          <p class="text-small">
  					<span class="show pull-left base-name marginright10">Member Name</span>:<span class="marginleft10"><?php echo $item['display_name'];?></span>
  				</p>
  				<p class="text-small">
  					<span class="show pull-left base-name marginright10">CID</span>:
  					<span class="marginleft10"><?php echo $item['obj_guid'];?></span>
  				</p>
          <p class="text-small">
  					<span class="show pull-left base-name marginright10">Login Code</span>:
  					<span class="marginleft10"><?php echo $item['login_code'];?></span>
  				</p>
  				<p class="text-small">
  					<span class="show pull-left base-name marginright10">Create Time</span>:<span class="marginleft10"><?php echo timeFormat($item['create_time']);?></span>
  				</p>
        </div>
      </div>
      <div class="basic-block time-info">
        <div class="repayment-time">
          <div class="time"><?php echo $output['remayment_count'];?></div>
          <div class="name">Repayment Times</div>
        </div>
        <div class="break-time">
          <div class="time"><?php echo $output['default_count'];?></div>
          <div class="name">Default Times</div>
        </div>
      </div>

    </div>
    <div class="report-record clearfix">
      <div class="record-left">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#creditRecord" aria-controls="creditRecord" role="tab" data-toggle="tab">Credit Record</a></li>
          <li role="presentation"><a href="#withdrawRecord" aria-controls="withdrawRecord" role="tab" data-toggle="tab">Withdraw Record</a></li>
        </ul>
        <div class="tab-content client-verify-info">
          <div role="tabpanel" class="tab-pane active" id="creditRecord">
            <div class="record-list-tab">
              <?php if(count($output['credit_list']) > 0){?>
                <table class="table">
                  <thead>
                    <tr class="table-header">
                        <td><?php echo 'Before';?></td>
                        <td><?php echo 'Current';?></td>
                        <td><?php echo 'Type';?></td>
                        <td><?php echo 'Status';?></td>
                        <td><?php echo 'Create Time';?></td>
                        <!--<td><?php echo 'Function';?></td>-->
                    </tr>
                  </thead>
                  <tbody class="table-body">
                    <?php foreach ($output['credit_list'] as $key => $val) { ?>
                      <tr>
                        <td><?php echo $val['before_credit'];?></td>
                        <td><?php echo $val['current_credit'];?></td>
                        <td><?php if($val['type'] == 0){echo '初次授信';}elseif($val['type'] == 1){echo '上调';}else{echo '下调';}?></td>
                        <td><?php if($val['state'] == 0){echo '申请中';}elseif($val['state'] == 1){echo '审核通过';}else{echo '不通过';}?></td>
                        <td><?php echo timeFormat($val['create_time']);?></td>
                        <!--<td>
                          <?php if($val['state'] == 0){?>
                            <div class="custom-btn-group">
                              <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'approvalEdit', array('uid'=>$val['uid'], 'show_menu'=>'loan-credit'), false, BACK_OFFICE_SITE_URL)?>">
                                  <span><i class="fa fa-edit"></i>去审核</span>
                              </a>
                            </div>
                          <?php }else{ ?>
                            --
                          <?php } ?>
                        </td>-->
                      </tr>
                    <?php }?>
                </tbody>
              </table>
            <?php }else{ ?>
              <div class="no-record">
                No Record
              </div>
            <?php } ?>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="withdrawRecord">
          <div class="record-list-tab">
            <div class="no-record">
              No Record
            </div>
          </div>
        </div>
        </div>
      </div>
      <div class="record-right">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#cerification" aria-controls="cerification" role="tab" data-toggle="tab">Cerification</a></li>
          <li role="presentation"><a href="#cerificationRecord" aria-controls="cerificationRecord" role="tab" data-toggle="tab">Cerification Record</a></li>
        </ul>
        <div class="tab-content client-verify-info">
          <div role="tabpanel" class="tab-pane active" id="cerification">
            <div class="activity-list record-list-tab">
              <?php $verify_field = $output['verify_field']; $verifys = $output['verifys'];?>
              <?php foreach ($verify_field as $key => $value) { ?>
                <div class="item">
                  <div>
                    <?php if($verifys[$key]){ ?>
                      <?php if($verifys[$key]['verify_state'] == -1){ ?>
                        <?php echo $value;?>
                      <?php }elseif($verifys[$key]['verify_state'] == 10){ ?>
                        <?php echo $value;?>
                      <?php }elseif($verifys[$key]['verify_state'] == 100){ ?>
                        <?php echo $value;?>
                      <?php }else{ ?>
                        <?php echo $value;?>
                      <?php } ?>
                    <?php }else{ ?>
                      <?php echo $value;?>
                    <?php } ?>

                    <?php if($verifys[$key]){ ?>
                      <?php if($verifys[$key]['verify_state'] == -1){ ?>
                        <span class="checking"><i class="fa fa-check"></i>Audit...</span>
                      <?php }elseif($verifys[$key]['verify_state'] == 10){ ?>
                        <span class="checked"><i class="fa fa-check"></i>Have Passed</span>
                      <?php }elseif($verifys[$key]['verify_state'] == 100){ ?>
                        <span class="checking"><i class="fa fa-remove"></i>Refuse</span>
                      <?php }else{ ?>
                        <span><i class="fa fa-edit"></i>Pending Audit</span>
                      <?php } ?>
                    <?php }else{ ?>
                      <span><i class="fa fa-edit"></i>Not Verified</span>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="cerificationRecord">
            <div class="cerification-record-list record-list-tab">
              <?php $verifys_list = $output['verifys_list'];?>
              <?php if(count($verifys_list) > 0){ ?>
                <table class="table">
                  <thead>
                    <tr class="table-header">
                        <td><?php echo 'Type';?></td>
                        <td><?php echo 'Status';?></td>
                        <td><?php echo 'Auditor Time';?></td>
                        <td><?php echo 'Function';?></td>
                    </tr>
                  </thead>
                  <tbody class="table-body">
                      <?php foreach ($verifys_list as $key => $value) { ?>
                        <tr>
                          <td><?php if($value['cert_type'] == certificationTypeEnum::ID){echo 'ID';}elseif($value['cert_type'] == certificationTypeEnum::FAIMILYBOOK){echo 'Faimily Book';}elseif($value['cert_type'] == certificationTypeEnum::PASSPORT){echo 'Passport';}elseif($value['cert_type'] == certificationTypeEnum::HOUSE){echo 'Housing & Store';}elseif($value['cert_type'] == certificationTypeEnum::CAR){echo 'Car';} ?></td>
                          <td><?php if($value['verify_state']==-1){echo '<em class="locking">Audit...</em>';}elseif($value['verify_state']==0){echo '未审核';}elseif($value['verify_state']==10){echo '审核通过';}else{echo 'No Pass';} ?></td>
                          <td><?php echo timeFormat($value['auditor_time']);?></td>
                          <td>
                            <div class="custom-btn-group">
                              <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$value['uid'], 'show_menu'=>'client-cerification'), false, BACK_OFFICE_SITE_URL)?>">
                                  <span><i class="fa fa-vcard-o"></i>Detail</span>
                              </a>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                </table>
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
    <div class="report-record cbc-record">
      <div class="ibox-title">
        <h5>CBC查询记录</h5>
      </div>
      <div class="content">
        <div class="no-record">
          No Record
        </div>
      </div>
    </div>
  </div>
</div>
<script>
var credit = $('#credit').val(), balance = $('#balance').val();
var ring = parseFloat(balance) / parseFloat(credit) * 100;
drawRing(150, 150, ring);

function drawRing(w, h, val){
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
  ctx.arc(75, 75, 67, 0, 2*Math.PI);
  //绘制已定义的路径
  ctx.stroke();

  //绘制根据百分比变动的环
  ctx.beginPath();
  ctx.lineWidth = lineWidth;
  ctx.strokeStyle = '#E84F34';
  //设置开始处为0点钟方向（-90*Math.PI/180）
  //x为百分比值（0-100）
  ctx.arc(75, 75, 67, -90*Math.PI/180, (val*3.6-90)*Math.PI/180);
  ctx.stroke();
}
</script>
