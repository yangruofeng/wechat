<style>
   .business-list .table tr td {
       width: 16.66%;
   }
</style>
<?php
$register_by = $output['detail'];
?>
<div class="row" style="max-width: 1000px">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table table-bordered">
                        <tr>
                            <td><label class="control-label">Creator Name(Id)</label></td>
                            <td>
                                <?php echo $register_by['creator_name']; ?>
                                <span class="green"> (<?php echo $register_by['creator_id']; ?>)</span>
                            </td>
                            <td><label class="control-label">Open Source</label></td>
                            <td><?php echo $lang['source_type_' . $register_by['open_source']]; ?></td>
                        </tr>

                        <tr>
                            <td><label>Address</label></td>
                            <td colspan="3"><?php echo $register_by['open_addr']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>