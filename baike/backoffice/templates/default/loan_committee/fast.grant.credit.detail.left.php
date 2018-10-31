<div class="col-sm-6">
    <div class="basic-info">
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
        </div>
        <div class="content">
            <form class="form-horizontal" method="post" action="<?php echo getUrl('loan_committee', 'commitCreditApplication', array(), false, BACK_OFFICE_SITE_URL)?>">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                <input type="hidden" name="is_fast_grant" value="1">
                <table class="table">
                    <tbody class="table-body">

                    <tr>
                        <td><label class="control-label">Monthly Repayment Ability</label></td>
                        <td>
                            <input type="number" class="form-control input-h30" name="monthly_repayment_ability" value="<?php echo $analysis['ability']; ?>">
                            <div class="error_msg"></div>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label">Default Credit</label></td>
                        <td>
                            <input type="number" class="form-control input-h30 count_credit" name="default_credit" value="<?php echo $suggest_profile['default_credit']; ?>">
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Increase Credit By</label></td>
                        <td></td>
                    </tr>

                    <?php if ($output['member_assets']) { ?>
                        <?php foreach($output['member_assets'] as $val) {?>
                            <tr>
                                <td><span class="pl-25">
                                                <span><?php echo $val['asset_name']; ?></span>
                                                <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                            </span>
                                </td>
                                <td>
                                    <input type="number" class="form-control input-h30 count_credit" name="increase_credit[]" value="<?php echo $suggest_profile['increase'][$val['uid']]['credit']; ?>">
                                    <input type="hidden" name="asset_id[]" value="<?php echo $val['uid']; ?>">
                                </td>
                            </tr>
                        <?php }?>
                    <?php } else { ?>
                        <tr>
                            <td><span class="pl-25"></span></td>
                            <td>
                                No Record
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><label class="control-label">Max Credit</label></td>
                        <td>
                            <input type="number" class="form-control input-h30" name="max_credit" value="<?php echo $suggest_profile['max_credit']; ?>">
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Invalid Terms</label></td>
                        <td>
                            <div class="input-group" style="width: 100%">
                                <input type="number" class="form-control input-h30" name="invalid_terms" value="<?php echo $suggest_profile['terms']; ?>">
                                <span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>
                            </div>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <?php if(count($output['package_list'])){?>
                        <tr>
                            <td><label class="control-label">Interest Package</label></td>
                            <td>
                                <div class="input-group" style="width: 100%">
                                    <select class="form-control input-h30" name="package_id">
                                        <option value="0">Default</option>
                                        <?php foreach($output['package_list'] as $package_item){?>
                                            <option value="<?php echo $package_item['uid']?>"><?php echo $package_item['package']?></option>
                                        <?php }?>
                                    </select>
                                     <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="showPackageInterestSetting(this)">Check InterestRate</button>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php }else{?>
                        <tr style="display: none">
                            <td>
                                <input type="hidden" name="package_id" value="0">
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>

                <table class="table" style="margin-top: 20px">
                    <tbody>
                    <tr>
                        <td><label class="control-label">Remark</label></td>
                        <td>
                            <textarea class="form-control" name="remark" style="width: 100%;height: 50px"></textarea>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Approve By</label></td>
                        <td>
                            <?php foreach ($output['committee_member'] as $val) { ?>
                                <label class="checkbox-inline col-sm-6" style="margin-left: 0px">
                                    <input type="checkbox" name="committee_member[]" value="<?php echo $val['user_id'] ?>"><?php echo $val['user_name'] ?>
                                </label>
                            <?php } ?>
                            <div class="error_msg"></div>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label">Auto Authorize</label></td>
                        <td>
                            <!--                                    <label class="checkbox-inline" style="margin-left: 0px">-->
                            <input type="checkbox" name="is_auto_Authorize" value="1">
                            <!--                                    </label>-->
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center">
                            <button type="button" class="btn btn-info" id="fast-submit" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Commit' ?></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script>
        function showPackageInterestSetting(_e){
            var _package_id=$(_e).closest("td").find("select").val();
            window.open("<?php echo getUrl("web_credit","showPackageInterestSetting",array(),false,BACK_OFFICE_SITE_URL)?>"+"&package_id="+_package_id);
        }
    </script>
</div>