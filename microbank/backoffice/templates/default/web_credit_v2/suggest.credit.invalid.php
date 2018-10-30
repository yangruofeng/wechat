<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<?php $client_info = $output['client_info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $client_info['uid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a class="current"><span>Request Credit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1300px">
        <div class="col-sm-12">
            <h5>Not allowed to edit credit:</h5>
            <h3>Please add new credit-request of client at first!</h3>
        </div>

        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php $output['is_bm'] ? $source_mark = 'bm_suggest' : 'op_suggest'; ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
