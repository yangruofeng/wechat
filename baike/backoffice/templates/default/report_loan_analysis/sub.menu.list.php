

<div class="fixed-bar">
    <div class="item-title">
        <h3>Loan Analysis</h3>
        <ul class="tab-base">
            <?php foreach( $output['sub_menu_list'] as $menu_value){  ?>
                <li>
                    <?php if( $menu_value['is_active']){ ?>
                        <a class="current">
                            <span>
                                <?php echo $menu_value['title']; ?>
                            </span>
                        </a>
                    <?php }else{ ?>
                        <a href="<?php echo $menu_value['url']; ?>">
                            <span>
                                <?php echo $menu_value['title']; ?>
                            </span>
                        </a>
                    <?php } ?>
                </li>
            <?php } ?>
         </ul>
        <div class="export-div">
            <a onclick="exportExcel()" class="export-excel" title="Excel"><i class="fa-file-excel-o"></i></a>
            <a onclick="printPage()" class="export-pdf" title="Print"><i class="fa-file-pdf-o"></i></a>
        </div>
    </div>
</div>