
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td><?php echo 'Category Code';?></td>
                            <td><?php echo 'Category Name';?></td>
                            <td><?php echo 'credit';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if($data){ ?>
                            <?php $i = 0 ;foreach($data as $row){ ++$i; ?>
                                <tr>
                                    <td>
                                        <?php echo $i ?>
                                    </td>
                                    <td>
                                        <?php echo $row['category_code'] ?>
                                    </td>

                                    <td>
                                        <?php echo $row['category_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo ncPriceFormat($row['credit']); ?>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan="7">No records</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

