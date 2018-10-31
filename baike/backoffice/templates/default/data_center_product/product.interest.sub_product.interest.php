<table class="table table-bordered table-no-background category-info" style="margin-top: 20px">
    <tr>
        <td><label for="">Default Repayment</label></td>
        <td><?php echo $output['category']['default_repayment'];?></td>
        <td><label for="">Default Interest Package</label></td>
        <td><?php echo $output['category']['default_package'];?></td>
        <td><label for="">Is One Time</label></td>
        <td>
            <?php if ($output['category']['is_one_time']) { ?>
                <span style="color: #008000"><i class="fa fa-check"></i></span>
            <?php } else { ?>
                <span style="color: red"><i class="fa fa-close"></i></span>
            <?php } ?>
        </td>
    </tr>
</table>
<ul class="list-group">
    <?php foreach ($output['list'] as $prod) {
        if ($prod['state'] != loanProductStateEnum::ACTIVE || count($prod['size_rate']) == 0) {
            continue;
        }
        ?>
        <li class="list-group-item">
            <label style="width: 300px" class="text-right">
                <span style="width: 200px;"><?php echo $prod['sub_product_name'] ?></span>
                <a class="btn btn-link" style="padding-left: 100px" role="button" data-toggle="collapse" href="#div_rate_list_<?php echo $prod['uid']?>" aria-expanded="true" aria-controls="div_rate_list_<?php echo $prod['uid']?>">
                    <i class="fa fa-angle-down"></i>
                </a>
            </label>
            <?php
            $size_rate = $prod['size_rate'];
            include(template("data_center_product/size_rate.list"))
            ?>
        </li>
    <?php } ?>
</ul>
