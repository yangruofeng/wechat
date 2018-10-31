<!--Staff List	Bank List	Transactions	Journal Voucher-->
<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=7" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch Center</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <?php $branch_list = $output['branch_list'];?>
    <div class="container" >
        <div class="data-center-top-list">
            <?php foreach($branch_list as $v){ ?>
                <button type="button" class="btn btn-primary btn-sm top-user-item" onclick="btn_search_branch_info(this, <?php echo $v['uid'];?>);"><?php echo $v['branch_code'];?></button>
            <?php }?>
        </div>
        <div class="data-center-base-info"></div>
        <div class="data-center-list"></div>
    </div>

</div>
<script>
    var _BRANCH_ID = 0;
    $(document).ready(function () {
        btn_search_branch_info();
    });

    function btn_search_branch_info(el, branch_id){
        if(branch_id) {
            $('.btn-center-item').removeClass('btn-success');
        }
        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        $(el).addClass("current").siblings().removeClass("current");
        _BRANCH_ID = branch_id;
        yo.dynamicTpl({
            tpl: "data_center_branch/branch.info",
            dynamic: {
                api: "data_center_branch",
                method: "getBranchInfo",
                param: { branch_id: branch_id }
            },
            callback: function (_tpl) {
                $(".data-center-base-info").html(_tpl);
                $(".data-center-list").html('');
            }
        });
    }

</script>

