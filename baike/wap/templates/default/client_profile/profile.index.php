<?php include_once(template('widget/inc_header_weui'));?>
<?php
$data = $output['data'];
$work_info = $output['work_info'];
$due_date = $output['due_date'];
$principal_period = $output['principal_period'];
$semi_balloon = $output['show_semi_balloon'];
?>

<div class="page__bd">
    <div class="weui-cells__title">
        Information
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="<?php echo getWapOperatorUrl('home', 'occupationInfomation', array('cid'=>$_GET['cid'],'id'=>$_GET['id']))?>">
            <div class="weui-cell__bd">
                Work Type
            </div>
            <div class="weui-cell__ft">
                <?php if(!$work_info){?>
                    Not Verify
                <?php }else{?>
                    <?php echo $work_info?>
                <?php }?>
            </div>
        </a>
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" onclick="editClientResidence_Onclick()">
            <div class="weui-cell__bd">
                Residence
            </div>
            <div class="weui-cell__ft">
                <?php if(!$output['residence']['full_text']){?>
                    Not Set
                <?php }else{?>
                    <?php echo $output['residence']['full_text'];?>
                <?php }?>
            </div>
        </a>
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" onclick="editRepaymentDay_Onclick()">
            <div class="weui-cell__bd">
                Repayment Day
            </div>
            <div class="weui-cell__ft" id="div_due_date" data-due="<?php echo $due_date?:1;?>">
                <?php if(!$due_date){?>
                    Not Set
                <?php }else{?>
                    <?php echo $due_date;?>
                <?php }?>
            </div>
        </a>
    </div>
    <?php if($output['show_semi_balloon']){?>
        <div class="weui-cells">
            <a class="weui-cell weui-cell_access" onclick="editSemiBalloon_Onclick()">
                <div class="weui-cell__bd">
                    Repay Principal Period(Semi Balloon)
                </div>
                <div class="weui-cell__ft" id="div_semi_balloon" data-period="<?php echo $principal_period?:'2';?>">
                    <?php if(!$principal_period){?>
                        Not Set
                    <?php }else{?>
                        <?php echo $principal_period;?>
                    <?php }?>
                </div>
            </a>
        </div>
    <?php }?>
    <div class="weui-cells__title">
        Loan Category
    </div>
    <div class="weui-cells">
        <?php foreach ($output['category'] as $k => $v){?>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" style="width: 40%;display: block">
                    <?php echo $v['alias']?>
                </div>
                <div class="weui-cell__bd" style="width: 20%">
                    <input class="weui-switch ios-checkbox"
                           data-category-id="<?php echo $v['category_id']?>"
                           type="checkbox" <?php if(!$v['is_close']) echo 'checked="checked"'?>/>
                </div>
                <div class="weui-cell__ft div-edit-category-item" style="width: 40%;display: <?php if($v['is_close']) echo 'none'?>">
                    <a class="weui-cell_link" href='<?php echo getWapOperatorUrl("client_profile","editCreditCategoryItemPage",array("cate_id"=>$v["category_id"],"member_id"=>$_GET["id"])) ?>'>
                        <span><?php echo $v['interest_package_name']?></span>
                        <br/>
                        <span> <?php echo $v['sub_product_name']?></span>
                    </a>
                </div>
            </div>

        <?php }?>
    </div>



</div>
<script>
    function editClientResidence_Onclick(){
        if(window.operator){
            window.operator.memberPlaceOfResidence('<?php echo $_GET['id'];?>');
            return;
        }
    }
    function editRepaymentDay_Onclick(){
        var dayArr = [], days = 28, i = 1, dueDate = parseInt($('#div_due_date').data('due'));
        for(i;i <= days;i++){
            dayArr.push({label:i,value:i});
        }
        var _picker_id="picker_repayment_day";

        weui.picker(dayArr,{
            defaultValue:[dueDate],
            onConfirm:function(_ret){
                if(_ret.length==0) return;
                var newDay = _ret[0];
                if(newDay == dueDate){
                    return;
                }
                showMask();
                yo.loadData({
                    _c: 'client_profile',
                    _m: 'ajaxSubmitClientRepaymentDay',
                    param: {cid: '<?php echo $_GET['cid'];?>',day: newDay},
                    callback: function (_o){
                        hideMask();
                        if(_o.STS){
                            $('#div_due_date').html(newDay);
                            $("#div_due_date").data("due",newDay);
                        }

                    }
                });

            },
            id:_picker_id
        });
    }
    function editSemiBalloon_Onclick(){
        var dayArr = [],  _default_period = parseInt($('#div_semi_balloon').data('period'));

        dayArr.push({label:'2 Months',value:2});
        dayArr.push({label:'3 Months',value:3});

        var _picker_id="picker_semi_balloon";
        weui.picker(dayArr,{
            defaultValue:[_default_period],
            onConfirm:function(_ret){
                if(_ret.length==0) return;
                var newDay = _ret[0];
                if(newDay == _default_period){
                    return;
                }
                showMask();
                yo.loadData({
                    _c: 'client_profile',
                    _m: 'ajaxSubmitClientPrincipalPeriod',
                    param: {cid: '<?php echo $_GET['cid'];?>',period: newDay},
                    callback: function (_o){
                        hideMask();
                        if(_o.STS){
                            $('#div_semi_balloon').html(newDay);
                            $("#div_semi_balloon").data("period",newDay);
                        }

                    }
                });

            },
            id:_picker_id
        });
    }

    $('.ios-checkbox').click(function(){
        var self_btn = $(this),
            member_id="<?php echo $_GET['id']?>",
            category_id = self_btn.data('category-id'),
            state = self_btn.is(':checked')?0:1;


        showMask();
        yo.loadData({
            _c: 'client_profile',
            _m: 'ajaxSubmitLoanCategoryState',
            param: {member_id: member_id, category_id: category_id, state: state},
            callback: function (_o) {
                hideMask();
                if (_o.STS) {
                    alert("Saved Successfully");
                    if(state==0){
                        self_btn.closest(".weui-cell").find(".div-edit-category-item").show();
                    }else{
                        self_btn.closest(".weui-cell").find(".div-edit-category-item").hide();
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });
</script>
