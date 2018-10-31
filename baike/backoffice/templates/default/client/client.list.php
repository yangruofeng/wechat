<div>
    <table class="table table-striped table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Account';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Credit Limit';?></td>
            <td><?php echo 'Credit Balance';?></td>
<!--            <td>--><?php //echo 'Email';?><!--</td>-->

            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
          <?php $acc_loan_balance = $data['acc_loan_balance'];?>
        <?php foreach($data['data'] as $row){ ?>
            <tr>
              <td>
                  <?php echo $row['o_guid']?:generateGuid($row['member_id'], objGuidTypeEnum::CLIENT_MEMBER) ?>
              </td>
              <td>
                  <?php echo $row['login_code'] ?>
              </td>
                <td>
                    <?php echo $row['display_name'] ?>
                </td>
              <td>
                  <?php echo $row['phone_id'] ?>
              </td>
              <td>
                  <?php echo $row['credit']; ?>
              </td>
              <td>
                  <?php echo $row['credit_balance']; ?>
              </td>

<!--              <td>-->
<!--                  --><?php //echo $row['email'] ?>
<!--              </td>-->

              <td>
                <?php echo timeFormat($row['create_time']); ?>
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
<?php include_once(template("widget/inc_content_pager"));?>
