<?php if (!$asset) $asset = $output['asset']; ?>
    <div class="col-sm-6" style="padding: 0px 5px 0px 0px">
        <div class="basic-info container" style="margin-top: 10px">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-list-ul"></i>Asset Info</h5>
            </div>
            <div class="content">
                <table class="table table-no-background">
                    <?php if ($asset['relative_list']) { ?>
                        <tr>
                            <td>Owner</td>
                            <td colspan="3">
                                <?php foreach ($asset['relative_list'] as $relative) { ?>
                                    <label style="display: inline-block;margin: 0 15px 0 0;<?php echo $relative['relative_id'] == 0 ? 'font-style: italic' : ''?>">
                                        <?php echo $relative['relative_id'] == 0 ? 'Own' : $relative['relative_name'];?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Asset Name</td>
                        <td><label><?php echo $asset['asset_name']?></label></td>
                        <td>Asset Id</td>
                        <td><label><?php echo $asset['asset_sn']?></label></td>
                    </tr>
                    <tr>
                        <td>Asset Type</td>
                        <td>
                            <?php $asset_type_list=(new certificationTypeEnum())->Dictionary();?>
                            <label>
                                <?php echo $asset_type_list[$asset['asset_type']]?>
                            </label>
                        </td>
                        <td>Certification Type</td>
                        <td>
                            <label>
                                <?php echo $asset['asset_cert_type']?>
                            </label>
                        </td>
                    </tr>
                </table>
                <div style="border-top: 1px solid #CCC">
                    <?php
                    $image_list = array();
                    foreach ($asset['image_list'] as $img_item) {
                        $image_list[] = $img_item['image_url'];
                    }
                    include(template(":widget/item.image.viewer.list"));
                    ?>
                </div>
            </div>
        </div>

        <div class="basic-info container" style="margin-top: 10px">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-list-ul"></i>Evaluate Info</h5>
            </div>
            <div class="content">
                <?php if($output['asset_evaluate']){?>
                    <table class="table table-bordered">
                        <tr>
                            <td>Evaluation</td>
                            <td>Operator</td>
                            <td>Remark</td>
                        </tr>
                        <tr>
                            <td><em style="font-weight: 600"><?php echo ncPriceFormat($output['asset_evaluate']['evaluation'])?></em></td>
                            <td><?php echo $output['asset_evaluate']['operator_name']?></td>
                            <td><?php echo $output['asset_evaluate']['remark']?></td>
                        </tr>
                    </table>
                <?php }else{?>
                    <?php require template(":widget/no_record")?>
                <?php }?>
            </div>
        </div>
    </div>

    <div class="col-sm-6" style="padding: 0px 0px 0px 5px">
        <div class="basic-info container" style="margin-top: 10px">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-list-ul"></i>Rental Info</h5>
            </div>
            <div class="content">
                <?php if($output['asset_rental']){?>
                    <table class="table table-bordered">
                        <tr>
                            <td>Renter</td>
                            <td>Monthly Rent</td>
                            <td>Operator</td>
                            <td>Time</td>
                        </tr>
                        <tr>
                            <td><?php echo $output['asset_rental']['renter']?></td>
                            <td><em style="font-weight: 600"><?php echo ncPriceFormat($output['asset_rental']['monthly_rent'])?></em></td>
                            <td><?php echo $output['asset_rental']['operator_name']?></td>
                            <td><?php echo $output['asset_rental']['update_time'] ? timeFormat($output['asset_rental']['update_time']) : timeFormat($output['asset_rental']['create_time'])?></td>
                        </tr>
                        <?php if (count($output['asset_rental']['images'])) { ?>
                            <tr>
                                <td colspan="4">
                                    <?php
                                    $image_list = array();
                                    foreach ($output['asset_rental']['images'] as $img_item) {
                                        $image_list[] = $img_item['image_path'];
                                    }
                                    include(template(":widget/item.image.viewer.list"));
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php }else{?>
                    <?php require template(":widget/no_record")?>
                <?php }?>
            </div>
        </div>

        <?php
        $survey_info = $asset['survey_info'];
        $survey_items = my_json_decode($survey_info['survey_json']);
        $survey_items_type = my_json_decode($survey_info['survey_json_type']);
        $survey_result = my_json_decode($asset['research_text']);
        ?>
        <?php if(count($survey_result)){?>
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-list-ul"></i>Survey Info</h5>
                </div>
                <div class="content">
                    <ul class="list-group">
                        <?php foreach($survey_items as $item_k=>$item_name){?>
                            <li class="list-group-item">
                        <span class="badge">
                            <?php
                            if($survey_items_type[$item_k] == assetSurveyType::CHECKBOX){
                                if($survey_result[$item_k]){
                                    echo 'Yes';
                                }else{
                                    echo 'No';
                                }
                            }else{
                                echo $survey_result[$item_k];
                            }
                            ?>
                        </span>
                                <?php echo $item_name?>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        <?php }?>

        <div class="basic-info container" style="margin-top: 10px">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-list-ul"></i>Storage FLow</h5>
            </div>
            <?php $storage_list=$output['storage_list'];$flow_type=(new assetStorageFlowType())->Dictionary();?>
            <div class="content">
                <?php if(count($storage_list)){?>
                    <table class="table table-bordered">
                        <tr>
                            <td>Time</td>
                            <td>From</td>
                            <td>To(Holder)</td>
                            <td>Type</td>
                        </tr>
                        <?php foreach($storage_list as $item){?>
                            <tr>
                                <td><?php echo $item['create_time']?></td>
                                <td><label><?php echo $item['from_operator_name']?:$client_info['login_code']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['from_branch_name']?:'client'?></em></td>
                                <td><label><?php echo $item['to_operator_name']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['to_branch_name']?></em></td>
                                <td><?php echo $flow_type[$item['flow_type']]?></td>
                            </tr>
                        <?php }?>
                    </table>
                <?php }else{?>
                    <?php require template(":widget/no_record")?>
                <?php }?>
            </div>
        </div>

        <div class="basic-info container" style="margin-top: 10px">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-list-ul"></i>Loan Contract</h5>
            </div>
            <?php $loan_list=$output['loan_list'];?>
            <div class="content">
                <?php if(count($loan_list)){?>
                    <table class="table table-bordered">
                        <tr>
                            <td>Contract-SN</td>
                            <td>Start-Date</td>
                            <td>Product</td>
                            <td>Principal</td>
                            <td>Outstanding</td>
                        </tr>
                        <?php foreach($loan_list as $item){?>
                            <tr>
                                <td><?php echo $item['contract_sn']?></td>
                                <td><?php echo $item['start_date']?></td>
                                <td><?php echo $item['sub_product_name']?></td>
                                <td><?php echo $item['principal_out']?></td>
                                <td><?php echo $item['principal_outstanding']?></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td colspan="10"><label>Total Outstanding Principal:<?php echo $output['principal_outstanding']?></label></td>
                        </tr>
                    </table>
                <?php }else{?>
                    <?php require template(":widget/no_record")?>
                <?php }?>
            </div>
        </div>
    </div>
<?php include(template(":widget/item.image.viewer.js"));?>