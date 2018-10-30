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

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        z-index: 99;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 37px;
        left: 8px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .triangle-up {
        background-position: 0 -228px;
        height: 8px;
        width: 12px;
        display: block;
        position: absolute;
        top: -15px;
        left: 40px;
        bottom: auto;
    }

    .triangle-up {
        background-image: url(./resource/image/common-slice-s957d0c8766.png);
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 18px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }
</style>
<?php $detail = $output['detail']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Contract</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'contract', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li>
                    <a href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $output['contact_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Detail</span></a>
                </li>
                <li><a class="current"><span>Write Off</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td><?php echo $output['contact_info']['contract_sn']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Client Name</label></td>
                    <td><?php echo $output['contact_info']['display_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loss Amount</label></td>
                    <td>
                        <span style="font-weight: 700"><?php echo ncAmountFormat($output['contact_info']['loss_amount']) ?></span>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Close Remark</label></td>
                    <td>
                        <?php if ($output['write_off']) { ?>
                            <?php echo $output['write_off']['close_remark']?>
                        <?php } else { ?>
                            <textarea class="form-control" name="close_remark">
                        </textarea>
                            <div class="error_msg"></div>
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <div class="custom-btn-group approval-btn-group">
                            <?php if(!$output['write_off']){?>
                                <button type="button" class="btn btn-danger" style="min-width:80px;">
                                    <i class="fa fa-check"></i><?php echo 'Submit'; ?>
                                </button>
                            <?php }?>
                            <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);" style="min-width:80px">
                                <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                            </button>
                        </div>
                        <?php if($output['write_off']){?>
                            <span class="error_msg">
                                The application has not been reviewed.
                            </span>
                        <?php }?>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $output['contact_info']['uid']; ?>">
            <input type="hidden" name="form_submit" value="ok">
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=1"></script>
<script>

    $(function () {
        $('.btn-danger').click(function () {
            if (!$("#validForm").valid()) {
                return;
            }
            $("#validForm").submit();
        })
    })

    $('#validForm').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            close_remark: {
                required: true
            }
        },
        messages: {
            close_remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>
