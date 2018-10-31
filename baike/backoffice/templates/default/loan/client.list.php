<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'ID';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Loan Balance';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Email';?></td>
            <td><?php echo 'Account Type';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
              <td>
                  <?php echo $row['uid'] ?>
              </td>
              <td>
                  <?php echo $row['display_name'] ?>
              </td>
              <td>
                  <?php echo $row['credit'] ?>
              </td>
              <td>
                  1520.37
              </td>
              <td>
                  <?php echo $row['phone_id'] ?>
              </td>
              <td>
                  <?php echo $row['email'] ?>
              </td>
              <td>
                <?php if($row['account_type'] == 0){echo 'Member';} ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'clientDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <span><i class="fa  fa-vcard-o"></i>Detail</span>
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
