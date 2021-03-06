
<?php $list = $data['data']; ?>
<div>

    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Client Name';?></td>
            <td><?php echo 'Client Id';?></td>
            <td><?php echo 'Client Phone';?></td>
            <td><?php echo 'Ace Account';?></td>
            <td><?php echo 'Bind Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach( $list as $row ){ ?>
            <tr data-ace-id="<?php echo $row['uid']; ?>" >
                <td>
                    <?php echo $row['display_name']?:$row['login_code']; ?>
                </td>
                <td>
                    <?php echo $row['member_guid']; ?>
                </td>
                <td>
                    <?php echo $row['handler_phone']; ?>
                </td>

                <td>
                    <?php echo $row['handler_account'] ?>
                </td>

                <td>
                    <?php echo timeFormat($row['create_time']); ?>
                </td>

                <td class="function">
                    <button class="btn btn-info" style="padding: 5px 10px" onclick="aceAccountUnbind(<?php echo $row['uid']?>)">
                        <i class="fa fa-unlock"></i>
                        Unbind
                    </button>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

<script>

</script>
