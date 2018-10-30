<?php $member_product = $data['data'];?>
<?php  foreach ($member_product as $product){ ?>
    <div style="margin-bottom: 35px;border: 1px solid lightgrey">
        <table class="table" id="productbox">
            <tr>
                <td><label class="control-label">Product Code</label></td>
                <td><?php echo $product['sub_product_code'] ?></td>
                <td><label class="control-label">Product Type</label></td>
                <td><?php echo ucwords(str_replace('_',' ',$product['category'])) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Product Name</label></td>
                <td><?php echo $product['sub_product_name']?></td>
                <td><label class="control-label">Min Rate</label></td>
                <td><?php echo $product['monthly_min_rate'] ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Product Summary</label></td>
                <td colspan="3"><?php echo $product['sub_summary']?></td>

            </tr>
            <tr style="text-align: center;margin-top: 20px!important;">
                <td colspan="4" style="border-bottom:1px solid lightgrey !important;">
                    <button class="btn btn-primary add_loan_next" title="Credit State is Invalid" disabled onclick="showLoan(<?php echo $product['uid'] ?>,'<?php echo $product['sub_product_name'] ?>','<?php echo $product['interest_type'] ?>','<?php echo $product['repayment_type'] ?>')">
                        Loan
                    </button>
                </td>
            </tr>
        </table>

    </div>
<?php } ?>

