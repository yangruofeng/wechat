<style>
    .business-condition {
        margin-bottom: 20px;
    }

    .verify-table .verify-img {
        width: 80px;
    }

    .verify-table .locking {
        color: red;
        font-style: normal;
    }

    .verify-table .locking i {
        margin-right: 3px;
    }

    .verify-table .fa-user {
        margin-right: 3px;
    }

    .img-list {
        display: inline-block;
    }

    .img-list span {
        width: 79px;
        height: 62px;
        display: block;
        float: left;
        border: 1px solid #f7d2d2;
    }

    .img-list span {
        margin-right: 2px;
    }

    .img-list span:last-child {
        margin-right: 0;
    }

    .verify-state .btn {
        margin-left: -1px;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .verify-table .lab-name {
        width: 130px;
        text-align: right;
        margin-right: 8px;
    }

    .verify-table .cert-info {
        line-height: 10px;
        padding-top: 13px;
    }

    .verify-table .cert-type h3 {
        font-size: 20px;
        font-weight: 100;
        color: #000;
    }

    .verify-table .cert-type p {
        margin: 0;
    }

    .verify-table .cert-type label {
        margin-bottom: 0;
    }

    .verify-table .cert-type .lab-name {
        width: auto;
    }

    .verify-table .verify-state {
        display: inline-block;
        width: 150px;
    }

    .verify-table .verify-state .title {
        font-weight: 600;
        color: #fff;
        background: #40B2DA;
        border: 1px solid #40B2DA;
        text-align: center;
        padding: 6px 0;
    }

    .verify-table .verify-state .content {
        text-align: center;
        border: 1px solid #40B2DA;
        height: 70px;
    }

    .verify-table .verify-state .state {
        height: 35px;
        line-height: 35px;
    }

    .verify-table .verify-state .state.other {
        line-height: 0;
    }

    .verify-table .verify-state .state.other p {
        padding-top: 3px;
    }

    .verify-table .verify-state .custom-btn-group {
        float: inherit;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client CBC</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'checkCbc', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('operator', 'addClientCbc', array('uid'=>$output['mid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>History</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
    <div class="table-form">
            <div class="business-content">
                <div class="business-list"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var _values = {};
        _values.pageNumber = 1;
        _values.pageSize = 20;
        _values.uid = '<?php echo $output['mid']?:0;?>';
        yo.dynamicTpl({
            tpl: "operator/cbc.history.list",
            dynamic: {
                api: "operator",
                method: "getCbcHistory",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    });

</script>




