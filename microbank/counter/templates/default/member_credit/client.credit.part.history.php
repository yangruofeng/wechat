<div role="tabpanel" class="tab-pane" id="tab_history">
    <?php if(count($client_authorized_history) > 0){?>
        <table class="table table-bordered authorized-history">
            <thead>
            <tr class="table-header">
                <td>Scene Photo</td>
                <td>Mortgages</td>
                <td>Loan Fee</td>
                <td>Contract State</td>
                <td>Authorized Time</td>
                <td>Function</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php foreach ($client_authorized_history as $k => $v) { ?>
                <tr>
                    <td>
                        <img class="scene" src="<?php echo getImageUrl($v['member_img'],120);?>" alt="">
                    </td>
                    <td>
                        <?php echo $v['mortgage_type']==1?'Mortgaged':'Redeem'; ?>
                    </td>

                    <td>
                        <?php echo ncPriceFormat($v['fee']); ?>
                    </td>
                    <td>
                        <?php
                        $contract_state_enum=(new authorizedContractStateEnum())->Dictionary();
                        echo $contract_state_enum[$v['state']];
                        ?>
                    </td>
                    <td>
                        <?php echo timeFormat($v['create_time']); ?>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl("member_credit","showAuthorizeContractDetail",array("uid"=>$v['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                                <span><i class="fa fa-vcard-o"></i>Detail</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }else{?>
        <div class="no-record">No history</div>
    <?php }?>
</div>