<?php
$limit = $output['limit'];
$partnerBizLang = enum_langClass::getPartnerBizTypeLang();
?>
<style>
    .amount{
        font-size: 16px;
        font-weight: 600;
        color:red;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Partner Limit</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'partnerLimitSettingPage', array(), false, BACK_OFFICE_SITE_URL ); ?>"><span>Setting</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php if( empty($limit) ){ ?>
            <div class="no-record">
                No setting.
            </div>
        <?php }else{ ?>
            <div class="col-sm-8">
                <?php foreach( $limit as $partner_limit ){ ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo $partner_limit['partner_name']; ?></h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <tr class="table-header">
                                    <td>Business Type</td>
                                    <td>Per Time</td>
                                    <td>Per Day</td>
                                    <td>Function</td>
                                </tr>
                                <?php foreach( $partner_limit['limit'] as $v ){ ?>
                                    <tr>
                                        <td>
                                            <kbd >
                                                <?php echo $partnerBizLang[$v['biz_type']]?:$v['biz_type']; ?>
                                            </kbd>

                                        </td>
                                        <td>
                                            <span class="amount"><?php echo $v['per_time']?:'N/A'; ?></span>
                                        </td>
                                        <td>
                                            <span class="amount"><?php echo $v['per_day']?:'N/A'; ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo getBackOfficeUrl('dev','partnerLimitSettingPage',array(
                                                'uid' => $v['uid']
                                            )); ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                                Edit
                                            </a>
                                            <a class="btn btn-sm btn-danger" onclick="deleteSetting(<?php echo $v['uid']; ?>);">
                                                <i class="fa fa-close"></i>
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </div>
</div>
<script>

    function deleteSetting(uid)
    {
        if( !uid ){
            return false;
        }
        $.messager.confirm("Confirm", "Confirm to delete this?", function(r){
            if (r) {
                $('body').waiting();
                yo.loadData({
                    _c: 'dev',
                    _m: 'ajaxDeleteSetting',
                    param: {uid: uid},
                    callback: function (_o) {
                        $('body').unmask();
                        if (_o.STS) {
                            alert('Delete success.');
                            setTimeout(function (){
                                window.location.reload();
                            },2000)
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        });
    }



</script>
