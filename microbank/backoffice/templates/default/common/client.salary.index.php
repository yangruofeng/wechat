<?php
$salary_income = $output['salary_income'];
?>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Company Name</td>
                            <td>Company Phone</td>
                            <td>Position</td>
                            <td>Salary</td>
                            <td>Operator</td>
                            <td>Images</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if ($salary_income){ ?>
                            <?php foreach ($salary_income as $k => $v) { ?>
                                <tr>
                                    <td><?php echo $v['company_name']?></td>
                                    <td><?php echo $v['company_phone']?></td>
                                    <td><?php echo $v['position']?></td>
                                    <td><?php echo $v['salary'] ? ncPriceFormat($v['salary']) : '0'; ?></td>
                                    <td><?php echo $v['update_operator_name'] ?: $v['operator_name'] ; ?></td>
                                    <td>
                                        <?php
                                        $image_list=array();
                                        foreach($v['image_list'] as $img_item){
                                            $image_list[]=$img_item['image_url'];
                                        }
                                        include(template(":widget/item.image.viewer.list"));
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6">
                                    <?php include(template(":widget/no_record")); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>