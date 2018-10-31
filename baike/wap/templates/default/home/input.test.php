<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=7">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=1">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-search-btn" onclick="window.location.href='<?php echo getUrl('home', 'suggestHistory', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>
<style>
html, body {
        padding: 0;
        margin: 0;
      }
      header {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        width: 100%;
        height: 50px;
        line-height: 50px;
        font-size: 18px;
        text-align: center;
        background: #ccc;
      }
      main {
        
        /* 使之可以滚动 */
        overflow-y: scroll;
        /* 增加该属性，可以增加弹性，是滑动更加顺畅 */
        -webkit-overflow-scrolling: touch;
      }
      footer {
        
        border-top: 1px solid #e6e6e6;
      }
      footer input {
        display: inline-block;
        width: 90%;
        height: 20px;
        font-size: 14px;
        outline: none;
        border: 1px solid #e6e6e6;
        border-radius: 5px;
      }
</style>
<div class="container">
<div>
     <header>
      This is the header
    </header>
    <main>
      <h1>title</h1>
      <p>Welcome to</p>
      <ul>
        <li>Today</li>
        <li>is</li>
        <li>Sunday</li>
        <li>And</li>
        <li>I</li>
        <li>have</li>
        <li>to</li>
        <li>go</li>
        <li>to</li>
        <li>work</li>
        <li>tomorrow</li>
        <li>Today</li>
        <li>is</li>
        <li>Sunday</li>
        <li>And</li>
        <li>I</li>
        <li>have</li>
        <li>to</li>
        <li>go</li>
        <li>to</li>
        <li>work</li>
        <li>tomorrow</li>
      </ul>
    </main>
    <footer id="sendText">
      <input type="text" id="textInput"  placeholder="Type here1...">
      <input type="text" id="textInput"  placeholder="Type here2...">
      <input type="text" id="textInput"  placeholder="Type here3...">
    </footer>
    <div id="blankDiv" style="width:100%;height: 350px;display:none;"></div>
  </div>
</div>
<script>
//防止键盘把当前输入框给挡住
$(function () {
    //键盘弹起时为键盘高度，未弹起时为0
    
    var iHeight = window.innerHeight, halfH = iHeight/2;
    $('input[type="text"],textarea').on('focus', function () {
        var iTop = $(this).offset().top;
        if(iTop < halfH)  return;
        $('#blankDiv').show();
        window.scrollTo(0,iTop-100)
    });
    $('input[type="text"],textarea').on('blur', function () {
        var iTop = $(this).offset().top;
        if(iTop < halfH)  return;
        $('#blankDiv').hide();
    });
})

</script>