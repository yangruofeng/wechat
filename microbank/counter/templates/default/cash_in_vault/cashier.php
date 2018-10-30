<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>

    .btn {
        margin-bottom: 5px !important;
    }

    .th {
        font-weight: 600;
        font-size: 15px;
    }

</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php require_once template('widget/branch.balance'); ?>
    <div class="container" style='width: 800px'>
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Cashier COD</h5>
        </div>
        <div class="content" class="col-sm-12" style="padding-top:5px">
            <table class="table">
                <tr class="th">
                    <td class="col-sm-3">
                        <?php echo 'Cashier' ?>
                    </td>
                    <td class="col-sm-3">
                        <?php echo 'USD' ?>
                    </td>
                    <td class="col-sm-3">
                        <?php echo 'KHR' ?>
                    </td>
                    <td class="col-sm-3">
                        <?php echo 'Function' ?>
                    </td>
                </tr>
                <?php foreach ($output['cashier_list'] as $cashier) { ?>
                    <tr>
                        <td><?php echo $cashier['user_name'] ?></td>
                        <td><?php echo $cashier['balance']['USD'] ?></td>
                        <td><?php echo $cashier['balance']['KHR'] ?></td>
                        <td>
                            <a class="btn btn-default" style="min-width: 60px"
                               href="<?php echo getUrl('cash_in_vault', 'cashierTransaction', array('cashier_id' => $cashier['uid']), false, ENTRY_COUNTER_SITE_URL) ?>">
                                <i class="fa fa-list-alt" style="margin-right: 2px"></i>Flow
                            </a>
                            <a class="btn btn-default" style="min-width: 60px"
                               href="<?php echo getUrl('cash_in_vault', 'cashierCredit', array('cashier_id' => $cashier['uid']), false, ENTRY_COUNTER_SITE_URL) ?>">
                                <i class="fa fa-outdent" style="margin-right: 2px"></i>Credit
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>