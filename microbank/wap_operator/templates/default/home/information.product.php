<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap information-wrap">
  <?php $info = $output['info'];$limit_product = $output['limit_product'];?>
  <ul class="aui-list request-detail-ul">
      <li class="aui-list-item f">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Product Code
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $info['sub_product_code'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
          Product Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $info['sub_product_name'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item f">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Repayment Type
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $info['repayment_type'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item f">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            State
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $limit_product?'Unavailable':'Available';?>
          </div>
        </div>
      </li>
    </ul>
    <div style="padding: 1rem;">
      <?php if($limit_product){?>
        <button type="button" onclick="btn_submit_onclick()" class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-r-15">
        Enable
      </button>
      <?php }else{?>
        <button type="button" onclick="btn_submit_onclick()" class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-r-15">
        Disable
      </button>
      <?php }?>
      
    </div>
</div>
<form id="frm_limit_product">
  <input type="hidden" name="product_code" value="<?php echo $info['sub_product_code'];?>">
  <input type="hidden" name="member_id" value="<?php echo $_GET['member_id'];?>">
  <input type="hidden" name="state" value="<?php echo $limit_product?1:0;?>">
</form>
<script type="text/javascript">
function btn_submit_onclick(){
  var _values=$("#frm_limit_product").getValues();
  console.log(_values)
  return
    yo.loadData({
        _c: 'home',
        _m: 'submitLimitProduct',
        param: _values,
        callback: function (_o) {
            if (_o.STS) {
                hint(_o.MSG);
                setTimeout(function(){
                  window.location.href = '<?php echo getUrl('home', 'information', array('cid'=>$_GET['cid'],'id'=>$_GET['member_id'],'back'=>'search','time'=>time()), false, WAP_OPERATOR_SITE_URL);?>';
                }, 2000);
            } else {
                hint(_o.MSG);
            }
        }
    });
}
</script>
