<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header" style="background-color: #EEE">
            <td>Co Name</td>
            <td>Phone</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['user_name']; ?><br>
                    </td>
                    <td>
                        <?php echo $row['mobile_phone']?><br>
                    </td>
                    <td>
                        <a href="#" onclick="selectCo(<?php echo $row['uid']?>, '<?php echo $row['user_name']?>')">
                            <i class="fa fa-check"></i>Select
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">
                   Null<br>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>