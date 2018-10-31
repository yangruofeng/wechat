<?php $list = $output['list']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Fee & Admin Fee</h3>
            <ul class="tab-base">
                <li><a class="current"><span><?php echo $output['category_info']['category_name'];?></span></a></li>
                <li>
                    <a href="<?php echo getUrl('loan', 'editLoanFeeSetting', array('category_id' => $output['category_id']), false, BACK_OFFICE_SITE_URL); ?>">
                        <span>Add</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <ul class="list-inline">
                <li>
                    <a class="btn btn-info"
                       href="<?php echo getBackOfficeUrl("loan", "loanFeeSetting", array("category_id" => 0)) ?>">
                        <?php echo 'Default Setting' ?>
                    </a>
                </li>
                <?php foreach ($output['category_list'] as $cate) {
                    if($cate['uid']==$output['category_id']) continue;
                    ?>
                    <li>
                        <a class="btn btn-info"
                           href="<?php echo getBackOfficeUrl("loan", "loanFeeSetting", array("category_id" => $cate['uid'])) ?>">
                            <?php echo $cate['category_name'] ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Currency</th>
                    <th>Min Amount</th>
                    <th>Max Amount</th>
                    <th>Admin Fee</th>
                    <th>Loan Fee</th>
                    <th>Annual Fee</th>
                    <th>Function</th>

                </tr>
                </thead>
                <tbody>
                <?php if (!empty($list)) {
                    foreach ($list as $item) { ?>
                        <tr>
                            <td><?php echo $item['currency']; ?></td>
                            <td><?php echo ncPriceFormat($item['min_amount']); ?></td>
                            <td><?php echo ncPriceFormat($item['max_amount']); ?></td>
                            <td>
                                <?php if ($item['admin_fee'] > 0) {
                                    echo $item['admin_fee'] . ($item['admin_fee_type'] == 1 ? '' : '%');
                                    ?>
                                <?php } else { ?>
                                    -
                                <?php } ?>

                            </td>
                            <td>
                                <?php if ($item['loan_fee'] > 0) {
                                    echo $item['loan_fee'] . ($item['loan_fee_type'] == 1 ? '' : '%');

                                    ?>
                                <?php } else { ?>
                                    -
                                <?php } ?>

                            </td>
                            <td>
                                <?php if ($item['annual_fee'] > 0) {
                                    echo $item['annual_fee'] . ($item['annual_fee_type'] == 1 ? '' : '%');

                                    ?>
                                <?php } else { ?>
                                    -
                                <?php } ?>

                            </td>

                            <td>
                                <a href="<?php echo getBackOfficeUrl('loan', 'editLoanFeeSetting', array(
                                    'uid' => $item['uid']
                                )); ?>" class="btn btn-primary ">
                                    <i class="fa fa-edit"></i>Edit
                                </a>
                                <button class="btn btn-default" onclick="deleteInfo(<?php echo $item['uid']; ?>)">
                                    <i class="fa fa-close"></i>Delete
                                </button>
                            </td>
                        </tr>

                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="10">Null</td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>
    <script>


        function deleteInfo($id) {

            $.messager.confirm("<?php echo 'Delete'?>", "<?php echo 'Are you sure to delete?'?>", function (_r) {
                if (!_r) return;
                $(document).waiting();
                yo.loadData({
                    _c: "loan",
                    _m: "deleteLoanFeeSetting",
                    param: {uid: $id},
                    callback: function (_o) {
                        $(document).unmask();
                        if (_o.STS) {
                            //alert('Success');
                            window.location.reload();
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            });
        }

    </script>
