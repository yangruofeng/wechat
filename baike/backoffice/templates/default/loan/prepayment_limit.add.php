<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Prepayment Limit</h3>
          <ul class="tab-base">
              <li><a href="<?php echo getUrl('loan', 'prepaymentLimit', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
              <li><a class="current"><span>Add</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container">
    <div class="col-sm-6">
        <form class="form-horizontal" method="post" id="limitForm" action="<?php echo getUrl('loan', 'addPrepaymentLimit', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Loan Days</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="loan_days" placeholder="" value="">
                    <span class="validate-checktip"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Limit Days</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="limit_days" placeholder="" value="">
                    <span class="validate-checktip"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-col-sm-8" style="padding-left: 15px">
                    <button type="button" class="btn btn-danger" onclick="submitForm();">Save</button>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/jquery.validate.min.js?v=2"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=20"></script>
<script>
    function submitForm(){
  $('#limitForm').submit();
}
var validParam = {
  ele: '#limitForm', //表单id
  params: [{
    field: 'loan_days',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input loan days.'
    }
  },{
    field: 'limit_days',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input limit days.'
    }
  }]
};
validform(validParam);
</script>
