<?php
$level = $output['credit_level'];
$cert_lang = $output['cert_verify_lang'];
?>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js"></script>
<style>
    a:hover{
        cursor: pointer;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit Level</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('setting', 'addCreditLevel', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">

        <div class="business-content">
            <div class="business-list">
                <div>
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td>Type</td>
                            <td><?php echo 'Amount';?></td>
                            <td><?php echo 'Disburse Time';?></td>
                            <td>Certification List</td>
                            <td>Function</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach($level as $key=>$row){?>
                            <tr>
                                <td>
                                    <?php echo $key+1; ?><br/>
                                </td>
                                <td>
                                    <?php echo $output['level_type_lang'][$row['level_type']]; ?>
                                </td>
                                <td>
                                    <?php echo $row['min_amount'].' - '.$row['max_amount']; ?>
                                </td>
                                <td>
                                    <?php echo $row['disburse_time'].' ';
                                        switch ( $row['disburse_time_unit'] ){
                                            case 1:
                                                echo 'Minutes';
                                                break;
                                            case 2:
                                                echo 'Hours';
                                                break;
                                            case 3:
                                                echo 'Days';
                                                break;
                                            default:
                                                echo 'Minutes';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?php foreach( $row['cert_list'] as $type ){  ?>
                                        <?php echo $cert_lang[$type]; ?>
                                        <br />
                                    <?php } ?>
                                </td>
                                <td>
                                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('setting', 'editCreditLevel', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>"  style="margin-right: 5px" >
                                        <i class="fa fa-edit"></i>
                                        Edit
                                    </a>
                                    <a title="<?php echo $lang['common_delete'];?>" onclick="deleteLevel(<?php echo $row['uid'];  ?>);" >
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function deleteLevel( id )
    {
        yo.confirm('','Are you sure to delete？', function(_r){
            if(!_r) return false;
            var values = {};
            values.id = id;
            yo.loadData({
                _c:'setting',
                _m:'deleteCreditLevel',
                param: values,
                callback: function(data){
                    if( data.STS ){
                        alert('Deleted success', 1, function(){
                            window.location.reload();
                        });

                    }else{
                        alert(data.MSG,2);
                    }

                }

            });

        });
    }

</script>




