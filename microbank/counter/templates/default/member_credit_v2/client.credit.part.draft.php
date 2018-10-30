<div role="tabpanel" class="tab-pane" id="tab_draft">
    <div class="">
        <div class="well-sm">
            if you want to print out the contract to client for signature before client come to counter,you can create draft-contract now:
        </div>
        <div>
            <form>
                <ul class="list-inline">

                        <?php foreach ($detail['assets'] as $k => $v) { ?>
                    <li>
                            <div class="asset-info-wrap  form-group left">
                                <label class="col-sm-12 control-label mortgage-label">
                                    <div class="ck_wrap" style="padding: 10px">
                                        <p>
                                            <?php  echo $asset_enum[$v['asset_type']];?>:
                                            <em class="n"><?php echo $v['asset_name'];?></em>
                                        </p>
                                        <p>
                                            <?php echo $v['asset_cert_type']?>:
                                            <em class="n"><?php echo $v['asset_sn'];?></em>
                                        </p>
                                        <p>
                                            From Relative: <em><?php echo $v['relative_name']?$v['relative_name']:'Own'?></em>
                                        </p>

                                        <p>Credit: <span class=""><?php echo ncPriceFormat($v['credit']);?></span></p>
                                        <input type="checkbox" name="draft_mortgage_asset[]" value="<?php echo $v['member_asset_id'];?>" val="<?php echo $v['credit'];?>" />
                                        <span class="c-asset-state"><?php echo 'Mortgage';?></span>
                                    </div>
                                </label>
                            </div>
                    </li>
                        <?php } ?>

                </ul>

                <div class="col-sm-12 form-group">
                    <div class="operation">
                        <a class="btn btn-primary" onclick="submitDraft();">Submit</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>