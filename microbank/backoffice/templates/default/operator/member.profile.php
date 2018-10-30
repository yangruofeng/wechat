<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group{
        margin-bottom: 20px;
    }

    .container{
        width: 800px!important;
    }

    .mincontent{
        padding:15px
    }

    .idnum{
        margin-top: 5px;
    }

    .redstar{
        color: red;
        font-size: 14px;
        padding-right: 1px;
    }

    .ibox-title{
        padding-top: 10px;
        height: 40px!important;
        min-height: 0px!important;
    }

    .btn {
        border-radius: 0;
        min-width: 80px;
    }
</style>
<?php $client_info = $output['client_info'];?>

<?php
$memberStateLang = enum_langClass::getMemberStateLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Profile </h3>
            <ul class="tab-base">
                <li><a class="current"><span>Request</span></a></li>
            </ul>
        </div>
    </div>
    <div class="collection-div">
        <?php require_once template("widget/item.member.search")?>

        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
            <a class="btn btn-default" href="<?php echo getUrl('operator', 'clientProfileIndex', array(), false, BACK_OFFICE_SITE_URL); ?>">
                <i class="fa fa-refresh"></i>
                <?php echo 'Reset' ?>
            </a>
            <button type="button" class="btn btn-primary" onclick="btn_set_work_type_onclick();">
                <i class="fa fa-edit"></i>
                <?php echo 'Work Type' ?>
            </button>
            <button type="button"   class="btn btn-primary" onclick="btn_set_residence_onclick()">
                <i class="fa fa-edit"></i>
                <?php echo 'Residence' ?>
            </button>
            <button type="button"   class="btn btn-primary" onclick="btn_set_branch_onclick()">
                <i class="fa fa-edit"></i>
                <?php echo 'Change Branch' ?>
            </button>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_member_onclick();
    })

    function btn_set_work_type_onclick() {
        var uid = parseInt($("#client_id").val());
        var state = $("#member_state").html()
        if (state == 'Locking' || state == 'Cancel') {
            alert("State Inappropriate");
            return;
        }
        if (uid > 0) {
            var _url = "<?php echo getUrl('operator', 'clientProfileWorkType', array(), false, BACK_OFFICE_SITE_URL);?>";
            _url += "&uid=" + uid;
            window.location.href = _url;
        } else {
            alert("Please choose member");
        }
    }

    function btn_set_residence_onclick() {
        var uid = parseInt($("#client_id").val());
        var state = $("#member_state").html()
        if (state == 'Locking' || state == 'Cancel') {
            alert("State Inappropriate");
            return;
        }
        if (uid > 0) {
            var _url = "<?php echo getUrl('operator', 'clientProfileResidence', array(), false, BACK_OFFICE_SITE_URL);?>";
            _url += "&uid=" + uid;
            window.location.href = _url;
        } else {
            alert("Please choose member");
        }
    }

    function btn_set_branch_onclick() {
        var uid = parseInt($("#client_id").val());
        var state = $("#member_state").html()
        if (state == 'Locking' || state == 'Cancel') {
            alert("State Inappropriate");
            return;
        }
        if (uid > 0) {
            var _url = "<?php echo getUrl('operator', 'clientProfileBranch', array(), false, BACK_OFFICE_SITE_URL);?>";
            _url += "&uid=" + uid;
            window.location.href = _url;
        } else {
            alert("Please choose member");
        }
    }

</script>