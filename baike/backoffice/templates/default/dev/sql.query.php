<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>SQL Query</h3>
            <ul class="tab-base">
                <li><a class="current"><span>SQL</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post" id="frm_sql_query" >
            <input type="hidden" name="form_submit" value="ok">

            <input type="hidden" name="act" value="dev">
            <input type="hidden" name="op" value="sqlQuery">


            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'SQL'?></label>
                <div class="col-sm-9">
                    <textarea name="sql_desc" class="form-control" id="" cols="30" rows="10"></textarea>
                    <div class="error_msg"></div>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger" id="btn_submit"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                    <input type="reset" class="btn btn-default"  value="Cancel">
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    $(function(){

        $('#btn_submit').click(function(){
            $('#frm_sql_query').submit();
        });
    });

</script>