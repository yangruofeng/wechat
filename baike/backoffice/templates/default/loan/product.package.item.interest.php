<div class="page">
    <?php if(!$output['is_readonly']){?>
        <div class="fixed-bar">
            <div class="item-title">
                <h3>Interest Package</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('loan', 'productPackagePage', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Package</span></a></li>
                    <li><a class="current"><span>Special Interest Of <?php echo $output['package_name']?></span></a></li>
                </ul>
            </div>
        </div>
    <?php }?>

    <div class="container">
        <div>
            <span style="color: #0000ff">TIP: The row of red background is sepcial setting</span>
        </div>
        <div class="business-content">
            <ul class="list-group">
                <?php foreach($output['list'] as $prod){
                    if($prod['state']!=loanProductStateEnum::ACTIVE){
                        continue;
                    }
                    ?>
                    <li class="list-group-item list-group-item-info">
                        <label><?php echo $prod['sub_product_name']?></label>
                        <?php
                        $data=array("data"=>$prod['size_rate'],"type"=>'info');
                        include(template("loan/size_rate.list"))
                        ?>
                    </li>
                <?php }?>

            </ul>

        </div>
    </div>
</div>
