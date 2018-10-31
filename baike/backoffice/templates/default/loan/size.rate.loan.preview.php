<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>

<style>

    .info-summary:before{
        display: block;
        content: '';
        clear: both;
        height: 0;
        line-height: 0;
    }

    .info-summary{
        display: block;
        background: #fff;
        padding: 5px;
    }

    .info-summary li{
        float: left;
        width: 33.33%;
    }

    .info-summary li span{
        font-size: 14px;
        font-weight: 500;
    }
    .info-summary li label{
        font-size: 16px;
        font-weight: 600;
        color:red;
    }
</style>
<?php
    $preview_info = $output['preview_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'editSubProduct', array("uid"=>$output['sub_product']['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Sub Product</span></a></li>
                <li><a class="current"><span>Size Rate Loan Preview</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <div>
                <span>Product Name:</span>
                <label><?php echo $output['sub_product']['sub_product_name']?></label>

            </div>
            <div>
                <?php
                $data=array("type"=>"info","data"=>array($output['size_rate']));
                include_once(template("loan/size_rate.list"));?>
            </div>
        </div>

        <div>

            <div style="margin-top: 20px;">
                <h3>Summary Info</h3>
                <ul class="info-summary clearfix">
                    <li>
                        <span>Loan Amount:</span>
                        <label for=""><?php echo $preview_info['loan_amount'].$preview_info['currency']; ?></label>
                    </li>

                    <li>
                        <span>Loan Time:</span>
                        <label for=""><?php echo $preview_info['loan_period_value'].' '.$preview_info['loan_period_unit']; ?></label>
                    </li>

                    <li>
                        <span>Interest Rate:</span>
                        <label for=""><?php echo $preview_info['interest_info']['interest_rate'].'% '.$preview_info['interest_info']['interest_rate_unit']; ?></label>
                    </li>

                    <li>
                        <span>Operation Fee:</span>
                        <label for=""><?php echo $preview_info['interest_info']['operation_fee'].'% '.$preview_info['interest_info']['operation_fee_unit']; ?></label>
                    </li>

                    <li>
                        <span>Admin Fee:</span>
                        <label for=""><?php echo $preview_info['admin_fee']; ?></label>
                    </li>

                    <li>
                        <span>Loan Fee:</span>
                        <label for=""><?php echo $preview_info['loan_fee']; ?></label>
                    </li>

                    <li>
                        <span>Receive Amount:</span>
                        <label for=""><?php echo $preview_info['arrival_amount']; ?></label>
                    </li>

                    <li>
                        <span>Payable Interest:</span>
                        <label for=""><?php echo $preview_info['total_repayment']['total_interest']; ?></label>
                    </li>

                    <li>
                        <span>Payable Operation Fee:</span>
                        <label for=""><?php echo $preview_info['total_repayment']['total_operator_fee']; ?></label>
                    </li>

                    <li>
                        <span>Total Payable Amount:</span>
                        <label for=""><?php echo $preview_info['total_repayment']['total_payment']; ?></label>
                    </li>
                </ul>
            </div>



            <div style="margin-top: 20px;">
                <h3>Repayment Schema</h3>
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-head">
                        <tr>
                            <th>Index</th>
                            <th>Receive Date</th>
                            <th>Initial Principal</th>
                            <th>Payable Principal</th>
                            <th>Payable Interest</th>
                            <th>Payable Operation Fee</th>
                            <th>Payable Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $preview_info['repayment_schema'] as $v ){ ?>
                            <tr>
                                <td><?php echo $v['scheme_index']; ?></td>
                                <td><?php echo $v['receive_date']; ?></td>
                                <td><?php echo $v['initial_principal']; ?></td>
                                <td><?php echo $v['receivable_principal']; ?></td>
                                <td><?php echo $v['receivable_interest']; ?></td>
                                <td><?php echo $v['receivable_operation_fee']; ?></td>
                                <td><?php echo $v['amount']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

