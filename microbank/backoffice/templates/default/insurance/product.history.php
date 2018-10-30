<style>
    .product-info {
        width: 98%;
        background: #FFF;
        margin: 15px;
    }

    .product-info .info {
        padding: 15px;
        position: relative;
        height: 85px;
        /*border-bottom: 1px solid #e7eaec;*/
    }

    .product-info .name {
        font-size: 20px;
        font-weight: 600;
    }

    .product-info .product-report {
        height: 90px;
    }

    .product-info .product-report .item {
        width: 25%;
        text-align: center;
        float: left;
        padding: 20px 0;
        max-height: 110px;
        border-top: 1px solid #e7eaec;
        border-right: 1px solid #e7eaec;
        font-weight: 600;
    }

    .product-info .product-report .item:nth-child(4n) {
        border-right: 0;
    }

    .product-info .product-report .item p {
        margin-bottom: 0;
        font-size: 20px;
        margin-top: 5px;
        color: #f60;
    }

    .product-valid, .product-history, .product-release {
        margin-top: 5px;
        padding: 6px 12px;
    }

    .version-num {
        width: 40px;
        position: absolute;
        top: 23px;
        border-radius: 20px;
        text-align: center;
        line-height: 40px;
        border: 1px solid #9e9e9e;
        font-size: 20px;
        font-weight: 500;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'addProduct', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>History Versions</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <?php $item_count = $output['item_count'];$contract_list = $output['contract_list']; ?>
            <?php $version = count($output['product_history']);foreach($output['product_history'] as $product){?>
                <div class="product-info">
                    <div class="info">
                        <div class="version-num"><?php echo $version;--$version;?></div>
                        <div style="float: left;padding-left: 60px">
                        <p class="name"><a style="text-decoration: none" href="<?php echo getUrl('insurance', 'showProduct', array('uid' => $product['uid'], 'is_edit' => false), false, BACK_OFFICE_SITE_URL)?>"><?php echo $product['product_name']?></a></p>
                        <p>
                            <span>Code:  <?php echo $product['product_code']?></span>
                            <span style="margin-left: 50px">State:  <?php if($product['state'] == 10){echo 'Temporary';}elseif($product['state'] == 20){echo 'Valid';}elseif($product['state'] == 30){echo 'Invalid';}elseif($product['state'] == 40){echo 'Invalid';} ?></span>
                            <span style="margin-left: 50px">
                                Active Time:
                                <?php echo $product['start_time'] ? (timeFormat($product['start_time']) . ' -- ' . ($product['end_time'] ? timeFormat($product['end_time']) :'')) : '' ?>
                            </span>
                        </p>
                        </div>
                    </div>
                    <div class="product-report clearfix">
                      <div class="item">
                          Insurance Contract
                          <p><?php echo $contract_list[$product['uid']]['count']?:0;?></p>
                      </div>
                      <div class="item">
                          Insurance Client
                          <p><?php echo count($contract_list[$product['uid']]['accounts']);?></p>
                      </div>
                      <div class="item">
                          Insurance Price
                          <p><?php echo $contract_list[$product['uid']]['price']?:'0.00';?></p>
                      </div>
                      <div class="item">
                          Items
                          <p><?php echo $item_count[$product['uid']]?:0;?></p>
                      </div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<script>
    function release_product(_uid) {
        if (!_uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseProduct",
            param: {uid: _uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>
