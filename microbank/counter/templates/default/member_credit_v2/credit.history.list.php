
<div><?php $rows = $data['data'];?>
            <?php if(count($rows) > 0){?>
                <table class="table table-bordered credit-history-table">
                    <thead>
                        <tr class="table-header">
                            <td>CID</td>
                            <td>Member Name</td>
                            <td>Scene Photo</td>
                            <td>Contract State</td>
                            <td>Total Fee</td>
                            <td>Authorized Time</td>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php foreach ($rows as $k => $v) {?>
                            <tr>
                                <td>
                                    <?php echo $v['obj_guid'];?>
                                </td>
                                <td>
                                    <?php echo $v['login_code'];?>
                                </td>
                                <td>
                                    <img class="scene" src="<?php echo getImageUrl($v['member_img'],imageThumbVersion::MAX_120);?>" alt="">
                                </td>
                                <td>
                                    <?php
                                    $contract_state_enum=(new authorizedContractStateEnum())->Dictionary();
                                    echo $contract_state_enum[$v['state']];
                                    ?> 
                                </td>
                                <td>
                                    <?php if( $v['fee'] > 0 ){ ?>
                                        USD <?php echo ncPriceFormat($v['fee']);?>
                                    <?php } ?>
                                    <br />
                                    <?php if( $v['fee_khr'] > 0 ){ ?>
                                        KHR <?php echo ncPriceFormat($v['fee_khr']);?>
                                    <?php } ?>

                                </td>
                                <td>
                                    <?php echo $v['create_time'];?>
                                </td>
                            </tr> 
                        <?php }?>
                    </tbody>
                </table>
            <?php }else{ ?>
                <div class="no-record">No Credit Record</div>
            <?php }?>
        
</div>
<?php include_once(template("widget/inc_content_pager"));?>