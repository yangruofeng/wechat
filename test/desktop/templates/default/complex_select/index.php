<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-3.3.4/css/bootstrap.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js"></script>

<input style="width: 300px;" name="user_name" id="user_name" type="text" class="form-control">
<input name="uid" type="hidden" id="uid">
<button class="btn btn-primary btn-flat md-trigger" data-modal="form-primary">Click</button>

<script>
    $('.btn-flat').click(function () {
        complexSelect.create({
            _title: 'Complex Select',
            _width: '900px',
            _height: '800px',
            _url: 'http://localhost/microbank/component/desktop/?act=complex_select&op=index',
            _callback: function(id,text){
                $('#user_name').val(text);
                $('#uid').val(id);
            }
        })
    })
</script>
