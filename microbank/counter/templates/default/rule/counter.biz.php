<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page" style="max-width: 700px">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php
    $list = $output['list'];
    $setting_value = $output['setting_value'];
    ?>

    <div class="ibox-title">
        <h5><i class="fa fa-id-card-o"></i>Counter Biz</h5>
    </div>

    <div class="content" style="padding: 10px" >
        <div style="border: 1px solid lightgrey">
            <table class="table">
                <thead>
                    <tr style="background-color: #DEDEDE">
                        <td width="40%"><label class="control-label">Subject</label></td>
                        <td width="30%"><label class="control-label">Is Require CT Check</label></td>
                        <td width="30%"><label class="control-label">Min Check Amount</label></td>
                    </tr>
                </thead>
                <?php foreach( $list as $code=>$name ){ ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $setting_value[$code]['is_require_ct_approve'] ? 'Yes' : 'No'; ?></td>
                    <td><?php
                        if($setting_value[$code]['min_approve_amount'] > 0){
                            echo $setting_value[$code]['min_approve_amount']." USD";
                        }else{
                            echo 'Not Setting';
                        }
                        ?></td>
                </tr>
                <?php } ?>
            </table>

        </div>

    </div>


</div>



