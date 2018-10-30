
<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)">
            <td>Contract No.</td>
            <td>Member Code</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>State</td>
            <td>Update Time</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['contract_sn'] ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo $row['loss_amount'] ?>
                    </td>
                    <td>
                        <?php echo $lang['write_off_state_'.$row['state']] ?>
                    </td>
                    <td>
                        <?php echo $row['create_time'] ?>
                    </td>
                    <td>
                        <a href="<?php echo getBackOfficeUrl('branch_manager','contractWrittenOffDetail',array('uid'=>$row['contract_id'])); ?>" class="btn btn-default">
                            <span>
                                <i class="fa fa-vcard-o"></i>
                                Detail
                            </span>

                        </a>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
            <tr>
                <td colspan="6">No Record</td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

