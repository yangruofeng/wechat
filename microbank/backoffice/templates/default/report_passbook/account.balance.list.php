<div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr class="table-header">
                <td class="number">No</td>
                <td class="number">ID</td>
                <td class="number">Book Code</td>
                <td class="number">Book Name</td>
                <td class="number">Book Type</td>
                <td class="number">State</td>
                <td class="number">Currency</td>
                <td class="number">Balance</td>
                <td class="number">Function</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php $list = $data['data'];?>
            <?php foreach ($list as $k => $v) { $first = $v['child'][0];?>
                <tr>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['no'];?></td>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['uid']; ?></td>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['book_code']; ?></td>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['book_name']; ?></td>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['book_type']; ?></td>
                    <td rowspan="<?php echo $v['count'];?>" class="number"><?php echo $v['state']; ?></td>
                    <td class="number"><?php echo $first['currency'];?></td>
                    <td class="currency"><?php echo ncPriceFormat($first['balance']);?></td>
                    <td class="number">
                        <div class="custom-btn-group">
                            <a title="" class="btn btn-link btn-xs" href="<?php echo getUrl('common', 'passbookAccountFlowPage', array('account_id'=>$first['account_id']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-vcard-o"></i>Flow</span>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php  foreach($v['child'] as $fk =>$fv){ if($fk > 0){ ?>
                    <tr>
                        <td class="number"><?php echo $fv['currency'];?></td>
                        <td class="currency"><?php echo ncPriceFormat($fv['balance']);?></td>
                        <td class="number">
                            <div class="custom-btn-group">
                                <a title="" class="btn btn-link btn-xs" href="<?php echo getUrl('common', 'passbookAccountFlowPage', array('account_id'=>$fv['account_id']), false, BACK_OFFICE_SITE_URL)?>">
                                    <span><i class="fa fa-vcard-o"></i>Flow</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php }}?>

            <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>