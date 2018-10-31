
<?php
$partnerBizTypeLang = enum_langClass::getPartnerBizTypeLang();
$setting_info = $output['setting_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Partner Limit Setting</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('dev', 'partnerLimit', array(), false, BACK_OFFICE_SITE_URL ); ?>"><span>List</span></a></li>
                <li><a class="current"><span>Setting</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
       <div>
           <form  class="form-horizontal" method="post" id="frm_partner_limit_setting">

               <input type="hidden" name="act" value="dev">
               <input type="hidden" name="op" value="savePartnerLimitSetting">

               <input type="hidden" name="uid" value="<?php echo $setting_info['uid']?:0; ?>">

               <table class="table table-bordered table-hover" style="width: 500px;">
                   <tr>
                       <td class="text-right">
                           <label for="">Partner</label>

                       </td>
                       <td class="text-left">
                           <select name="partner_code" id="" class="form-control">
                               <?php foreach( $output['partner_list'] as $partner ){ ?>
                                   <option value="<?php echo $partner['partner_code']; ?>" <?php echo $setting_info['partner_code']==$partner['partner_code']?'selected':''; ?> ><?php echo $partner['partner_name']; ?></option>
                               <?php } ?>
                           </select>
                           <div class="error_msg"></div>
                       </td>
                   </tr>
                   
                   <tr>
                       <td class="text-right">
                           <label for="">Business Type</label>

                       </td>
                       <td class="text-left">
                           <select name="biz_type" id="" class="form-control">
                               <?php foreach( (new partnerBizTypeEnum())->toArray() as $biz_type ){ ?>
                                   <option value="<?php echo $biz_type; ?>" <?php echo $setting_info['biz_type']==$biz_type?'selected':''; ?> ><?php echo $partnerBizTypeLang[$biz_type]?:$biz_type; ?></option>
                               <?php } ?>
                           </select>
                           <div class="error_msg"></div>
                       </td>
                   </tr>

                   <tr>
                       <td class="text-right">
                           <label for=""> Per Time</label>

                       </td>
                       <td class="text-left">
                           <input type="number" name="per_time" class="form-control" value="<?php echo $setting_info['per_time']; ?>">
                           <div class="error_msg"></div>
                       </td>
                   </tr>

                   <tr>
                       <td class="text-right">
                           <label for="">Per Day</label>
                       </td>
                       <td class="text-left">
                           <input type="number" name="per_day" class="form-control" value="<?php echo $setting_info['per_day']; ?>">
                           <div class="error_msg"></div>
                       </td>
                   </tr>


                   <tr>
                       <td colspan="2" class="text-center" style="padding: 20px">
                           <button class="btn btn-primary" style="width: 150px" onclick="formSubmit();">Submit</button>
                           <button type="button" class="btn btn-default" style="width: 150px;margin-left: 20px" onclick="javascript:history.back(-1)">Cancel</button>
                       </td>
                   </tr>

               </table>
           </form>


       </div>
    </div>
</div>
<script>

    function formSubmit()
    {
        $('#frm_partner_limit_setting').submit();
    }

</script>
