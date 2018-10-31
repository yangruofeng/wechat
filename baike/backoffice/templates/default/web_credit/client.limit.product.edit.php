<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

     .ibox-title {
         padding-top: 12px!important;
         min-height: 40px;
     }
</style>
<?php
$client_info=$output['client_info'];
$product_list=$output['product_list'];
$limit_product=$output['limit_product'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Product</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Product</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Product</h5>
                </div>
                <div class="content">
                    <form id="frm_co" method="POST" action="<?php echo getUrl('web_credit', 'editMemberLimitProduct', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <table class="table">
                            <tr>
                                <td><label class="control-label">List</label></td>
                                <td>
                                    <?php $limit = array_column($limit_product, 'uid');foreach ($product_list as $v) { ?>
                                        <div class="col-sm-4">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="sub_product_code[]" value="<?php echo $v['sub_product_code']; ?>" <?php echo !in_array($v['sub_product_code'],$limit_product) ? 'checked' : ''?>><?php echo $v['sub_product_name']; ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                    <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger">
                                        <i class="fa fa-check"></i>
                                        Submit
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        $('#frm_co').submit();
    }

</script>






