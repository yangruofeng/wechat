<style>
    .amount {
        text-align: right;
        text-wrap: avoid;
    }
</style>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Biz ID';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Member';?></td>
            <td><?php echo 'Amount';?></td>
            <td><?php echo 'Trx ID';?></td>
            <td><?php echo 'Trx State';?></td>
            <td><?php echo 'Trade ID';?></td>
            <td><?php echo 'Trade State';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){ ?>
            <tr>
              <td>
                  <?php echo $row['uid'] ?>
              </td>

              <td>
                  <?php echo $row['create_time'] ?>
              </td>
              <td>
                  <?php echo $row['login_code'] ?>
                  <?php if ($row['display_name']): ?>
                      <br/>
                      &nbsp;<span>(<?php echo $row['display_name'] ?>)</span>
                <?php endif; ?>
              </td>
              <td class="amount">
                  <?php echo $row['amount'] . " " . $row['currency'] ?>
              </td>
                <td>
                    <?php echo $row['api_trx_id'] ?>
                </td>
                <td>
                    <span title="<?php echo addslashes($row['api_error']) ?>">
                        <?php echo (new apiStateEnum())->Dictionary()[$row['api_state']]; ?>
                    </span>
                </td>
                <td>
                    <?php echo $row['passbook_trading_id'] ?>
                </td>
                <td>
                    <?php
                    if ($row['trading_state'] == passbookTradingStateEnum::DONE && $row['is_outstanding']) {
                        echo 'OUTSTANDING';
                    } else {
                        echo (new passbookTradingStateEnum())->Dictionary()[$row['trading_state']];
                    }
                    ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="confirm_trading('<?php echo $row['uid']?>', <?php echo "{". join(",", array_map(function($v) {return "'" . $v['currency'] . "': " .$v['balance'];}, array_filter($row['accounts'], function($v) {return $v['balance'] != "0";}))) . "}"; ?>)">
                            <span><i class="fa fa-check"></i>Confirm</span>
                        </a>
                        <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="cancel_trading('<?php echo $row['uid']?>', <?php echo "{". join(",", array_map(function($v) {return "'" . $v['currency'] . "': " .$v['balance'];}, array_filter($row['accounts'], function($v) {return $v['balance'] != "0";}))) . "}"; ?>)">
                            <span><i class="fa fa-close"></i>Cancel</span>
                        </a>
                    </div>
                </td>

            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
