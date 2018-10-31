<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=2" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<style>
    .loan-exp {
        float: left;
        margin-left: 10px;
        position: relative;
        margin-top: 3px;
    }

    .loan-exp > span {
        color: #5b9fe2;
    }

    .loan-exp > span:hover {
        color: #ea544a;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 24px;
        right: 10px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-hover .loan-exp-wrap {
        filter: alpha(enabled=false);
        opacity: 1;
        visibility: visible;
    }

    .loan-exp-wrap .pos {
        position: relative;
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

    .triangle-up {
        left: auto!important;
        right: 30px;
    }

    .loan-exp-table .t {
        height: 20px;
    }

    .loan-exp-table .a {
        font-size: 14px;
        height: 30px;
    }

    .marginright5 {
        margin-right: 5px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li>
                    <?php if($output['pre']){ ?>
                        <a onclick="javascript:history.go(-1);"><span>List</span></a>
                    <?php } else{ ?>
                        <a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    <?php }?>

                </li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="client-detail-wrap clearfix">
            <?php
            $item = $output['detail'];
            $credit_info = $output['credit_info'];
            $source_mark = 'client_detail';
            ?>
           <?php include(template("client/client.detail.top"));?>
            <div class="other-detail clearfix">
                <?php include(template("client/client.detail.full.left"));?>
                <?php include(template("client/client.detail.full.right"));?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=1"></script>
<script>
//    function showCheckDetail(member_id, cert_type) {
//        if(!member_id || !cert_type){
//            return;
//        }
//
//        yo.loadData({
//            _c: 'client',
//            _m: 'getCheckDetailUrl',
//            param: {member_id: member_id, cert_type: cert_type, source_mark: 'client_detail'},
//            callback: function (_o) {
//                if (_o.STS) {
//                    var url = _o.DATA;
//                    window.location.href = url;
//                } else {
//                    alert(_o.MSG);
//                }
//            }
//        });
//    }
</script>
