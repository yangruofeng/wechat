<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table > tr > td:first-child {
        width: 200px;
    }

    .audit-table textarea {
        width: 300px;
        height: 80px;
        float: left;
    }

    .custom-btn-group {
        float: inherit;
    }

    .audit-table em {
        font-size: 20px;
        font-style: normal;
        color: #ea544a;
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
<?php
$deducting_penalties = $output['deducting_penalties'];
$loan_contract = $output['loan_contract'];
$scheme_list = $output['scheme_list'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Deducting Penalties</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'deductingPenalties', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Unprocessed</span></a>
                </li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post" action="<?php echo getUrl('loan', 'auditRepayment', array(), false, BACK_OFFICE_SITE_URL)?>">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td><?php echo $loan_contract['contract_sn']?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Penalties Detail</label></td>
                    <td>
                        <table class="table table-bordered" style="max-width: 400px">
                            <thead>
                            <tr class="table-header" style="background: #EFEFEF">
                                <td>Scheme Name</td>
                                <td>Penalties</td>
                                <td>Deducted</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <?php foreach($scheme_list as $scheme){?>
                                <tr>
                                    <td>
                                        <?php echo $scheme['scheme_name']?>
                                    </td>
                                    <td>
                                        <?php echo ncAmountFormat($scheme['penalties'])?>
                                    </td>
                                    <td>
                                        <?php echo ncAmountFormat($scheme['deduction_penalty'])?>
                                    </td>
                                </tr>
                            <?php }?>
                            <tr style="font-weight: 700">
                                <td>
                                    <?php echo 'Total'?>
                                </td>
                                <td>
                                    <?php echo ncAmountFormat($loan_contract['penalties_total'])?>
                                </td>
                                <td>
                                    <?php echo ncAmountFormat($loan_contract['deduction_total'])?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Type</label></td>
                    <td><?php echo $lang['deducting_penalties_type_' . $deducting_penalties['type']] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Deducting Penalties</label></td>
                    <td><?php echo ncAmountFormat($deducting_penalties['deducting_penalties']) ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <td><?php echo $deducting_penalties['remark'] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Client</label></td>
                    <td><?php echo $loan_contract['display_name']?:$loan_contract['login_code'] ?></td>
                </tr>

                <?php if($deducting_penalties['creator_name']){?>
                    <tr>
                        <td><label class="control-label">Creator</label></td>
                        <td><?php echo $deducting_penalties['creator_name'] ?></td>
                    </tr>
                <?php }?>

                <tr>
                    <td><label class="control-label">Create Time</label></td>
                    <td><?php echo timeFormat($deducting_penalties['create_time']) ?></td>
                </tr>

                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <div class="custom-btn-group approval-btn-group">
                            <a type="button" class="btn btn-info" style="min-width:80px;"
                               onclick="submitForm(<?php echo $deducting_penalties['uid'] ?>,'approve')">
                                <i class="fa fa-check"></i><?php echo 'Approve'; ?>
                            </a>
                            <button type="button" class="btn btn-danger" style="min-width:80px;"
                                    onclick="submitForm(<?php echo $deducting_penalties['uid'] ?>,'disapprove')">
                                <i class="fa fa-remove"></i><?php echo 'Disapprove'; ?>
                            </button>
                            <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"
                                    style="min-width:80px">
                                <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $deducting_penalties['uid']; ?>">
        </form>
    </div>
</div>
<script>
    function submitForm(uid, type) {
        yo.loadData({
            _c: "loan",
            _m: "auditPenalties",
            param: {uid: uid,type: type},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = "<?php echo getUrl('loan', 'deductingPenalties', array(), false, BACK_OFFICE_SITE_URL) ?>";
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>
