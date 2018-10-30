<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Prepayment Limit</h3>
          <ul class="tab-base">
              <li><a class="current"><span> Default List</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container" style="width: 1000px">
      <div class="row">
          <div class="col-sm-6">
              <table class="table table-hover table-bordered">
                  <thead>
                  <tr class="table-header">
                      <td style="width: 100px"><?php echo 'Loan Days';?></td>
                      <td style="width: 200px"><?php echo 'Limit Days';?></td>
                      <td style="width: 200px"><?php echo 'Function';?></td>
                  </tr>
                  </thead>
                  <tbody class="table-body">
                  <?php $list = $output['list'];?>
                  <?php if(count($list) > 0){?>
                      <?php foreach($list as $row){?>
                          <tr data-loan_days="<?php echo $row['loan_days']?>">
                              <td>
                                  <?php echo $row['loan_terms'] ?>
                              </td>
                              <td>
                                <span class="span-title">
                                    <?php echo $row['limit_title'] ?>
                                </span>
                                  <select class="form-control sel-limit" style="display: none">
                                      <option value="0">No Limit</option>
                                      <?php foreach($output['limit_keys'] as $key=>$val){
                                          if($key<30 && $row['loan_days']>=30) continue;
                                          if($key>$row['loan_days']) continue;
                                          ?>
                                          <option value="<?php echo $key?>" <?php if($key==$row['limit_days']) echo 'selected'?>>
                                              <?php echo $val;?>
                                          </option>
                                      <?php }?>
                                  </select>
                              </td>
                              <td>
                                  <div class="div-edit-group">
                                      <a class="btn btn-link btn-xs" onclick="btn_start_edit_onclick(this)"><i class="fa fa-edit"></i> Edit</a>
                                  </div>
                                  <div class="div-save-group" style="display: none">
                                      <a class="btn btn-link btn-xs" onclick="btn_save_edit_onclick(this)"><i class="fa fa-save"></i> Save</a>
                                      <a class="btn btn-link btn-xs" onclick="btn_cancel_edit_onclick(this)"><i class="fa fa-close"></i> Cancel</a>
                                  </div>
                              </td>
                          </tr>
                      <?php }?>
                  <?php }else{ ?>
                      <tr>
                          <td colspan="3"><div class="no-record">No Data</div></td>
                      </tr>
                  <?php } ?>
                  </tbody>
              </table>
          </div>
          <div class="col-sm-6">
              <ul class="list-group">
                  <?php foreach($output['category_list'] as $cate){?>
                      <li class="list-group-item" style="border: none">
                          <a class="btn btn-default btn-block"
                             href="<?php echo getBackOfficeUrl("loan","setPrepaymentLimitSpecialPage",array("category_id"=>$cate['uid']))?>">
                              <?php echo $cate['category_name']?>
                          </a>
                      </li>
                  <?php }?>
              </ul>
          </div>

      </div>

    </div>
</div>
<script>
    function btn_start_edit_onclick(_e){
        var _tr=$(_e).closest("tr");
        _tr.find(".div-save-group").show();
        _tr.find(".div-edit-group").hide();
        _tr.find(".span-title").hide();
        _tr.find(".sel-limit").show();
    }
    function btn_save_edit_onclick(_e){
        var _tr=$(_e).closest("tr");
        var _loan_days=_tr.data("loan_days");
        var _limit_days=_tr.find(".sel-limit").val();
        $(document).waiting();
        yo.loadData({
            _c:"loan",
            _m:"editPrepaymentLimit",
            param:{loan_days:_loan_days,limit_days:_limit_days},
            callback:function(_o){
                $(document).unmask();
                if(!_o.STS){
                    alert(_o.MSG);
                }else{
                    _tr.find(".span-title").text(_o.DATA.title);
                    btn_cancel_edit_onclick(_e);
                }
            }
        });
    }
    function btn_cancel_edit_onclick(_e){
        var _tr=$(_e).closest("tr");
        _tr.find(".div-save-group").hide();
        _tr.find(".div-edit-group").show();
        _tr.find(".span-title").show();
        _tr.find(".sel-limit").hide();
    }

    
</script>
