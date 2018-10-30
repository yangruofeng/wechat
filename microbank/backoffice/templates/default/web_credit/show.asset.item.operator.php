<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }

</style>
<?php
$client_info = $output['client_info'];
$asset = $output['assets_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a class="current"><span>Asset Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php require_once template("web_credit/show.asset.item.content")?>
</div>







