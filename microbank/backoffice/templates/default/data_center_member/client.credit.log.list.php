<style>
    td{
        height: 30px!important;
    }
</style>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Event Type';?></td>
            <td><?php echo 'Authorized Contract Id';?></td>
            <td><?php echo 'Begin Credit';?></td>
            <td><?php echo 'Increase';?></td>
            <td><?php echo 'Reduce';?></td>
            <td><?php echo 'End Credit';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Create Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php foreach($data['data'] as $row){ ?>
                <tr>
                    <td>
                        <?php echo ucwords(str_replace("_"," ",$row['event_type']))?>
                    </td>
                    <td>
                        <?php echo $row['authorized_contract_id'] ?>
                    </td>
                    <td>
                        <?php echo $row['begin_credit'] ?>
                    </td>
                    <td>
                        <?php  echo $row['flag'] == 1? $row['amount'] : '' ?>
                    </td>
                    <td <?php  echo $row['flag'] == -1? $row['amount'] : '' ?>
                    </td>
                    <td>
                        <?php echo $row['after_credit']; ?>
                    </td>
                    <td>
                        <?php echo $row['remark']; ?>
                    </td>
                    <td>
                        <?php echo $row['create_time']; ?>
                    </td>
                </tr>
            <?php }?>
            <?php }else{ ?>
                 <tr>
                     <td colspan="7">
                         <?php include(template(":widget/no_record")); ?>
                     </td>
                 </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
