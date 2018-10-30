<style>
    .table {
        margin-bottom: 0;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch Management</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
        </div>

        <div class="business-content">
            <div class="business-list">
                <?php if($output['branch_list']){ ?>
                    <?php foreach($output['branch_list'] as $item){?>
                        <div class="col-sm-4" style="padding: 20px;height: 250px">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label class="panel-title"><?php echo $item['branch_code']?></label>
                                </div>
                                <div class="panel-body">
                                    <table class="table">
                                        <tr>
                                            <td><label for="">Name</label></td>
                                            <td><?php echo $item['branch_name']?></td>
                                            <td><label for="">Phone</label></td>
                                            <td><?php echo $item['contact_phone']?></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Credit</label></td>
                                            <td <?php if($item['credit'] <= $item['balance']['Est.USD']){ ?>style="color: #f0ad4e;"<?php }?>>
                                                <?php echo ncPriceFormat($item['credit']);?>
                                            </td>
                                            <td><label for="">Limit</label></td>
                                            <td><?php echo ncPriceFormat($item['limit_value']);?></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Balance</label></td>
                                            <td colspan="3">
                                                <?php foreach($item['balance'] as $k => $v){ ?>
                                                    <span><?php echo $k;?>: <?php echo ncPriceFormat($v);?></span>&nbsp;&nbsp;&nbsp;
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Region</label></td>
                                            <td colspan="3"><?php echo $item['address_region']?></td>
                                        </tr>

                                    </table>
                                </div>
                                <div class="panel-footer">
                                    <span style="text-align: center">
                                        <a class="btn btn-default"
                                           href="<?php echo getUrl("treasure", "branchIndex", array("branch_id" => $item['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                            <i class="fa fa-edit"></i> Management</a>
                                    </span>
                                    <span style="text-align: center">
                                         <a class="btn btn-default"
                                            href="<?php echo getUrl('treasure', 'branchLimit', array('uid'=>$item['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                             <i class="fa fa-tasks"></i>
                                             Limit
                                         </a>
                                    </span>
                                    <span style="text-align: center">
                                         <a class="btn btn-default"
                                            href="<?php echo getUrl('treasure', 'branchCredit', array('uid'=>$item['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                             <i class="fa fa-tasks"></i>
                                             Credit
                                         </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                <?php }else{?>
                    <div style="width: 500px;margin:auto">
                        <?php include(template(":widget/no_record"))?>
                    </div>
                <?php }?>

            </div>
        </div>
    </div>
</div>
