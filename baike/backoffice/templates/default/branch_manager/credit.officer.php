<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .activity-list .item {
        margin-top: 0;
        padding: 10px 20px 10px 15px;;
    }

    .activity-list .item div > span:first-child {
        font-weight: 500;
    }

    .activity-list .item span.check-state {
        float: right;
        font-size: 12px;
        margin-left: 5px;
    }

    .activity-list .item span.check-state .fa-check {
        font-size: 18px;
        color: green;
    }

    .activity-list .item span.check-state .fa-question {
        font-size: 18px;
        color: red;
        padding-right: 5px;
    }

    #cbcModal .modal-dialog {
        margin-top: 10px!important;
    }

    #cbcModal .modal-dialog input{
        height: 30px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit Officer</h3>
            <ul class="tab-base">
             <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $list = $output['list'];?>
        <form class="form-horizontal">
            <table class="table audit-table">
                <thead>
                    <tr class="table-header">
                        <td>Name</td>
                        <td>Phone</td>
                        <td>Function</td>
                    </tr>
                </thead> 
                <tbody class="table-body">
                    <?php if(count($list) > 0){ ?>
                        <?php foreach ($list as $k => $v) { ?>
                            <tr>
                                
                                <td><?php echo $v['user_name'];?></td>
                                <td><?php echo $v['mobile_phone'];?></td>
                                <td>
                                <div class="custom-btn-group">
                                    <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('branch_manager', 'showCreditOfficerDetail', array('uid'=>$v['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                        <span><i class="fa fa-vcard-o"></i>Detail</span>
                                    </a>
                                </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr><td colspan="6"><div class="no-record">No record.</div></td></tr>
                    <?php } ?>
                </tbody>               
            </table>
        </form>
    </div>
</div>