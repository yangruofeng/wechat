
<?php $keys = $output['keys']; ?>
<?php $list = $output['list']; ?>
<div class="page">
    <div class="row">

        <table class="table table-bordered">
            <thead>
                <tr style="background-color: #ddd;">
                    <?php foreach( $keys as $v){ ?>
                        <th><?php echo $v; ?></th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach( $list as $item ){ ?>
                    <tr>
                        <?php foreach( $item as $value ){ ?>
                            <td><?php echo $value; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>

        </table>

    </div>
</div>