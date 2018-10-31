<style>
    .client-top dl {
        min-height: 172px !important;
    }

    .client-top dl.account-basic {
        width: 30%;
    }

    .client-top dl.credit-basic {
        width: 29%;
    }

    .client-top dl.assets-basic {
        width: 19%;
    }

    .client-top .assets-basic dt {
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .client-top .assets-basic .info-detail {
        margin-left: 25px;
    }

</style>
<div class="client-top clearfix">
    <dl class="account-basic clearfix">
        <dt class="pull-left">
        <p class="account-head">
            <img src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg">
        </p>
        </dt>
        <dd class="pull-left margin-large-left" style="padding: 11px 0 12px">
            <p class="text-small">
                <span class="show pull-left base-name marginright5">CID</span>:
                <span class="marginleft10"><?php echo $item['obj_guid']; ?></span>
            </p>

            <p class="text-small">
                <span class="show pull-left base-name marginright5">Login Account</span>:
                <span class="marginleft10"><?php echo $item['login_code']; ?></span>
            </p>

            <p class="text-small">
                <span class="show pull-left base-name marginright5">Phone</span>:
                <span class="marginleft10"><?php echo $item['phone_id']; ?></span>
            </p>

            <p class="text-small">
                <span class="show pull-left base-name marginright5">Status</span>:
                <span class="marginleft10"><?php echo $lang['client_member_state_' . $item['member_state']]; ?></span>
            </p>

            <p class="text-small">
                <span class="show pull-left base-name marginright5">Member Grade</span>:
                <span class="marginleft10"><?php echo $item['grade_code'] ?: ''; ?></span>
            </p>
        </dd>
    </dl>

    <dl class="credit-basic clearfix">
        <dt class="pull-left">
            <div class="fact-data fact-data-1">
                <input type="hidden" name="credit" id="credit" value="<?php echo $credit_info['credit'] ? : 0; ?>">
                <input type="hidden" name="balance" id="balance" value="<?php echo $credit_info['balance'] ? : 0; ?>">
                <div class="epie-chart easyPieChart" data-percent="45" style="width: 120px; height: 120px;">
                    <div class="credit-lan" style="margin-top: 40px">
                        <p class="base-name">Credit Balance</p>
                        <p class="balance"><?php echo $credit_info['balance']; ?></p>
                    </div>
                    <canvas id="myCanvas" width="130" height="130"></canvas>
                </div>
            </div>
        </dt>
        <dd class="pull-left margin-large-left">
            <h5 class="text-small" style="margin-top: 25px;">
                <span class="show pull-left base-name marginright5">Credit Limit</span>:
                <span class="marginleft10"><?php echo ncPriceFormat($credit_info['credit']); ?></span>
            </h5>
            <h5 class="text-small" style="margin-top: 15px">
                <span class="show pull-left base-name marginright5">Credit Balance</span>:
                <span class="marginleft10"><?php echo ncPriceFormat($credit_info['balance']); ?></span>
            </h5>
            <h5 class="text-small" style="margin-top: 15px">
                <span class="show pull-left base-name marginright5">Expire Time</span>:
                <span class="marginleft10"><?php echo dateFormat($item['expire_time']); ?></span>
            </h5>
        </dd>
    </dl>

    <dl class="assets-basic clearfix" style="padding: 10px 20px">
        <dt>Loan Principal</dt>
        <?php $loan_principal = $item['loan_principal'];?>
        <dd>
            <div class="info-detail">
                <?php $currency = (new currencyEnum())->Dictionary();?>
                <?php foreach ($currency as $k => $v) {?>
                    <div class="item">
<!--                        <a href="#">-->
                            <span class="p"><?php echo $k;?>:
                                <span style="font-weight: 600"><?php echo ncPriceFormat($loan_principal[$k]);?></span>
                            </span>
<!--                        </a>-->
                    </div>
                <?php }?>
            </div>
        </dd>
        <dt>Outstanding Principal</dt>
        <?php $outstanding_principal = $item['outstanding_principal'];?>
        <dd>
            <div class="info-detail">
                <?php foreach ($currency as $k => $v) {?>
                    <div class="item">
<!--                        <a href="#">-->
                            <span class="p"><?php echo $k;?>:
                                <span style="font-weight: 600"><?php echo ncPriceFormat($outstanding_principal[$k]);?></span>
                            </span>
<!--                        </a>-->
                    </div>
                <?php }?>
            </div>
        </dd>
    </dl>

    <dl class="assets-basic clearfix" style="padding: 10px 20px">
        <dt>Savings</dt>
        <dd>
            <div class="info-detail">
                <?php $currency = (new currencyEnum())->Dictionary(); $savings_balance = $output['savings_balance']?:$data['savings_balance']?>
                <?php foreach ($savings_balance as $k => $v) {?>
                    <?php if($currency[$k]){?>
                        <div class="item item-<?php echo strtolower($k);?>">
                            <a href="<?php echo getUrl('client', 'clientSavingsBalanceFlow', array('uid'=>$item['uid'],'currency'=>$k), false, BACK_OFFICE_SITE_URL) ?>">
                                <span class="p"><?php echo $k;?>:
                                    <span style="font-weight: 600"><?php echo ncPriceFormat($v);?></span>
                                </span>
                            </a>
                        </div>
                    <?php }?>
                <?php }?>
            </div>
        </dd>
    </dl>
</div>
<script>
    $(function(){
        var credit = $('#credit').val(), balance = $('#balance').val();
        var ring = parseFloat(balance) / parseFloat(credit) * 100;
        drawRing(130, 130, ring);
        function drawRing(w, h, val) {
            //先创建一个canvas画布对象，设置宽高
            var c = document.getElementById('myCanvas'), ctx = c.getContext('2d'), lineWidth = 8;
            ctx.canvas.width = w;
            ctx.canvas.height = h;
            //圆环有两部分组成，底部灰色完整的环，根据百分比变化的环
            //先绘制底部完整的环
            //起始一条路径
            ctx.beginPath();
            //设置当前线条的宽度
            ctx.lineWidth = lineWidth;
            //设置笔触的颜色
            ctx.strokeStyle = '#f1f1f1';
            //arc()方法创建弧/曲线（用于创建圆或部分圆）arc(圆心x,圆心y,半径,开始角度,结束角度)
            ctx.arc(65, 65, 57, 0, 2 * Math.PI);
            //绘制已定义的路径
            ctx.stroke();

            //绘制根据百分比变动的环
            ctx.beginPath();
            ctx.lineWidth = lineWidth;
            ctx.strokeStyle = '#E84F34';
            //设置开始处为0点钟方向（-90*Math.PI/180）
            //x为百分比值（0-100）
            ctx.arc(65, 65, 57, -90 * Math.PI / 180, (val * 3.6 - 90) * Math.PI / 180);
            ctx.stroke();
        }
    })
</script>
