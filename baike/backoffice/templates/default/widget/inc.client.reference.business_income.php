<div role="tabpanel" class="tab-pane" id="business_income">
    <table class="table">
        <tbody class="table-body">
        <?php if (!count($member_industry_info)) { ?>
            <tr>
                <td style="height: 40px">No Record</td>
            </tr>
        <?php } else { ?>
            <table class="table">
                <tbody class="table-body">
                <?php
                $business_research_operator = $reference_info['business_research_operator'];
                $business_image = $reference_info['business_image'];
                $total_profit = 0;
                ?>
                <?php if(count($member_industry_info)){?>
                    <tr>
                        <td colspan="10">
                            <label class="control-label">Total Profit: </label>
                            <label id="total_profit" style="font-size: 16px;font-weight: 600;padding-left: 10px"></label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <ul class="nav nav-tabs" role="tablist">
                                <?php $i = 0;foreach ($member_industry_info as $industry) {?>
                                    <?php if($industry['branch_code']){?>
                                        <?php foreach ($industry['branch_code'] as $branch_code) { ++$i?>
                                            <li role="presentation" class="<?php echo $i == 1 ? 'active' : '' ?>">
                                                <a href="#co_industry_<?php echo $industry['uid']; ?>_<?php echo $i?>"
                                                   aria-controls="co_industry_<?php echo $industry['uid']; ?>_<?php echo $i?>"
                                                   role="tab" data-toggle="tab"
                                                   style="<?php echo $i == 1 ? 'border-left: 0' : '' ?>"><?php echo $industry['industry_name'] . ' -- ' . $branch_code['branch_code']; ?></a>
                                            </li>
                                        <?php } ?>
                                    <?php } else {++$i?>
                                        <li role="presentation" class="<?php echo $i == 1 ? 'active' : '' ?>">
                                            <a href="#co_industry_<?php echo $industry['uid']; ?>"
                                               aria-controls="co_industry_<?php echo $industry['uid']; ?>" role="tab"
                                               data-toggle="tab"
                                               style="<?php echo $i == 1 ? 'border-left: 0' : '' ?>"><?php echo $industry['industry_name']; ?></a>
                                        </li>
                                    <?php }?>
                                <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <?php
                                $i = 0;
                                $count = count($business_research_operator);
                                $width = 85 / $count;
                                foreach ($member_industry_info as $ids_i => $ids_item) {
                                    $industry_income_text = $ids_item['industry_income_text'];
                                    $industry_expense_text = $ids_item['industry_expense_text'];
                                    $industry_text = $ids_item['industry_text'];
                                    $total_profit_industry = 0;
                                    $total_profit_count = 0;
                                    ?>

                                    <?php if ($ids_item['branch_code']) { ?>
                                        <?php foreach ($ids_item['branch_code'] as $branch_code) { ++$i?>
                                            <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="co_industry_<?php echo $ids_item['uid'];?>_<?php echo $i?>">
                                                <table class="table">
                                                    <tbody class="table-body">
                                                    <tr>
                                                        <td><label class="control-label">Survey Item</label></td>
                                                        <?php foreach ($business_research_operator as $co) { ?>
                                                            <td style="width: <?php echo $width ?>%">
                                                                <?php echo $co['officer_name'] ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>

                                                    <tr>
                                                        <td><label class="control-label">Relative</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                            <td>
                                                                <?php foreach ($income_business['owner_list'] as $owner) { ?>
                                                                    <span style="padding-right: 15px"><?php echo $owner['relative_name'] ?></span>
                                                                <?php } ?>
                                                            </td>
                                                        <?php }?>
                                                    </tr>

                                                    <tr>
                                                        <td><label class="control-label">Industry Place</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                            <td>
                                                                <?php echo $income_business['industry_place_text']?>
                                                            </td>
                                                        <?php }?>
                                                    </tr>

                                                    <tr>
                                                        <td><label class="control-label">Employees</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                            <td>
                                                                <?php echo $income_business ? $income_business['employees'] : '';?>
                                                            </td>
                                                        <?php }?>
                                                    </tr>

                                                    <tr>
                                                        <td><label class="control-label">Income</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$branch_code['branch_code']][$ids_i]?>
                                                            <td>
                                                                <em><?php echo $income_business ? ncPriceFormat($income_business['income']) : '';?></em>
                                                            </td>
                                                        <?php }?>
                                                    </tr>

                                                    <?php foreach ($industry_income_text as $key => $val) { ?>
                                                        <tr>
                                                            <td><span class="pl-25"><?php echo $val?></span></td>
                                                            <?php foreach ($business_research_operator as $k => $co) {$income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']] ?>
                                                                <td>
                                                                    <?php
                                                                    $research_text = my_json_decode($income_business['research_text']);
                                                                    $research_text_src = my_json_decode($income_business['research_text_src']);
                                                                    echo $research_text ? ncPriceFormat($research_text[$key]) : ''?>
                                                                    <span class="research_text_src"><?php echo $research_text_src[$key]?></span>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php } ?>

                                                    <tr>
                                                        <td><label class="control-label">Expense</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                            <td>
                                                                <em><?php echo $income_business ? ncPriceFormat($income_business['expense']) : '';?></em>
                                                            </td>
                                                        <?php }?>
                                                    </tr>

                                                    <?php foreach ($industry_expense_text as $key => $val) { ?>
                                                        <tr>
                                                            <td><span class="pl-25"><?php echo $val?></span></td>
                                                            <?php foreach ($business_research_operator as $k => $co) {$income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']] ?>
                                                                <td>
                                                                    <?php
                                                                    $research_text = my_json_decode($income_business['research_text']);
                                                                    $research_text_src = my_json_decode($income_business['research_text_src']);
                                                                    echo $research_text ? ncPriceFormat($research_text[$key]) : ''?>
                                                                    <span class="research_text_src"><?php echo $research_text_src[$key]?></span>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php } ?>

                                                    <tr>
                                                        <td><label class="control-label">Profit</label></td>
                                                        <?php foreach ($business_research_operator as $co) {
                                                            $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']];
                                                            if ($income_business['profit']) {
                                                                $total_profit_industry += round($income_business['profit'], 2);
                                                                ++$total_profit_count;
                                                            }
                                                            ?>
                                                            <td>
                                                                <em><?php echo $income_business ? ncPriceFormat($income_business['profit']) : '';?></em>
                                                            </td>
                                                        <?php }?>
                                                        <?php $total_profit += round($total_profit_industry / $total_profit_count, 2) ?>
                                                    </tr>

                                                    <?php foreach ($industry_text as $key => $val) { ?>
                                                        <tr>
                                                            <td><label class="control-label"><?php echo $val['name']?></label></td>
                                                            <?php foreach($business_research_operator as $k => $co){ $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']];$research_text = my_json_decode($income_business['research_text']);?>
                                                                <td>
                                                                    <?php if($val['type'] == 'checkbox') {?>
                                                                        <?php if($research_text) {?>
                                                                            <span class="col-second"><i class="fa fa-<?php echo $research_text[$key] == 1 ? 'check' : 'close'; ?>" aria-hidden="true"></i></span>
                                                                        <?php }?>
                                                                    <?php } else {?>
                                                                        <span><?php echo $research_text ? $research_text[$key] : ''?></span>
                                                                    <?php }?>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td><label class="control-label">Address</label></td>
                                                        <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                            <td>
                                                                <?php echo $income_business['address_detail'];?>
                                                                <?php if(count($income_business)){?>
                                                                    <a href="javascript:void(0)" onclick="showMyGoogleMap('<?php echo $income_business['coord_x']?>','<?php echo $income_business['coord_y']?>')" style="font-style: italic">Google Map</a>
                                                                <?php }?>

                                                            </td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php foreach ($business_research_operator as $co) { $income_business = $co_research[$co['officer_id']][$ids_i][$branch_code['branch_code']]?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $co['officer_name']?>
                                                            </td>
                                                            <td colspan="20">
                                                                <?php if ($income_business['image_list']) { ?>
                                                                    <?php
                                                                    $viewer_width = "1000";
                                                                    $image_version=imageThumbVersion::MAX_240;
                                                                    $image_list = array();
                                                                    foreach ($income_business['image_list'] as $img_item) {
                                                                        $image_list[] = array(
                                                                            'url' => $img_item['image_url'],
                                                                            'image_source' => $img_item['image_source'],
                                                                        );
                                                                    }
                                                                    include(template(":widget/item.image.viewer.list"));
                                                                    ?>
                                                                <?php } else { ?>
                                                                    No Image
                                                                <?php } ?>
                                                            </td>
                                                        </tr>

                                                    <?php }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } ?>
                                    <?php } else {++$i?>
                                        <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : '' ?>"
                                             id="co_industry_<?php echo $ids_item['uid']; ?>">
                                            <table class="table">
                                                <tbody class="table-body">
                                                <tr>
                                                    <td>No Record<td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php }?>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $(function () {
        var total_profit = '<?php echo ncPriceFormat($total_profit);?>';
        $('#total_profit').text(total_profit);
    })
</script>