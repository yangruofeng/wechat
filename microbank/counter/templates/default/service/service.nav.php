<div class="content-nav">
    <ul class="nav nav-tabs">
        <li role="presentation" id="requestLoan"  class="<?php echo ($_GET['op'] == 'requestLoan' || $_GET['op'] == 'addRequestLoan') ? 'active' : ''; ?>">
            <a href="<?php echo ($_GET['op'] == 'requestLoan' ||  $_GET['op'] == 'addRequestLoan') ? '#' : getUrl('service', 'requestLoan', array(), false, ENTRY_COUNTER_SITE_URL);?>">Request Loan</a>
        </li>
        <li role="presentation" id="currencyExchange"  class="<?php echo $_GET['op'] == 'currencyExchange' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'currencyExchange' ? '#' : getUrl('service', 'currencyExchange', array(), false, ENTRY_COUNTER_SITE_URL);?>">Currency Exchange</a>
        </li>
    </ul>
</div>


