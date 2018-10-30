<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/insurance.css?v=2" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('insurance', 'addProduct', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <?php $list = $output['list'];$item_count = $output['item_count'];$contract_list = $output['contract_list'];?>
    <div class="container">
        <div class="business-content">
            <style>
                .product-info{
                    width: 100%;
                    background: #FFF;
                    margin-bottom: 15px;
                }
                .product-info .info {
                    padding: 15px;
                    position: relative;
                    height: 80px;
                    border-bottom: 1px solid #e7eaec;
                }
                .product-info .name{
                    font-size: 20px;
                    font-weight: 600;
                }
                .product-info .product-report {
                    height: 100px;
                }

                .product-info .product-report .item {
                    width: 25%;
                    text-align: center;
                    float: left;
                    padding: 30px 0;
                    max-height: 110px;
                    border-right: 1px solid #e7eaec;
                }
                .product-info .product-report .item:last-child {
                    border-right:0;
                }
                .product-info .product-report .item p {
                    margin-bottom: 0;
                    font-size: 20px;
                    font-weight: 600;
                    margin-top: 5px;
                    color: #f60;
                }
                .custom-btn-group a {
                  margin-left: 8px;
                }
                .custom-btn span {
                  padding: .6em 1em;
                }
            </style>
            <?php foreach ($list as $key => $value) {?>
              <div class="product-info">
                  <div class="info">
                      <p class="name"><?php echo $value['product_name'];?></p>
                      <p>Code:  <?php echo $value['product_code'];?>
                        <span style="margin-left: 50px;">State: <?php if($value['state'] == 10){echo 'Temporary';}elseif($value['state'] == 20){echo 'Valid';}elseif($value['state'] == 30){echo 'Invalid';}elseif($value['state'] == 40){echo 'Invalid';} ?></span>
                      </p>
                      <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('insurance', 'showProductHistory', array('uid'=>$value['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-reorder"></i>History Versions</span>
                        </a>
                        <?php if ($value['state'] == 10) {?>
                          <a class="custom-btn custom-btn-secondary" href="javascrript:;" onclick="release_product('<?php echo $value['uid']?>')">
                            <span><i class="fa fa-long-arrow-up"></i>Active</span>
                          </a>
                        <?php }?>
                        <?php if ($value['state'] == 20) {?>
                          <a class="custom-btn custom-btn-secondary" href="javascrript:;" onclick="unshelve_product('<?php echo $value['uid']?>')">
                            <span><i class="fa fa-long-arrow-down"></i>Inactive</span>
                          </a>
                        <?php }?>
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('insurance', 'addProduct', array('uid'=>$value['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-edit"></i>Edit</span>
                        </a>
                      </div>
                  </div>
                  <div class="product-report clearfix">
                      <div class="item">
                          Insurance Contract
                          <p><?php echo $contract_list[$value['uid']]['count']?:0;?></p>
                      </div>
                      <div class="item">
                          Insurance Client
                          <p><?php echo count($contract_list[$value['uid']]['accounts']);?></p>
                      </div>
                      <div class="item">
                          Insurance Price
                          <p><?php echo $contract_list[$value['uid']]['price']?:'0.00';?></p>
                      </div>
                      <div class="item">
                          Items
                          <p><?php echo $item_count[$value['uid']]?:0;?></p>
                      </div>
                  </div>
              </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=1"></script>
<script>
    function release_product(_uid) {
        if (!_uid) {
            return;
        }
        yo.loadData({
            _c: "insurance",
            _m: "releaseProduct",
            param: {uid: _uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert('Saved Successfully',1, function(){
                        window.location.reload();
                    });
                } else {
                    alert( _o.MSG, 2);
                }
            }
        });
    }

    function unshelve_product(_uid) {
        if (!_uid) {
            return;
        }
        yo.loadData({
            _c: "insurance",
            _m: "unShelveProduct",
            param: {uid: _uid},
            callback: function (_o) {
                if (_o.STS) {
                  alert(_o.MSG,1,function(){
                      window.location.reload();
                  });
                } else {
                    alert(_o.MSG, 2);
                }
            }
        });
    }
</script>
