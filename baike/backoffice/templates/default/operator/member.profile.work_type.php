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
$work_type_lang=enum_langClass::getWorkTypeEnumLang();
$member_industry_key=array_keys($output['member_industry']);
$client_info=$output['member_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Profile</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'clientProfileIndex', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Request</span></a></li>
                <li><a  class="current"><span>Work Type</span></a></li>
            </ul>
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
                    <form id="frm_worktype">
                        <input type="hidden" name="uid" value="<?php echo $client_info['uid']?>">
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
                                <td>
                                    <label class="control-label">
                                        <input type="checkbox" id="chk_own_business" name="is_with_business" <?php if($client_info['is_with_business']) echo 'checked' ?>/>
                                        Own Business
                                    </label>
                                </td>
                                <td>
                                    <div id="div_industry_list" style="<?php if(!$client_info['is_with_business']) echo 'display:none'?>">
                                        <?php foreach ($output['industry_list'] as $industry) {?>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="industry_item_<?php echo $industry['uid']?>" <?php if(in_array($industry['uid'],$member_industry_key)) echo 'checked'?>> <?php echo $industry['industry_name']?>
                                            </label>
                                        <?php } ?>
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

    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        var _values=$("#frm_worktype").getValues();
        yo.loadData({
            _c: 'operator',
            _m: 'submitClientProfileWorkType',
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('operator', 'clientProfileIndex', array(), false, BACK_OFFICE_SITE_URL);?>';
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

</script>






