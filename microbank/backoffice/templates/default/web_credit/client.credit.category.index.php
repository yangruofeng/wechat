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
    .ios-checkbox {
        float: left;
    }
    /* ==========================================================================
          label标签模拟按钮
========================================================================== */
    .emulate-ios-button {
        display: block;
        width: 40px;
        height: 20px;
        background: #ccc;
        border-radius: 50px;
        cursor: pointer;
        position: relative;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
    }
    .emulate-ios-button.active {
        background: #34bf49;
    }

    /* ==========================================================================
              设置伪类,来实现模拟滑块滑动,过渡用了transition来实现 ,
              translateZ来强制启用硬件渲染
    ========================================================================== */

    .emulate-ios-button:after {
        content: '';
        display: block;
        width: 18px;
        height: 19px;
        border-radius: 100%;
        background: #fff;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .1);
        position: absolute;
        left: .05rem;
        top: .05rem;
        -webkit-transform:translateZ(0);
        transform:translateZ(0);
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
    }

    .emulate-ios-button.active:after {
        position: absolute;
        top: .05rem;
        left: auto;
        right: .05rem;
        -webkit-transform:translateZ(0);
        transform:translateZ(0);
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
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
            <h3>Client-Credit-Category</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                <li><a  class="current"><span>Credit Category</span></a></li>
            </ul>

        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <!--<a class="btn btn-primary btn-xs" href="<?php echo getBackOfficeUrl("web_credit","editMemberCreditCategoryPage",array("member_id"=>$output['client_info']['uid']))?>" style="float: right;position: relative;"> ADD </a>-->
                    <h5 style="color: black">
                        <i class="fa fa-id-card-o"></i>Loan-Category
                    </h5>
                </div>
                <div class="content">
                    <table class="table table-striped table-bordered table-hover">
                        <tr class="table-header">
                            <td>Category Name</td>
                            <td>Repayment</td>
                            <td>Interest Package</td>
                            <td>One Time</td>
                            <td>Close</td>
                            <td>Function</td>
                        </tr>
                        <?php if($output['member_category_list']){?>

                            <?php foreach($output['member_category_list'] as $uid=>$item){?>
                                <tr>
                                    <td>
                                        <?php echo $item['alias']?>
                                    </td>
                                    <td>
                                        <?php echo $item['sub_product_name']?>
                                    </td>
                                    <td>
                                        <?php echo $item['interest_package_name']?:'Default'?>
                                    </td>
                                    <td>
                                        <?php if($item['is_one_time']){?>
                                            <i class="fa fa-check"></i>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <div class="ios-checkbox" data-category="<?php echo $item['category_id'];?>"  data-state="<?php echo $item['is_close']?0:1;?>">
                                            <label for="ios-checkbox" class="emulate-ios-button <?php if(!$item['is_close']){?>active<?php }?>"></label>
                                            <input type="hidden" class="state" value="<?php echo $item['is_close']?0:1;?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="func" style="<?php if($item['is_close']){?>display:none;<?php }?>">
                                            <a class="btn btn-link btn-xs btn-edit-category" href="<?php echo getBackOfficeUrl("web_credit","editMemberCreditCategoryProductPage",array("member_id"=>$output['client_info']['uid'],"uid"=>$item['uid']))?>">Edit</a>
                                        </div>
                                    </td>

                                </tr>
                            <?php }?>
                        <?php }else{?>
                            <td colspan="10"><?php include(template(":widget/no_record"))?></td>
                        <?php }?>
                    </table>

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
    $('.ios-checkbox').click(function(){
        var self_btn = $(this),
            label = self_btn.find('label'),
            edit = self_btn.parents('tr').find('.func'),
            cls = label.hasClass('active'),
            category_id = self_btn.attr('data-category'),
            state = self_btn.find('.state').val();
        $(document).waiting();
        yo.loadData({
            _c: 'web_credit',
            _m: 'submitLoanCategory',
            param: {member_id: <?php echo $_GET['uid'];?>, category_id: category_id, state: state},
            callback: function (_o) {
                if (_o.STS) {
                    cls ? label.removeClass('active') : label.addClass('active');
                    if(cls){
                        label.removeClass('active');
                        self_btn.find('.state').val(0);
                        edit.hide()
                    }else{
                        label.addClass('active');
                        self_btn.find('.state').val(1);
                        edit.show();
                    }
                    window.location.reload();
                } else {
                    $(document).unmask();
                    alert('Handle fail:'+_o.MSG);
                }
            }
        });
    });
</script>






