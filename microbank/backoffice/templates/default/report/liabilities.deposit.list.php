<table class="table">
    <thead>
        <tr>
            <th>Member Name</th>
            <th>CID</th>
            <th>Phone</th>
            <th class="c-usd">USD</th>
            <th class="c-khr">KHR</th>
            <th>Handle</th>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $item) { ?>
                <tr>
                    <td><?php echo $item['login_code'];?></td>
                    <td><?php echo $item['obj_guid'];?></td>
                    <td><?php echo $item['phone_id'];?></td>
                    <td class="c-usd"><em><?php echo ncPriceFormat($item['children']['USD']) ?></em></td>
                    <td class="c-khr"><em><?php echo ncPriceFormat($item['children']['KHR']) ?></em></td>
                    <td>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('report', $data['flow'], array('uid'=>$item['mid'], 'pid'=>$item['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-vcard-o"></i>Flow</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="8">Null</td></tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
