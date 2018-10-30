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
     .industry-wrap {
         display: block;
         margin-bottom: 10px;
     }
     .industry-item {
        padding: 0 15px;
     }
     .div_industry_list {
        display: block;
        overflow-y: auto;
        position: relative;
        height: 600px;
        border: 1px solid #ddd;
     }
     .letter-sort {
        position: absolute;
        top: 10px;
        right: 0;
        width: 30px;
        font-size: .7rem;
        font-weight: 500;
        color: #f60;
    }
    .letter-sort > div {
        position: fixed;
    }
    .letter-sort span {
        display: block;
        text-align: center;
        padding: 5px 0;
    }
    .letter-sort span:hover {
        cursor: pointer;
    }
    .industry-initials {
        padding: 6px 15px;
        background: #f1f1f1;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .industry-list {
        margin-right: 30px;
    }
    .industry-list label {
        display: block;    
        font-weight: normal;
    }
    .industry-list label input {
        vertical-align: top;
    }
</style>
<?php
$work_type_lang=enum_langClass::getWorkTypeEnumLang();
$client_info=$output['client_info'];
$member_industry_key=array_keys($client_info['member_industry']);
$letters = array_keys($output['industry_list']);
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Work Type</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Work Type</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1000px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Work Type</h5>
                </div>
                <div class="content">
                    <form id="frm_worktype" method="POST" action="<?php echo getUrl('web_credit', 'editMemberWorkTypeAndIndustry', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <table class="table">
                            <tr>
                                <td><label class="control-label">Work Type</label></td>
                                <td>
                                    <select class="form-control" name="work_type" style="width: 250px">
                                        <option value="">Please Select</option>
                                        <?php foreach ($output['work_type'] as $key => $type) {?>
                                            <option value="<?php echo $key?>" <?php echo $key == $client_info['work_type'] ? 'selected' : ''?>><?php echo $work_type_lang[$key]?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td class="col-sm-2" style="vertical-align: top;">
                                    <label class="control-label">
                                        <input type="checkbox" id="chk_own_business" name="is_with_business" value="1" <?php if($client_info['is_with_business']) echo 'checked' ?>/>
                                        Own Business
                                    </label>
                                </td>
                                <td>
                                    <div class="div_industry_list" id="div_industry_list" style="<?php if(!$client_info['is_with_business']) echo 'display:none'?>">
                                        <div class="letter-sort" id="letterSort">
                                            <div>
                                                <?php foreach ($letters as $k => $v){ ?>
                                                    <span letter="<?php echo strtoupper($v); ?>"><?php echo strtoupper($v); ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="industry-list" id="industryList">
                                            <?php foreach ($output['industry_list'] as $k => $industry) {?>
                                                <div class="industry-wrap clearfix">
                                                    <div class="industry-initials" id="fristLetterItem<?php echo strtoupper($k);?>"><?php echo $k;?></div>
                                                    <div class="industry-item">
                                                        <?php foreach ($industry as $key => $value) {?>
                                                            <label class="">
                                                                <input type="checkbox" name="member_industry[industry_item_<?php echo $value['uid']?>]" value="<?php echo $value['uid']?>" <?php if(in_array($value['uid'],$member_industry_key)) echo 'checked'?>> <?php echo $value['industry_name']?>
                                                            </label>
                                                        
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
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
    $(function () {
        $("#chk_own_business").click(function(){
            if($(this).is(":checked")){
                $("#div_industry_list").show();
            }else{
                $("#div_industry_list").hide();
            }
        });
    });

    $('#letterSort span').click(function(){
        var letter = $(this).attr('letter'), li = 'fristLetterItem' + letter;
        var litop = $('#' + li).offset().top - $('.div_industry_list').offset().top + $('.div_industry_list').scrollTop();
            // 取字母标记位置top值  + 当前ul滚动条的top值 = 当前需要的top值
        $('.div_industry_list').animate({scrollTop: litop}, 500);

    });

    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        $("#frm_worktype").submit();
    }

</script>






