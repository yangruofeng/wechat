<div>
    <table class="table">
        <?php if ($data['group'] == 'bank') { ?>
            <thead>
            <tr class="table-header">
                <td><?php echo 'Bank Name';?></td>
                <td><?php echo 'Account No';?></td>
                <td><?php echo 'Account Name';?></td>
                <td><?php echo 'Account Phone';?></td>
                <td><?php echo 'Currency';?></td>
                <td><?php echo 'Balance';?></td>
                <td><?php echo 'Branch Belong';?></td>
                <td><?php echo 'State';?></td>
                <td><?php echo 'Function';?></td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php foreach ($data['data'] as $key => $row) {?>
                <tr>
                    <td>
                        <?php echo $row['bank_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['bank_account_no']; ?>
                    </td>
                    <td>
                        <?php echo $row['bank_account_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['bank_account_phone']; ?>
                    </td>
                    <td>
                        <?php echo $row['currency']; ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['balance']); ?>
                    </td>
                    <td>
                        <?php echo $row['branch_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['account_state'] == 1 ? 'Valid' : 'Invalid'; ?>
                    </td>
                    <td>
                        <!--  <a title="<?php echo 'Transaction';?>" href="<?php echo getUrl('financial', 'bankTransaction', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>" style="margin-right: 5px" >
                            <i class="fa fa-edit"></i>
                            Transaction
                        </a>
                        -->
                        <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('financial', 'editBank', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>" style="margin-right: 5px" >
                            <i class="fa fa-edit"></i>
                            Edit
                        </a>
                        <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('financial', 'deleteBank', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>">
                            <i class="fa fa-trash"></i>
                            Delete
                        </a>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        <?php } else { ?>
            <thead>
            <tr class="table-header">
                <td><?php echo 'Branch Name';?></td>
                <td><?php echo 'Bank Name';?></td>
                <td><?php echo 'Account No';?></td>
                <td><?php echo 'Account Name';?></td>
                <td><?php echo 'Account Phone';?></td>
                <td><?php echo 'Currency';?></td>
                <td><?php echo 'Balance';?></td>
                <td><?php echo 'State';?></td>
                <td><?php echo 'Function';?></td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $j = 0 ;foreach ($data['data'] as $key => $row) {$j++?>
                <?php $i = 0;foreach($row['bank_list'] as $bank) {$i++;?>
                    <tr class="<?php echo $j % 2 == 1 ? "tr_odd" : "tr_even"?>">
                        <?php if ($i == 1) { ?>
                            <td rowspan="<?php echo count($row['bank_list']);?>">
                                <?php echo $row['branch_name']; ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo $bank['bank_name']; ?>
                        </td>
                        <td>
                            <?php echo $bank['bank_account_no']; ?>
                        </td>
                        <td>
                            <?php echo $bank['bank_account_name']; ?>
                        </td>
                        <td>
                            <?php echo $bank['bank_account_phone']; ?>
                        </td>
                        <td>
                            <?php echo $bank['currency']; ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($bank['balance']); ?>
                        </td>
                        <td>
                            <?php echo $bank['account_state'] == 1 ? 'Valid' : 'Invalid'; ?>
                        </td>
                        <td>
                            <a title="<?php echo 'Transaction';?>" href="<?php echo getUrl('financial', 'bankTransaction', array('uid'=>$bank['uid']), false, BACK_OFFICE_SITE_URL); ?>" style="margin-right: 5px" >
                                <i class="fa fa-edit"></i>
                                Transaction
                            </a>
                            <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('financial', 'editBank', array('uid'=>$bank['uid']), false, BACK_OFFICE_SITE_URL); ?>" style="margin-right: 5px" >
                                <i class="fa fa-edit"></i>
                                Edit
                            </a>
                            <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('financial', 'deleteBank', array('uid'=>$bank['uid']), false, BACK_OFFICE_SITE_URL); ?>">
                                <i class="fa fa-trash"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php }?>
            <?php }?>
            </tbody>
        <?php } ?>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>