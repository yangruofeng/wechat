<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-3.3.4/css/bootstrap.min.css?v=1" rel="stylesheet" />
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/validate.css?v=4" rel="stylesheet" />
<style>
.form-wrap {width: 500px;margin: 50px auto;}
p.title {margin-bottom: 30px;font-size: 16px;text-align: center;font-weight: 600;}
</style>

<div class="form-wrap">
  <p class="title">表单验证</p>
  <form class="demoform" id="validform" action="index.php?act=form_validate&op=valid" method="post">
    <input type="hidden" name="validate" value="" />
    <div class="form-group">
      <label class="control-label" for="username">用户名</label>
      <input type="text" class="form-control" name="username" id="username" />
      <span class="validate-checktip"></span>
    </div>
    <!--<div class="form-group">
      <label class="control-label" for="username">用户名实时验证</label>
      <input type="text" class="form-control" name="uname" id="uname" />
      <span class="validate-checktip"></span>
    </div>-->
    <div class="form-group">
      <label class="control-label" for="password">密码</label>
      <input type="password" class="form-control" name="password" id="password" />
      <span class="validate-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="password1">确认密码</label>
      <input type="password" class="form-control" name="repassword" id="repassword" />
      <span class="validate-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="mobile">手机号码</label>
      <input type="text" class="form-control" name="mobile" id="mobile" />
      <span class="validate-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="email">邮箱</label>
      <input type="text" class="form-control" name="email" id="email" />
      <span class="validate-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="regexp">正则表达式(匹配中文字符)</label>
      <input type="text" class="form-control" name="regexp" id="regexp" />
      <span class="validate-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="regexp2">正则表达式(匹配2-10位英文字母、数字或者下画线！)</label>
      <input type="text" class="form-control" name="regexp2" id="regexp2" />
      <span class="validate-checktip"></span>
    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" name="agree" id="agree" /> Check me out
        <span class="validate-checktip"></span>
      </label>
    </div>
    <div class="form-group">
      <label class="control-label" for="captcha">验证码</label>
      <input type="text" class="form-control" name="captcha" id="captcha" value="" />
      <!--<img src="index.php?act=seccode&op=makecode&admin=1&nchash=<?php echo getNchash();?>" name="codeimage" id="codeimage" border="0"/>-->
      <span class="validate-checktip"></span>
    </div>
    <!--
    <div class="form-group">
      <label class="control-label" for="password1">确认密码</label>
      <input type="password" class="form-control" name="repassword" id="repassword" recheck="password" datatype="*6-15" nullmsg="请再输入一次密码！" errormsg="您两次输入的账号密码不一致！" />
      <span class="validform-checktip"></span>
    </div>
    <div class="form-group">
      <label class="control-label" for="password">验证密码强度</label>
      <input type="password" class="form-control" name="pwd" id="pwd" plugin="passwordStrength" datatype="*6-15" nullmsg="请设置密码！" errormsg="密码范围在6~15位之间" />
      <span class="validform-checktip"></span>
      <div class="passwordStrength" style="display:none;"><b>密码强度：</b> <span>弱</span><span>中</span><span class="last">强</span></div>
    </div>

    -->
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
  </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/jquery.validate.min.js?v=1"></script>
<!--验证密码强度引入-->
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/passwordStrength-min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=6"></script>
<script>
//validform('表单ID', true);
var validParam = {
  ele: '#validform', //表单id
  params: [{
    field: 'username',
    rules: {
      required: true,
      minlength: 3,
      maxlength: 10,
      remote: {
          url: 'index.php?act=form_validate&op=check_member&column=ok',
          type: 'get',
          data: {
              username: function () {
                  return $('#username').val();
              }
          }
      }
    },
    messages: {
      required: '请输入用户名!',
      minlength: '用户名在3-10个字符之间!',
      maxlength: '用户名在3-10个字符之间!',
      remote: '用户名已存在'
    }
  },{
    field: 'password',
    rules: {
      required: true,
      minlength: 6,
      maxlength: 15
    },
    messages: {
      required: '请设置密码！',
      minlength: '密码范围在6~15位之间!',
      maxlength: '密码范围在6~15位之间!'
    }
  },{
    field: 'repassword',
    rules: {
      required: true,
      equalTo: '#password'
    },
    messages: {
      required: '请再输入一次密码！',
      equalTo: '您两次输入的账号密码不一致！'
    }
  },{
    field: 'mobile',
    rules: {
      required: true,
      number: true
    },
    messages: {
      required: '请输入您的手机号码！',
      number: '请输入有效的手机号码！'
    }
  },{
    field: 'email',
    rules: {
      required: true,
      email: true
    },
    messages: {
      required: '请输入正确的邮箱地址！',
      email: '请输入正确的邮箱地址！'
    }
  },{
    field: 'regexp',
    rules: {
      required: true,
      regexp: /^[\x4e00-\x9fa5]+$/,
      checkChinese: true,
      regexpFun: 'checkChinese'
    },
    messages: {
      required: '请输入中文字符',
      regexp: '请输入汉字'
    }
  },{
    field: 'regexp2',
    rules: {
      required: true,
      regexp: /^\w{2,10}$/,
      checkRegexp2: true,
      regexpFun: 'checkRegexp2'
    },
    messages: {
      required: '请输入2-10位英文字母、数字或者下画线',
      regexp: '只允许2-10位英文字母、数字或者下画线！'
    }
  },{
    field: 'agree',
    rules: {
      required: true
    },
    messages: {
      required: '请接受我们的声明'
    }
  }]
};
validform(validParam);
</script>
