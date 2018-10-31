<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Business Data</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Navigator</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="padding-top: 20px ">
        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Credit</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","creditOverview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","creditTop10")?>">Top10</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","creditAgreement")?>">Agreement</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","creditLog")?>">Log</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Loan</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanOverview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanTop10")?>">Top10</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanContract")?>">Contract</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanRepay")?>">Repay</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanOverdue")?>">Overdue</a>
                    </li>
                    <li class="list-group-item">

                        <a href="<?php echo getBackOfficeUrl("data_center_business","loanPenalty")?>">Penalty</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Deposit</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","deposit_overview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","deposit_log")?>">Log</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Withdraw</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","withdraw_overview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","withdraw_log")?>">Log</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Transfer</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","transfer_overview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","transfer_log")?>">Log</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Exchange</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","exchange_overview")?>">Overview</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","exchange_log")?>">Log</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Current Savings</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","savingsTop100",array('currency'=>currencyEnum::USD))?>">USD Top 100</a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo getBackOfficeUrl("data_center_business","savingsTop100",array('currency'=>currencyEnum::KHR))?>">KHR Top 100</a>
                    </li>
                </ul>
            </div>
        </div>




    </div>
</div>


