<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<?php
    $default_rate=$output['size_rate'];

?>
<style>
    .tr-package-input td input{
        width: 70px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'editSubProduct', array("uid"=>$output['product_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Sub Product</span></a></li>
                <li><a class="current"><span>Special Rate</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <div>
                <span>Product Name</span>
                <label><?php echo $output['sub_product']['sub_product_name']?></label>
            </div>
            <div>
               <?php
               $data=array("type"=>"info","data"=>array($output['size_rate']));
               include_once(template("loan/size_rate.list"));?>
            </div>
        </div>
        <div style="padding: 10px">
            <label>Special Rate For Product Package</label>
        </div>
        <div class="business-content">
            <div class="business-list">
                <form method="post" action="<?php echo getUrl("loan","submitSpecialRateSetting",array(),false,BACK_OFFICE_SITE_URL)?>">
                    <input type="hidden" name="sub_product_id" value="<?php echo $output['product_id']?>">
                    <input type="hidden" name="size_rate_id" value="<?php echo $output['size_rate_id']?>">
                    <div>
                        <table class="table">
                            <thead>
                            <tr class="table-header">

                                <td rowspan="2">Package</td>
                                <td rowspan="2">Min Amount</td>
                                <td rowspan="2">Max Amount</td>
                                <td rowspan="2">Min Days</td>
                                <td rowspan="2">Max Days</td>
                                <!--
                                <td rowspan="2"><?php echo 'Admin Fee';?></td>
                                <td rowspan="2"><?php echo 'Loan Fee';?></td>
                                -->

                                <td colspan="4" align="center">
                                    Interest Rate
                                </td>
                                <td colspan="4" align="center">Operate Fee</td>

                            </tr>
                            <tr class="table-header">
                                <td>No mortgage</td>
                                <td>MortgageSoft</td>
                                <td>MortgageHard</td>
                                <td>Min value</td>
                                <td>No mortgage</td>
                                <td>MortgageSoft</td>
                                <td>MortgageHard</td>
                                <td>Min value</td>

                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <?php foreach($output['package_list'] as $package_id=>$package_item){
                                $special_rate=$output['special_rate_list'][$package_id];
                                ?>
                                <tr class="tr-package-input">
                                    <td>
                                        <?php echo $package_item['package']?>
                                        <input type="hidden" name="package_id[]" value="<?php echo $package_id?>">
                                    </td>
                                    <td>
                                        <?php echo $default_rate['loan_size_min']; ?>
                                    </td>
                                    <td>
                                        <?php echo $default_rate['loan_size_max']; ?>
                                    </td>
                                    <td>
                                        <?php echo $default_rate['min_term_days']; ?>
                                    </td>
                                    <td>
                                        <?php echo $default_rate['max_term_days']; ?>
                                    </td>
                                    <!--
                                    <td>
                                        <input type="number" step="0.01"  name="package_admin_fee[]" value="<?php echo $special_rate?$special_rate['admin_fee']:$default_rate['admin_fee']?>">
                                        <?php echo $default_rate['admin_fee_type'] == 1 ? '$' : '%';  ?>
                                        <br/>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" name="package_loan_fee[]" value="<?php echo $special_rate?$special_rate['loan_fee']:$default_rate['loan_fee']?>">
                                        <?php echo $default_rate['loan_fee_type'] == 1 ? '$'  :  '%';  ?>
                                        <br/>
                                    </td>
                                    -->

                                    <td>
                                        <?php echo $special_rate?$special_rate['interest_rate']:$default_rate['interest_rate']?>
                                        <?php echo $default_rate['interest_rate_type'] == 1 ? '$' : '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['interest_rate_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['interest_rate_mortgage1']:$default_rate['interest_rate_mortgage1']?>
                                        <?php echo $default_rate['interest_rate_type'] == 1 ? '$' : '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['interest_rate_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['interest_rate_mortgage2']:$default_rate['interest_rate_mortgage2']?>
                                        <?php echo $default_rate['interest_rate_type'] == 1 ? '$': '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['interest_rate_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['interest_min_value']:$default_rate['interest_min_value']?>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['operation_fee']:$default_rate['operation_fee']?>
                                        <?php echo $default_rate['operation_fee_type'] == 1 ? '$' : '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['operation_fee_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['operation_fee_mortgage1']:$default_rate['operation_fee_mortgage1']?>
                                        <?php echo $default_rate['operation_fee_mortgage1'] == 1 ? '$' : '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['operation_fee_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['operation_fee_mortgage2']:$default_rate['operation_fee_mortgage2']?>
                                        <?php echo $default_rate['operation_fee_mortgage2'] == 1 ? '$': '%';  ?>
                                        (<?php echo $lang['enum_'.$default_rate['operation_fee_unit']]; ?>)
                                        <br/>
                                    </td>

                                    <td>
                                        <?php echo $special_rate?$special_rate['operation_min_value']:$default_rate['operation_min_value']?>
                                        <br/>
                                    </td>
                                </tr>
                            <?php }?>
                            <?php if(count($output['package_list'])){?>
                                <tr>
                                    <td colspan="20" style="text-align: center">
                                        <a type="button" class="btn btn-default" href="<?php echo getUrl('loan', 'editSubProduct', array('uid' => $output['product_id']), false, BACK_OFFICE_SITE_URL)?>">
                                            <i class="fa fa-mail-reply" style="display: inline-block;margin-left: 0"></i>
                                            <?php echo 'Back'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

