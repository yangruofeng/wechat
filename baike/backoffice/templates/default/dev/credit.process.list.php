<?php
$all_dict = $output['all_dict'];
?>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js"></script>
<style>
    a:hover{
        cursor: pointer;
    }
    .function a{
        margin-left: 5px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit Process</h3>
            <ul class="tab-base">
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 800px">
        <div>
            <table class="table">
                <thead>
                <tr>
                    <td>Process</td>
                    <td>State</td>
                    <td>Function</td>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>
                        Fingerprint Cert
                    </td>
                    <td>
                        <?php
                        if( $all_dict['close_credit_fingerprint_cert'] == 1 ){
                            echo '<i style="font-size: 24px;color:red;" class="fa fa-close" aria-hidden="true"></i>';
                        }else{
                            echo '<i style="font-size: 24px;color:green;" class="fa fa-check" aria-hidden="true"></i>';
                        }
                        ?>
                    </td>
                    <td class="function">
                        <a title="Open" onclick="openProcess(1);">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Open</span>
                        </a>
                        <a title="Close" onclick="closeProcess(1)">
                            <i class="fa fa-close" aria-hidden="true"></i>
                            <span>Close</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        Authorized Contract
                    </td>
                    <td>
                        <?php
                        if( $all_dict['close_credit_authorized_contract'] == 1 ){
                            echo '<i style="font-size: 24px;color:red;" class="fa fa-close" aria-hidden="true"></i>';
                        }else{
                            echo '<i style="font-size: 24px;color:green;" class="fa fa-check" aria-hidden="true"></i>';
                        }
                        ?>
                    </td>
                    <td class="function">

                        <a title="Open" onclick="openProcess(2);">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Open</span>
                        </a>
                        <a title="Close" onclick="closeProcess(2)">
                            <i class="fa fa-close" aria-hidden="true"></i>
                            <span>Close</span>
                        </a>
                    </td>
                </tr>
                </tbody>

            </table>
        </div>



    </div>
</div>

<script>
    function openProcess(type) {
        var params = {};
        params.type = type;
        yo.loadData({
            _c: 'dev',
            _m: 'openCreditProcess',
            param: params,
            callback: function (data) {
                if (data.STS) {
                    alert('Saved Successfully',1, function () {
                        window.location.reload();
                    });
                } else {
                    alert(data.MSG,2)
                }
            }
        });
    }

    function closeProcess(type) {
        var params = {};
        params.type = type;
        yo.loadData({
            _c: 'dev',
            _m: 'closeCreditProcess',
            param: params,
            callback: function (data) {
                if (data.STS) {
                    alert('Saved Successfully',1,function () {
                        window.location.reload();
                    });
                } else {
                    alert(data.MSG, 2)
                }
            }
        });
    }
</script>




