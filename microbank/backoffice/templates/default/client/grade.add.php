<style>
label.error {
  color: red;
}
</style>
<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Grade</h3>
          <ul class="tab-base">
            <li><a href="<?php echo getUrl('client', 'grade', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
            <li><a class="current"><span>Add</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container">
    <form class="form-horizontal" method="post" id="validForm" action="<?php echo getUrl('client', 'addGrade', array(), false, BACK_OFFICE_SITE_URL) ?>">
        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" name="grade_id" value="<?php echo $output['member_grade']['uid'] ?>">
        <div class="form-group">
            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Grade Code'?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="grade_code" placeholder="" value="<?php echo $output['member_grade']['grade_code'] ?>">
                <span class="validate-checktip"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Min Score'?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="min_score" placeholder="" value="<?php echo $output['member_grade']['min_score'] ?>">
                <span class="validate-checktip"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Max Score'?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="max_score" placeholder="" value="<?php echo $output['member_grade']['max_score'] ?>">
                <span class="validate-checktip"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Grade Caption'?></label>
            <div class="col-sm-8">
                <textarea class="form-control" name="grade_caption" rows="8" cols="80"><?php echo $output['member_grade']['grade_caption'] ?></textarea>
                <span class="validate-checktip"></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-col-sm-8" style="padding-left: 15px">
                <button type="button" class="btn btn-danger" onclick="submitForm();"><?php echo 'Save' ?></button>
            </div>
        </div>
    </form>
  </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/jquery.validate.min.js?v=2"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=20"></script>
<script>
function submitForm(){
  $('#validForm').submit();
}
var validParam = {
  ele: '#validForm', //表单id
  params: [{
    field: 'grade_code',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the grade code.'
    }
  },{
    field: 'min_score',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the min score.'
    }
  },{
    field: 'max_score',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the max score.'
    }
  },{
    field: 'grade_caption',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the grade caption.'
    }
  }]
};
validform(validParam);
</script>
