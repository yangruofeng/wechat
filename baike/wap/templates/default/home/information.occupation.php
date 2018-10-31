<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<link rel="stylesheet" type="text/css" href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/mobileSelect/mobileSelect.css">
<style>
.industry-list-flex {
    display: block!important;
    padding-left: 0!important;
    width: 100%;
}
.letter-sort {
    position: fixed;
    top: 7rem;
    right: 0;
    width: 30px;
    font-size: .7rem;
    font-weight: 500;
    color: #f60;
}
.letter-sort span {
    display: block;
    text-align: center;
    padding: 5px 0;
}
.wap {
    display: block;
    overflow-y: auto;
    position: relative;
}
.industry-list {
    margin-right:  30px;
}
.industry-wrap {
    padding: .3rem 0 .3rem 0;
    font-size: .7rem;
}
.industry-initials {
    padding: 0 8px;
    background: #f1f1f1;
    height: 1.2rem;
    line-height: 1.2rem;
    font-weight: 500;
}
.industry-item {
    flex: 1;
    padding-left: .5rem;
}
.industry-item label {
    padding-right: .6rem;
    display: block;
}
.aui-list label {
    font-size: .7rem;
}
</style>
<?php include_once(template('widget/inc_header'));?>
<?php
$member_industry_key=array_keys($output['member_industry']);
$letters = array_keys($output['industry_list']);
$client_info=$output['member_info'];
?>
<div class="wap">
    <form id="frm_worktype" class="customer-form custom-form">
        <input type="hidden" name="uid" value="<?php echo $client_info['uid']?>">
        <ul class="aui-list aui-form-list loan-item">
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <label class="aui-list-item-label label">
                        Work Type
                    </label>
                    <div class="aui-list-item-input label-on">
                        <div class="mui_select_block">
                            <input type="hidden" name="work_type" id="workType" value="<?php echo $client_info['work_type'];?>">
                            <input type="text" disabled class="mui_select" id="workTypeSelect" value="<?php if($client_info['work_type']){echo $output['work_type'][$client_info['work_type']];}else{ echo 'Please Select'; }?>">
                            <i class="aui-iconfont aui-icon-down"></i>
                        </div>
                        
                    </div>
                </div>
            </li>
                            
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="">
                        <input type="checkbox" id="chk_own_business" name="is_with_business" <?php if($client_info['is_with_business']) echo 'checked' ?>/> <label for="chk_own_business">Own Business</label>
                    </div>

                </div>
            </li>
            <li class="aui-list-item div_industry_list"  id="div_industry_list" style="<?php if(!$client_info['is_with_business']) echo 'display:none'?>">
                <div class="industry-list-flex">
                    <div class="letter-sort" id="letterSort">
                        <?php foreach ($letters as $k => $v){ ?>
                            <span letter="<?php echo strtoupper($v); ?>"><?php echo strtoupper($v); ?></span>
                        <?php } ?>
                    </div>
                    <div class="industry-list" id="industryList">
                        <?php foreach ($output['industry_list'] as $k => $industry) {?>
                            <div class="industry-wrap clearfix">
                                <div class="industry-initials" id="fristLetterItem<?php echo strtoupper($k);?>"><?php echo $k;?></div>
                                <div class="industry-item">
                                    <?php foreach ($industry as $key => $value) {?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="industry_item_<?php echo $value['uid']?>" value="<?php echo $value['uid']?>" <?php if(in_array($value['uid'],$member_industry_key)) echo 'checked'?>> <?php echo $value['industry_name']?>
                                        </label>
                                    <?php }?>
                                </div>
                            </div>
                        <?php } ?>   
                    </div>
                </div>
            </li>
        </ul>
        <div style="padding: 1rem;">
            <button type="button" onclick="btn_submit_onclick()" class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-r-15">
                    Submit
            </button>
        </div>
    </form>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/mobileSelect/mobileSelect.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict(); 
    $(function () {
        $("#chk_own_business").click(function(){
            var _sts=($(this).prop('checked'));
            if($(this).is(":checked")){
                $("#div_industry_list").show();
            }else{
                $("#div_industry_list").hide();
            }
        });
        $('.wap').css('height','568px');
        $('#letterSort span').click(function(){
            var letter = $J(this).attr('letter'), li = 'fristLetterItem' + letter;
            var litop = $J('#' + li).offset().top - $J('.wap').offset().top + $J('.wap').scrollTop();
                // 取字母标记位置top值  + 当前ul滚动条的top值 = 当前需要的top值
            $J('.wap').animate({scrollTop: litop}, 500);

        });
    });

    var type = '<?php echo json_encode($output['work_type']);?>';
    type = eval("("+type+")");

    var typeArr = [], typeIndex = [], defaultWorkType = $('#workType').val(), defaultIndex = 0;
    $.each(type,function(index,value){
        typeArr.push(value);
        typeIndex.push(index);
　　});
    $.each(typeIndex,function(index, value){
        if(defaultWorkType == value){
            defaultIndex = index;
        }
　　});

  var dueDateSelect = new MobileSelect({
    trigger: '.mui_select_block',
    title: 'Please Select',
    wheels: [
      {data: typeArr}
    ],
    triggerDisplayData: false,
    position:[defaultIndex],// 默认
    transitionEnd:function(indexArr, data){
        //console.log(data);
    },
    callback:function(indexArr, data){
        $('#workType').val(typeIndex[indexArr]);
        $('#workTypeSelect').val(data[0]);
      }
  });

    function btn_submit_onclick(){
        var _values=$("#frm_worktype").getValues();
        if(_values.work_type == 0){
            hint('Please select work type');
            //alert()

            return
        }
        yo.loadData({
            _c: 'home',
            _m: 'submitClientWorkType',
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    hint(_o.MSG);
                    setTimeout(function(){
                        window.location.href = '<?php echo getUrl('home', 'information', array('cid'=>$_GET['cid'],'id'=>$_GET['id'],'back'=>'search','time'=>time()), false, WAP_OPERATOR_SITE_URL);?>';
                    }, 2000);
                } else {
                    hint(_o.MSG);
                }
            }
        });
    }
</script>
