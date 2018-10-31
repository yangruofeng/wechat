<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header" style="background-color: #EEE">
            <td>CID</td>
            <td>Branch Name</td>
            <td>Address</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['obj_guid']; ?><br>
                    </td>
                    <td>
                        <?php echo $row['branch_name']; ?><br>
                    </td>
                    <td>
                        <?php echo $row['address_region'] . ' ' . $row['address_detail']?><br>
                    </td>
                    <td>
                        <a href="#" onclick="selectBranch(<?php echo $row['uid']?>, '<?php echo $row['branch_name']?>')">
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