

<div class="container">
    <table class="table table-bordered table-hover">

        <tr class="table-header">
            <td>
                ID
            </td>
            <td>
                Name
            </td>
            <td>Age</td>
            <td>Update Time</td>
        </tr>

        <?php foreach( $output['list'] as $v ){ ?>
            <tr>
                <td><?php echo $v['uid']; ?></td>
                <td><?php echo $v['name']; ?></td>
                <td><?php echo $v['age']; ?></td>
                <td><?php echo $v['update_time']; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>