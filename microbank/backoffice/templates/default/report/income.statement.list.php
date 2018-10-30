<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th class="c-usd">USD</th>
            <th class="c-khr">KHR</th>
            <th>Handle</th>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $item) { ?>
                <tr>
                    <td><?php echo $item['create_time'];?></td>
                    <td class="c-usd amount">
                        <a href="<?php echo getUrl('report', 'showIncomeStatementFlowPage', array('time'=>$item['create_time'],'currency'=>'USD'), false, BACK_OFFICE_SITE_URL)?>">
                            <em><?php echo ncPriceFormat($item['children']['USD']) ?></em>
                        </a>
                    </td>
                    <td class="c-khr amount">
                        <a href="<?php echo getUrl('report', 'showIncomeStatementFlowPage', array('time'=>$item['create_time'],'currency'=>'KHR'), false, BACK_OFFICE_SITE_URL)?>">
                            <em><?php echo ncPriceFormat($item['children']['KHR']) ?></em>
                        </a>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('report', 'showIncomeStatementFlowPage', array('time'=>$item['create_time']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-vcard-o"></i>Flow</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="4">Null</td></tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
