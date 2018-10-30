<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .lv0 {
        font-weight: bold;
    }

    .lv1 label {
        padding-left: 10px;
        font-weight: normal;
    }

    .lv2 label {
        padding-left: 20px;
        font-weight: normal;
    }

    .lv3 label {
        padding-left: 30px;
        font-weight: normal;
    }

    .amount {
        text-align: right;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Income Statement</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    
    <div class="container">
    Coming soon
        <!--<table style="display: none;">
            <?php
            function render($arr, $lv = 0) {
                foreach ($arr as $k => $item) {
                ?>
                <tr class="lv<?php echo $lv ?>">
                    <td>
                        <label>
                            <?php echo $k;  ?>
                        </label>
                    </td>
                    <td class="amount c-usd"><em><?php echo ncPriceFormat($item['amount']['USD']) ?></em></td>
                    <td class="amount c-khr"><em><?php echo ncPriceFormat($item['amount']['KHR']) ?></em></td>
                </tr>
            <?php
                    if ($item['children']) render($item['children'], $lv+1);
                }
            }
            ?>
        </table>
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="content">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="c-usd">USD</th>
                            <th class="c-khr">KHR</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php render($output['data']) ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       -->
    </div>
</div>