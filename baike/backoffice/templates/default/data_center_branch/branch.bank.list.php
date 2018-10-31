<?php
$list = $data['data'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr class="table-header t1">
            <td class="number">No</td>
            <td class="number">Branch</td>
            <td class="number">Bank</td>
            <td class="number">Account No</td>
            <td class="number">Account Name</td>
            <td class="number">Status</td>
            <td class="number">Currency</td>
            <td class="number">Debit</td>
            <td class="number">Credit</td>
            <td class="number">Balance</td>
            <td class="number">Function</td>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if( count($list) > 0 ){ ?>
            <?php foreach( $list as $v ){ ?>
                <tr>
                    <td class="number"><?php echo $v['no'];?></td>
                    <td class="number"><?php echo $v['branch_name'];?></td>
                    <td class="number"><?php echo $v['bank_name'];?></td>
                    <td class="number"><?php echo $v['bank_account_no'];?></td>
                    <td class="number"><?php echo $v['bank_account_name'];?></td>
                    <td class="number <?php echo $v['account_state'] ? 'green' : 'red';?>"><?php echo $v['account_state']?'Active':'Inactive';?></td>
                    <td class="number"><?php echo $v['currency'];?></td>
                    <td class="currency"><?php echo ncPriceFormat($v['debit']);?></td>
                    <td class="currency"><?php echo ncPriceFormat($v['credit']);?></td>
                    <td class="currency"><?php echo ncPriceFormat($v['balance']);?></td>
                    <td class="number"><a href="javascript:void(0);" onclick="go_flow(<?php echo $v['account_id'];?>)">Flow</a></td>
                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="11">
                    <div>
                        <?php include(template(":widget/no_record")); ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if(count($list) > 0){?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php }else{?>
    <?php if($data['pageNumber'] != 1){?>
        <?php include_once(template("widget/inc_content_pager")); ?>
    <?php }?>
<?php }?>
<script>
    function go_flow(account_id){
        yo.dynamicTpl({
            tpl: "data_center_branch/branch.bank.flow.index",
            dynamic: {
                api: "data_center_branch",
                method: "showBranchBankFlowPage",
                param: {
                    account_id: account_id,
                    date_start:  $('#date_search_from').val(),
                    date_end: $('#date_search_to').val()
                }
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }
</script>

