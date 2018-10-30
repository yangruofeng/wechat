<div>
    <table class="table table-striped table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'UID';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Password';?></td>
            <td><?php echo 'Gender';?></td>
<!--            <td>--><?php //echo 'Credit Balance';?><!--</td>-->
<!--            <td>--><?php //echo 'Create Time';?><!--</td>-->
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
          <?php $acc_loan_balance = $data['acc_loan_balance'];?>
        <?php foreach($data['data'] as $row){ ?>
            <tr>

              <td>
                  <?php echo $row['uid'] ?>
              </td>
                <td>
                    <?php echo $row['user_name'] ?>
                </td>
              <td>
                  <?php echo $row['password'] ?>
              </td>
              <td>
                  <?php echo $row['gender']; ?>
              </td>

              <td>
                  <div class="custom-btn-group">
                    <a title="" class="btn btn-link btn-xs" href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id']), false, BACK_OFFICE_SITE_URL)?>">
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
<?php include_once(template("inc_content_pager"));?>
