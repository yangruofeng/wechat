<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td style="width: 70px"></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Department';?></td>
            <td><?php echo 'Point';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i == 0;foreach($data['data'] as $row) {
            ++$i;
            $odd = $i % 2 == 1 ? 'tr_odd' : '';
            ?>
            <tr class="<?php echo $odd?>" user_id="<?php echo $row['uid']?>">
                <td style="text-align: center">
                    <i class="fa fa-plus" uid="<?php echo $row['uid']?>"></i>
                </td>
                <td>
                    <?php echo $row['user_name'] ?>
                </td>
                <td>
                    <?php echo $row['branch_name'] . " " . $row['depart_name']?>
                </td>
                <td class="point-total">
                    <?php echo ncPriceFormat($row['point']) ?>
                </td>
            </tr>

            <tr class="point-list point-list-<?php echo $row['uid']?> define-item-title <?php echo $odd?>">
                <td></td>
                <td>
                    <?php echo 'Event Type' ?>
                </td>
                <td>
                    <?php echo 'Event Code' ?>
                </td>
                <td>
                    <?php echo 'Point' ?>
                </td>
            </tr>
            <?php $type_new = '';foreach($row['point_list'] as $event_id => $point){?>
                <tr class="point-list point-list-<?php echo $row['uid']?> <?php echo $odd?>">
                    <td></td>
                    <td>
                        <?php
                        if ($point['is_system']) {
                            $type = 'System Event';
                        } else {
                            $type = 'Evaluation Event';
                        };
                        if ($type != $type_new) {
                            echo $type;
                        }
                        $type_new = $type;
                        ?>
                    </td>
                    <td>
                        <?php echo $point['event_code'] ?>
                    </td>
                    <td class="point-<?php echo $row['uid'] . '-' . $event_id;?>">
                        <?php echo ncPriceFormat($point['point']);?>
                    </td>
                </tr>
            <?php }?>
            <tr class="point-list point-list-<?php echo $row['uid']?> <?php echo $odd?>">
                <td colspan="4"></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

