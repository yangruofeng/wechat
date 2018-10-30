<?php $loan = $data['data']; ?>
<div class="row-fluid">
    <div class="col-sm-3 dashboard-contract">
        <div class="box-content box-statistic">
            <h3 class="title text-success"><span class="i">
                    <i class="fa fa-file-text"></i>
                </span><?php echo formatQuantity($loan['count_contract']); ?>
            </h3>
            <small>Contracts</small>
        </div>
    </div>
    <div class="col-sm-9 paddingright0">
        <div class="col-sm-6 paddingleft0">
            <div class="box-content box-statistic">
                <h3 class="title text-primary">
                    <span class="i"><i class="fa fa-money"></i></span>
                    <?php echo ncPriceFormat($loan['total_principal']); ?>
                    <span class="currency">USD</span></h3>
                <small>Total Principal</small>
            </div>
        </div>
        <div class="col-sm-6 paddingleft0">
            <div class="box-content box-statistic">
                <h3 class="title text-warning"><span class="i"><i
                            class="fa fa-credit-card"></i></span><?php echo ncPriceFormat($loan['total_outstanding_principal']); ?>
                    <span class="currency">USD</span></h3>
                <small>Outstanding Principal</small>
            </div>
        </div>
        <div class="col-sm-6 paddingleft0">
            <div class="box-content box-statistic">
                <h3 class="title text-error"><span class="i"><i
                            class="fa fa-align-left"></i></span><?php echo ncPriceFormat($loan['total_receivable_interest']); ?>
                    <span class="currency">USD</span></h3>
                <small>Receivable Interest + Fee</small>
            </div>
        </div>
        <div class="col-sm-6 paddingleft0">
            <div class="box-content box-statistic">
                <h3 class="title text-info"><span class="i"><i
                            class="fa fa-area-chart"></i></span><?php echo ncPriceFormat($loan['total_outstanding_interest']); ?>
                    <span class="currency">USD</span></h3>
                <small>Outstanding Interest + Fee</small>
            </div>
        </div>
    </div>
</div>