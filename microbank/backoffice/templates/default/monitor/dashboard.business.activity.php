<?php $business_activity = $data['data']; ?>
<ul class="col-sm-12 business-activity">
    <?php foreach ($business_activity as $val) { ?>
        <li class="row">
            <span class="title"><?php echo $val['title'] ?></span>
            <span class="count"><?php echo formatQuantity($val['count']) ?></span>
        </li>
    <?php } ?>
</ul>