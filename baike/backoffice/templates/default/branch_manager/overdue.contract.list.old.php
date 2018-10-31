<style>
#myModal .modal-dialog, #cbcModal .modal-dialog {
    margin-top: 10px!important;
}
.warning {
    color: #da0000;
}
</style>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Member Name';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Apply Amount';?></td>
            <td><?php echo 'Overdue Amount';?></td>
            <td><?php echo 'Receivable Date';?></td>
            <td><?php echo 'Credit Officer';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){ ?>
            <tr>
              <td>
                  <?php echo $row['obj_guid'] ?>
              </td>
              <td>
                  <?php echo $row['login_code'] ?>
              </td>
              <td>
                  <?php echo $row['phone_id'] ?>
              </td>
              <td>
                  <?php echo $row['contract_sn']; ?>
              </td>
              <td>
                  <?php echo ncAmountFormat($row['apply_amount']); ?>
              </td>
              <td>
                  <span class="warning"><?php echo ncAmountFormat($row['amount']-$row['actual_payment_amount']); ?></span>
              </td>
              <td>
                  <span class="warning"><?php echo dateFormat($row['receivable_date']); ?></span>
              </td>
              <td>
                  <?php echo $row['co_name']?:'None'; ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="showCoModal(<?php echo $row['uid']?:0; ?>,<?php echo $row['co_id']?:0; ?>,<?php echo $row['member_id']?:0; ?>);">
                        <span><i class="fa fa-edit"></i>Edit</span>
                    </a>
                  </div>
              </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Credit Officer</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="co_task_form">
                        <input type="hidden" name="contract_id" id="contract_id" value="0">
                        <input type="hidden" name="member_id" id="member_id" value="0">
                        <div class="col-sm-12">
                            <label  class="col-sm-2 control-label"><span class="required-options-xing">*</span>List</label>
                            <div class="col-sm-10">
                                <?php foreach ($data['co_list'] as $co) { ?>
                                    <div class="col-sm-4">
                                        <label class="checkbox-inline">
                                            <input type="radio" name="co_id" value="<?php echo $co['uid']; ?>" <?php echo in_array($co['uid'],$user_co) ? 'checked' : ''?>><?php echo $co['user_name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="COSubmit();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
<script>
    function showCoModal(uid, co_id, member_id) {
        $('#contract_id').val(uid);
        $('#member_id').val(member_id);
        if(co_id){
            $(":radio[name='co_id'][value='" + co_id + "']").prop("checked", true);
        }else{
            $(":radio[name='co_id']").prop("checked", false);
        }

        $('#myModal').modal('show');
    }
    function COSubmit(){
        var values = getFormJson($('#co_task_form'));
        
        if(!values.co_id){
            alert('Please select credit officer.');
            return;
        }
        yo.loadData({
            _c: 'branch_manager',
            _m: 'editOverdueContractCo',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

</script>