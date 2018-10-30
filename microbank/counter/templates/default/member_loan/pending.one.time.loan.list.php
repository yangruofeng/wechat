<?php
$member_one_time_task_list=$output['memberOneTimeTaskList'];
?>
<div>
    <form style="margin-bottom: 0">
        <table class="table table-bordered">
            <thead>
            <tr style="background-color: #DEDEDE">
                <td>Product Name</td>
                <td>Category Name</td>
                <td>Currency</td>
                <td>Amount</td>
<!--                <td>State</td>-->
                <td>Create Time</td>
                <td>Function</td>
            </tr>
            </thead>
            <tbody>
            <?php if ($member_one_time_task_list) {  ?>
                <?php foreach ($member_one_time_task_list as $key => $row) { ?>
                    <tr>
                        <td>
                            <?php echo $row['sub_product_name'] ?>
                        </td>
                        <td>
                            <?php echo $row['alias'] ?>
                        </td>
                        <td>
                            <?php echo $row['currency'] ?>
                        </td>
                        <td>
                            <?php  if($output['state'] == 3){
                                echo ncPriceFormat($row['apply_amount']);
                            }else{
                                if($row['currency'] == currencyEnum::USD){
                                    echo  ncPriceFormat($row['credit_usd_balance']);
                                }else{
                                  echo   ncPriceFormat($row['credit_khr_balance']);
                                }
                            }
                           ?>
                        </td>
<!--                        <td>-->
<!--                            --><?php //echo $row['state']; ?>
<!--                        </td>-->
                        <td>
                            <?php echo $row['create_time'] ?>
                        </td>
                        <td>
                            <?php if($output['state'] == 3) {?>
                                <a class="btn btn-default" href="<?php echo getUrl('member_loan', 'showOneTimeLoanDisburse', array('biz_id'=>$row['uid']), false, ENTRY_COUNTER_SITE_URL)?>"><?php echo 'Detail' ?></a>
                            <?php } else { ?>
                                <a class="btn btn-danger" href="<?php echo getUrl('member_loan', 'oneTimeLoanDisburse', array('biz_id'=>$row['uid']), false, ENTRY_COUNTER_SITE_URL)?>"><?php echo 'Disburse' ?></a>

                                <span class="btn btn-default" onclick="cancelTask(<?php echo $row['uid']; ?>);">
                                    Cancel
                                </span>
                            <?php }?>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>
                <tr>
                    <td colspan="7">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    function cancelTask(biz_id)
    {
        showMask();
        yo.loadData({
            _c: 'member_loan',
            _m: 'cancelOneTimeLoanTask',
            param: {biz_id:biz_id},
            callback: function (_o) {
                hideMask();
                if (_o.STS) {
                    alert('Cancel success!',1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
