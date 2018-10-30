<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .text-small {
        margin-bottom: 0;
    }
    .no-credit-tip{
        font-size: 16px;
        text-align: center;
        background-color: #fff;
        padding: 25px 0;
        color: red;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="row" style="max-width: 1300px">
            <div class="col-sm-12 col-md-10 col-lg-7">
                <div class="basic-info">
                    <?php include_once(template("widget/item.member.summary.v2"))?>
                </div>
                <div class="scene-photo">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Client Credit Record</h5>
                    </div>
                    <div class="business-content">
                        <div class="credit-list">

                            <div class="no-credit-tip">
                                This client has no any grant credit yet,please grant credit for client first!
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<script>



</script>