<div class="data-center-btn">
    <?php foreach ($output['sub_product_list'] as $sub_product) { ?>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_detail_op(this, <?php echo $sub_product['uid']?>)"><?php echo $sub_product['sub_product_name']?></button>
    <?php } ?>
</div>