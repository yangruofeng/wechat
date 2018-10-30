<link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL.'/font/font-awesome/css/font-awesome.css'; ?>">
<style>
html, body, ul, li{padding: 0;margin: 0;}
body {font-size: 14px;}
p {margin: 0;padding: 0;}
textarea {outline: none;}
ul, li {list-style: none;}
.script-form {width: 600px;margin: 0 auto;margin-top: 150px;}
.sql-script {width: 600px;height: 300px;resize: none;padding: 8px;border-radius: 3px;border: 1px solid #d0cfcf;margin: 0 0 10px 0;font-family: sans-serif;}
.btn-submit {display: inline-block;padding: 8px 20px;background-color: #5BB75B;color: #fff;text-shadow: 0 -1px 0 rgba(0,0,0,0.1);text-decoration: none;}
.btn-submit:hover {text-decoration: none;color: #FFF;background-color: #51A351;}
.script-tip-wrap {display: none;position: absolute;top: 0;left: 0;width: 100%;height: 100%;}
.script-tip {height: 100%;position: relative;z-index: 9999;}
.script-tip .content {width: 100px;margin: 0 auto;text-align: center;padding: 15px 0;background: rgba(0, 0, 0, .4);color: #fff;border-radius: 4px;position: absolute;top: 50%;left: 50%;margin-top: -112px;margin-left: -50px;}
.script-tip span i {font-size: 22px;}
.added-sql-wrap {margin: 20px 50px;font-size: 12px;line-height: 18px;}
.added-sql-wrap .item {position: relative;padding: 10px 15px 10px 105px;border: 1px solid #ddd;margin-top: -1px;min-height: 38px;}
.added-sql-wrap .item .time {position: absolute;left: 15px;top: 10px;display: inline-block;width: 80px;text-align: center;color: #909090;}
</style>
<a href="index.php?act=index&op=list">Update</a>
<div class="script-form">
  <p>Script:</p>
  <textarea name="sqlScript" class="sql-script" id="sqlScript"></textarea>
  <br>
  <a href="#" class="btn-submit" id="scriptSubmitBtn">提交</a>
</div>
<div class="script-tip-wrap"  id="scriptTip">
  <div class="script-tip">
    <div class="content">
      <span id="tipText"><i class="fa fa-spinner fa-pulse"></i></span>
    </div>
  </div>
</div>
<ul class="added-sql-wrap" id="addUL"></ul>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.1.11.2.min.js"></script>
<script>
  $('#scriptSubmitBtn').click(function(){
    var _sql_text = $('#sqlScript').val().trim();
    if(!_sql_text){
      tip('输入sql脚本');
      return;
    }
    $('#scriptTip').show();
    $.ajax({
      url: 'index.php?act=index&op=submit_script',
      type: 'post',
      data: {text: _sql_text},
      dataType: 'json',
      success: function(ret){
        console.log(ret)
        if(ret.state){
          tip('添加成功');
          $('#addUL').prepend('<li class="item"><span class="time">' + ret.add_time + '</span>' + ret.sql + '</li>');
        }else{
          $('#sqlScript').val(ret.remaining);
          tip('添加失败，请重试');
        }
      }
    });
  });

  function tip(text){
    $('#tipText').html(text);
    $('#scriptTip').show();
    setTimeout(function(){
      $('#scriptTip').hide();
      $('#tipText').html('<i class="fa fa-spinner fa-pulse"></i>');
    },2000);
  }
</script>
