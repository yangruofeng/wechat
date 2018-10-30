<tr class="tr-approve-item" data-biz-code="<?php echo $data['biz_code']?>" data-biz-id="<?php echo $data['uid']?>">
    <td style="width: 150px">
       <?php
        $image_item=$data['member_icon'];
        require(template(":widget/item.image.viewer.item"));
       ?>
    </td>
    <td>
        <ul>
            <li><label><?php echo strtoupper($data['member_code'])?></label></li>
            <li><?php echo $data['display_name']?></li>
            <li><?php echo $data['phone_id']?></li>
        </ul>
    </td>
    <td>
        <ul>
            <li>
                <label><?php echo strtoupper($data['cashier_code'])?></label>
            </li>
            <li><?php echo $data['cashier_name']?></li>
            <li>
                <?php echo $data['create_time']?>
            </li>
        </ul>
    </td>
    <td>
        <ul>
            <?php foreach($data['cash'] as $cash_item){?>
                <li>
                    <?php echo $cash_item['currency']?>
                    <label><?php echo $cash_item['amount']?></label>
                </li>
            <?php }?>
        </ul>
    </td>
    <td style="width: 150px">
        <?php
        $image_item=$data['member_image'];
        require(template(":widget/item.image.viewer.item"));
        ?>
    </td>
    <?php if(!$data['without_function']){?>
        <td>
            <a class="btn btn-primary" style="width: 200px" href="<?php echo getUrl('member_index','bizApproveDetail',array('biz_code'=>$data['biz_code'],'biz_id'=>$data['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                GET
            </a>
        </td>
    <?php }?>
</tr>