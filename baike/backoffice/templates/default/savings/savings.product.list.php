<?php $product_list = $data; ?>

<table class="table table-hover table-striped table-bordered">
    <tr class="table-header">
        <td>Product Code</td>
        <td>Product Name</td>
        <td>Category Name</td>
        <td>Interest Rate</td>
        <td>Terms</td>
        <td>Supported Currency</td>
        <td>State</td>
        <td>Function</td>
    </tr>
    <?php if ($product_list) { ?>
        <?php foreach ($product_list as $item) { ?>
            <tr>
                <td>
                    <?php echo $item['product_code'] ?>
                </td>
                <td>
                    <?php echo $item['product_name'] ?>
                </td>
                <td>
                    <?php echo $item['category_name'] ?>
                </td>
                <td>
                    <?php echo $item['interest_rate'] . '%(' . ucwords($item['interest_rate_unit']) . ')' ?>
                </td>
                <td class="text-right">
                    <?php
                    if ($item['category_term_style'] == savingsCategoryTermStyleEnum::FIXED) {
                        echo $item['min_terms'] . ' Days';
                    } else if ($item['category_term_style'] == savingsCategoryTermStyleEnum::FREE) {
                        echo 'Min ' . $item['min_terms'] . ' Days';
                    } else {
                        echo $item['min_terms'] . ' - ' . $item['max_terms'] . ' Days';
                    }
                    ?>
                </td>
                <td>
                    <?php echo $item['currency'] ?>
                </td>
                <td>
                    <?php echo $lang['savings_product_state_' . $item['state']] ?>
                </td>
                <td>
                    <a class="btn btn-link btn-xs" href="<?php echo getUrl("savings", "editProductPage", array('uid' => $item['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a class="btn btn-link btn-xs" onclick="deleteProduct(<?php echo $item['uid'] ?>)">
                        <i class="fa fa-edit"></i>
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="9">NO RECORD</td>
        </tr>
    <?php } ?>
</table>