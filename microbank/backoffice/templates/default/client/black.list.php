<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=1" rel="stylesheet" type="text/css"/>
<div>
  <table class="table verify-table">
    <thead>
    <tr class="table-header">
      <td><?php echo 'Member GUID';?></td>
      <td><?php echo 'Member Name';?></td>
      <td><?php echo 'Black List';?></td>
      <td><?php echo 'Function';?></td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php foreach($data['data'] as $row){ ?>
        <tr>
          <td><?php echo $row['obj_guid'] ?></td>
          <td><a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['uid'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['display_name'] ?></a></td>
          <td>
            <?php $black = $row['black']; $black = json_decode($black, true);$count = count($black);?>
            <?php if($count > 0){ ?>
              <?php foreach ($black as $key => $val) { $label; $state;
                switch ($key) {
                  case 't'.blackTypeEnum::LOGIN :
                    $label = 'Login';
                    $state = $val;
                    break;
                  case 't'.blackTypeEnum::DEPOSIT :
                    $label = 'Deposit';
                    $state = $val;
                    break;
                  case 't'.blackTypeEnum::INSURANCE :
                    $label = 'Insurance';
                    $state = $val;
                    break;
                  case 't'.blackTypeEnum::CREDIT_LOAN :
                    $label = 'Credit Loan';
                    $state = $val;
                    break;
                  case 't'.blackTypeEnum::MORTGAGE_LOAN :
                    $label = 'Mortgage Loan';
                    $state = $val;
                    break;
                  default:
                    $label = 'Login';
                    $state = $val;
                    break;
                }
                ?>

                <span class="label <?php echo $state == 1 ? 'label-default' : 'label-success';?>"><?php echo $label;?></span>
              <?php } ?>
            <?php }else{ ?>
              <span class="color28B779"><i class="fa fa-check"></i></span>
            <?php } ?>
          </td>
          <td>
            <div class="custom-btn-group">
              <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'editBlack', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                <span><i class="fa  fa-edit"></i>Edit</span>
              </a>
            </div>
          </td>
        </tr>
    <?php }?>
    </tbody>
  </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
