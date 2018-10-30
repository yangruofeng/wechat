<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Login Code';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Email';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
          <?php $acc_loan_balance = $data['acc_loan_balance'];?>
        <?php foreach($data['data'] as $row){ ?>
            <tr>
              <td>
                  <?php echo $row['obj_guid'] ?>
              </td>
              <td>
                  <?php echo $row['display_name'] ?>
              </td>
                <td>
                    <?php echo $row['login_code'] ?>
                </td>
              <td>
                  <?php echo $row['phone_id'] ?>
              </td>
              <td>
                  <?php echo $row['email'] ?>
              </td>
              <td>
                <?php echo timeFormat($row['create_time']); ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="reset_password(<?php echo $row['uid']?>)">
                        <span><i class="fa fa-key"></i>Reset</span>
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
