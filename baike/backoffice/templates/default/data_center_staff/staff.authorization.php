<style>
    .business-condition {
        margin-bottom: 10px;
    }
</style>
<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$auth_group = $output['auth_group'];
?>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="content">
                <table id="table_left" class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Allow Auth</th>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php foreach( $auth_group as $k => $v ){ ?>
                            <tr>
                                <td>
                                    <?php
                                        echo str_pad('',(3-1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                                        $k;
                                    ; ?>
                                </td>
                            </tr>
                            <?php foreach( $v as $cv ){ ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo str_pad('',(4  -1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                                            $cv;
                                        ; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
</script>
