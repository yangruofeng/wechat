<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table tr td:first-child {
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
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Write Off</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'writeOff', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Unprocessed</span></a>
                </li>
                <li>
                    <a href="<?php echo getUrl('loan', 'writeOff', array('type' => 'processed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Processed</span></a>
                </li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td><?php echo $output['detail']['contract_sn']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Client Name</label></td>
                    <td><?php echo $output['detail']['display_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loss Amount</label></td>
                    <td>
                        <span style="font-weight: 700"><?php echo ncAmountFormat($output['detail']['loss_amount']) ?></span>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Close Remark</label></td>
                    <td>
                        <?php echo $output['detail']['close_remark']?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Creator</label></td>
                    <td>
                        <?php echo $output['detail']['creator_name']?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Create Time</label></td>
                    <td>
                        <?php echo timeFormat($output['detail']['create_time'])?>
                    </td>
                </tr>

                <?php if ($output['lock']) { ?>
                    <tr>
                        <td><label class="control-label">Handler</label></td>
                        <td><span class="color28B779"><?php echo $output['detail']['auditor_name']; ?></span></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <?php if ($output['lock']) { ?>
                            <span class="color28B779">Auditing...</span>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i
                                        class="fa fa-vcard-o"></i>Back
                                </button>
                            </div>
                        <?php } else { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger btn-approve" style="min-width:80px;">
                                    <i class="fa fa-check"></i><?php echo 'Approve'; ?>
                                </button>
                                <button type="button" class="btn btn-info btn-disapprove" style="min-width:80px;">
                                    <i class="fa fa-remove"></i><?php echo 'Disapprove'; ?>
                                </button>
                                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"
                                        style="min-width:80px">
                                    <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                                </button>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="state" value="">
            <input type="hidden" name="uid" value="<?php echo $output['detail']['uid']; ?>">
        </form>
    </div>
</div>
<script>
    $(function () {
        $('.btn-approve').click(function () {
            $('[name="state"]').val('approve');
            $("#validForm").submit();
        })

        $('.btn-disapprove').click(function () {
            $('[name="state"]').val('disapprove');
            $("#validForm").submit();
        })
    })
</script>
