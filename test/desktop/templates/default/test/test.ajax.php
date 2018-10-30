<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi File</title>
</head>
<body>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
<script>
    //1.$.ajax
    $.ajax({
        url: '<?php echo getUrl('test', 'ajaxHandle', array(), false, DESKTOP_SITE_URL)?>',
        type: 'post',
        data: {uid: 1},
        dataType: 'json',//注释这行 看效果
        success: function (ret) {
            console.log(ret['a']);
        }
    });

//    //2.$.post $.get一样
//    $.post('<?php //echo getUrl('test', 'ajaxHandle', array(), false, DESKTOP_SITE_URL)?>//', {uid: 123}, function (ret) {
//        console.log(ret);
//
//        var obj = JSON.parse(ret);//json字符串转变为json对象
//        console.log(obj);
//    })
//
    //3.$.post json $.get一样
//    $.post('<?php //echo getUrl('test', 'ajaxHandle', array(), false, DESKTOP_SITE_URL)?>//', {uid: 123}, function (ret) {
//        console.log(ret);
//    }, 'json')
</script>
</body>
</html>