<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
<style>
p {line-height: 30px;}
</style>
<p><a href="javascript:message('', '', 'loading')">Loading</a></p>
<p><a href="javascript:message('操作提示!', '请选择数据后再进行操作', 'prompt')">Prompt</a></p>
<p><a href="javascript:;" onclick="_confirm();">Confirm</a></p>
<p><a href="javascript:message('操作成功!', '操作成功!', 'succ')">Success</a></p>
<p><a href="javascript:;" onclick="_success();">Success(回调)</a></p>
<p><a href="javascript:message('提交失败', '请稍后再试', 'error')">Error</a></p>
<p><a href="javascript:message('', '', 'close')">Close</a></p>
<p><a href="javascript:message('', '', 'closeAll')">CloseAll</a></p>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=2"></script>
<script>
function _confirm(){
  message('确定提交审核吗？', '提交后将不能进行修改。', 'confirm', function(){
    alert('执行回调');
  })
}

function _success(){
  message('操作成功!', '操作成功!', 'succ', function(){
    alert('成功，刷新页面');
    window.location.reload();
  })
}
</script>
