<link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL.'/font/font-awesome/css/font-awesome.css'; ?>">
<style>
html, body, ul, li{padding: 0;margin: 0;}
body {font-size: 14px;}
p {margin: 0;padding: 0;}
textarea {outline: none;}
ul, li {list-style: none;}
.script-tip-wrap {display: none;position: absolute;top: 0;left: 0;width: 100%;height: 100%;}
.script-tip {height: 100%;position: relative;z-index: 9999;}
.script-tip .content {width: 100px;margin: 0 auto;text-align: center;padding: 15px 0;background: rgba(0, 0, 0, .4);color: #fff;border-radius: 4px;position: absolute;top: 50%;left: 50%;margin-top: -112px;margin-left: -50px;}
.script-tip span i {font-size: 22px;}
.sql-list {margin: 50px;font-size: 12px;line-height: 18px;}
.sql-list .item {position: relative;padding: 10px 15px 10px 105px;border: 1px solid #ddd;margin-top: -1px;min-height: 38px;}
.sql-list .item .time {position: absolute;left: 15px;top: 10px;display: inline-block;width: 80px;text-align: center;color: #909090;}
</style>
<p></p>
<a href="index.php?act=index&op=index">Record</a>
<ul class="sql-list">
    <?php if (count($output['list'])) { ?>
        <?php foreach ($output['list'] as $key => $value) {?>
            <li class="item">
                <span class="time"><?php echo date('Y-m-d H:i:s', $value['add_time']);?></span>
                <?php echo $value['sql'];?>
                <span class="act"><a href="index.php?act=index&op=skip&from=<?php echo $output['local_ver'] ?>&to=<?php echo $value['uid'] ?>">Skip To</a></span>
            </li>
        <?php } ?>
    <?php } else { ?>
        <li class="item">
            数据库已经更新到最新
        </li>
    <?php } ?>
    <?php if ($output['error']) { ?>
        <li><?php echo $output['error']; ?></li>
    <?php } ?>
    <li style="text-align: right">
        <form action="index.php?act=index&op=update" method="post">
            <input type="hidden" name="ver" value="<?php echo $output['local_ver'] ?>" />
            <input type="submit" value="Update" />
        </form>
    </li>
</ul>
<div class="script-tip-wrap"  id="scriptTip">
  <div class="script-tip">
    <div class="content">
      <span id="tipText"><i class="fa fa-spinner fa-pulse"></i></span>
    </div>
  </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.1.11.2.min.js"></script>
<script>
  function tip(text){
    $('#tipText').html(text);
    $('#scriptTip').show();
    setTimeout(function(){
      $('#scriptTip').hide();
      $('#tipText').html('<i class="fa fa-spinner fa-pulse"></i>');
    },2000);
  }
</script>
