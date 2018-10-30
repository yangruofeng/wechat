<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1" rel="stylesheet" type="text/css"/>

<?php
$data = $output['data'];
$currency = (new currencyEnum())->toArray();
?>

<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3>Warning Of Overdue Loan</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
