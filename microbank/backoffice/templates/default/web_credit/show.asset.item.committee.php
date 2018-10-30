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
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a onclick="javascript:history.go(-1)"><span>Credit Grant</span></a></li>
                <li><a class="current"><span>Asset Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php require_once template("web_credit/show.asset.item.content")?>
    <?php require_once template(":widget/item.image.viewer.js")?>
</div>