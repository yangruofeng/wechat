<style>
    .row {
        padding: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .account-basic {
        margin-bottom: 0;
    }

    #member-icon {
        width: 90px;
        height: 90px;
        border-radius: 3px;
    }

    .pull-left {
        margin-right: 15px;
    }

    .margin-large-left p {
        line-height: 15px;
    }

    .btn {
        padding: 4px 12px;
        border-radius: 0;
        min-width: 70px;
    }

    .table .content {
        padding-top: 5px;
    }

    .table .content .col-second, .content .col-third {
        margin-left: 15px;
    }

    .table .content .text {
        line-height: 20px;
    }

    .table .content .currency {
        font-style: normal;
        margin-left: 5px;
    }

    .link-item:hover {
        color: #3b3bf1;
        cursor: pointer;
    }

    .table-no-background {

    }

    .table.table-no-background tbody tr:nth-child(even) {
        background: transparent;
    }

    .change-state {
        min-width: 80px;
        margin-right: 5px;
        margin-bottom: 3px;
    }

    .panel-item {
        padding: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0;
    }

    .panel-item .panel-heading {
        padding-top: 3px;
        padding-bottom: 3px;
        border-radius: 0;
    }

    .panel-item .panel-heading .panel-title {
        font-size: 14px;
        font-weight: bold;
    }

    .panel-item .panel-heading .btn {
        float: right;
        position: relative;
        border: none;
        min-width: auto;
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        padding-left: 0;
        padding-right: 0;
        background-color: transparent;
    }

    #myModal .modal-dialog {
        margin-top: 20px !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Staff</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'staff', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a  class="current"><span>Staff Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php
        $staff_info = $output['staff_info'];
        $staff_status_list = $output['staff_status'];
        $identity_list = $staff_info['identity_list'];
        ?>
        <?php include template("user/staff.info.part.left")?>
        <?php include template("user/staff.info.part.right")?>
    </div>
</div>