<div class="row" style="margin: 0">
    <form class="form-horizontal" id="frm_edit" style="padding: 50px">
        <input type="hidden" name="module_code" value="<?php echo $data['module_code']?>">
        <input type="hidden" name="platform" value="<?php echo $data['platform']?>">
        <p>
            <span>Module-Code</span>
            <label><?php echo $data['module_code']?></label>
        </p>
        <p>
            <span>Platform</span>
            <label><?php echo $data['platform']?></label>
        </p>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_close" <?php if($data['is_close']) echo 'checked'?>> Close
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_show" <?php if($data['is_show']) echo 'checked'?>> Show
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_new" <?php if($data['is_new']) echo 'checked'?>> New
            </label>
        </div>
        <div class="form-group" style="padding: 20px">
            <button type="button" class="btn btn-primary btn-sm" onclick="submitEditModuleSetting()">Submit</button>

            <button type="button" class="btn btn-sm btn-default" onclick="javascript:yo.dialog.close();">Close</button>
        </div>
    </form>
</div>
<script>
    function submitEditModuleSetting(){
        var _values=$("#frm_edit").getValues();
        $(document).waiting();
        yo.loadData({
            _c:"dev",
            _m:"submitModuleEntranceSetting",
            param:_values,
            callback:function(_o){
                yo.dialog.close();
                $(document).unmask();
                if(!_o.STS){
                    alert(_o.MSG);
                }else{
                    alert("Edit Success");
                }
                window.location.reload();
            }
        });

    }
</script>
