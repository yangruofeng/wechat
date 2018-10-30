
<?php $page_data = $output['asset_page_data']; ?>

<div class="page">
    <form role="form" action="" method="post" class="form form-horizontal" enctype="multipart/form-data">

        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" name="type" value="<?php echo $page_data['asset_type']; ?>">
        <input type="hidden" name="relative_id" value="0">

        <table class="table table-no-background">
            <tr>
                <td>
                    <label for="" class="form-label">
                        Asset Type
                    </label>
                </td>
                <td>
                    <?php echo $page_data['type_name']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="form-label">
                        Member ID
                    </label>
                </td>
                <td>
                    <input class="form-control" type="text" name="member_id">
                </td>
            </tr>
            <?php foreach( $page_data['input_field_list'] as $v ){ ?>
                <tr>
                    <td>
                        <label for="" class="form-label">
                            <?php if($v['is_required']){ ?>
                            <span style="color:red;">*</span>
                            <?php } ?>
                            <?php echo $v['field_label']; ?>
                        </label>
                    </td>
                    <td>
                        <?php if( $v['field_type'] == 'input' ){ ?>
                            <input type="text" class="form-control" name="<?php echo $v['field_name']; ?>">
                        <?php }elseif( $v['field_type'] == 'select' ){ ?>
                            <select class="form-control" name="<?php echo $v['field_name']; ?>" id="">
                                <?php foreach( $v['select_list'] as $vv ){ ?>
                                    <option value="<?php echo $vv['value']; ?>"><?php echo $vv['name']; ?></option>
                                <?php } ?>
                            </select>
                        <?php }elseif( $v['field_type'] == 'checkbox' ){ ?>
                            <input class="form-control" type="checkbox" name="<?php echo $v['field_name']; ?>" value="1">
                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>

            <?php foreach( $page_data['upload_image_list'] as $item ){ ?>
                <tr>
                    <td>
                        <label for="" class="form-label">
                            <?php if($item['is_required']){ ?>
                                <span style="color:red;">*</span>
                            <?php } ?>
                            <?php echo $item['filed_label']; ?>
                        </label>
                    </td>
                    <td>
                        <input class="form-control" type="file"  name="<?php echo $item['field_name']; ?>">

                        <div>
                            <img src="<?php echo $item['sample_image']; ?>" alt="" width="80" height="80">
                        </div>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <td></td>
                <td>
                    <input type="submit" class="btn btn-danger" value="Submit">
                </td>
            </tr>
        </table>
    </form>
</div>
