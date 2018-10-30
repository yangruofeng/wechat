<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Start Insured Amount';?></td>
            <td><?php echo 'Price';?></td>
            <td><?php echo 'Tax Fee';?></td>
            <td><?php echo 'Start Date';?></td>
            <td><?php echo 'End Date';?></td>
            <td><?php echo 'Account Type';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
              <td>
                  <?php echo $row['contract_sn'] ?>
              </td>
              <td>
                  <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['display_name'] ?></a>
              </td>
              <td>
                  <?php echo $row['start_insured_amount'] ?>
              </td>
              <td>
                  <?php echo $row['price'] ?>
              </td>
              <td>
                  <?php echo $row['tax_fee'] ?>
              </td>
              <td>
                  <?php echo $row['start_date'] ?>
              </td>
              <td>
                  <?php echo $row['end_date'] ?>
              </td>
              <td>
                <?php if($row['account_type'] == 0){echo 'Member';} ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('insurance', 'contractDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <span><i class="fa fa-vcard-o"></i>Detail</span>
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
