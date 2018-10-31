<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch : <?php echo $output['bank'][0]['branch_name']?></h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current" ><span>bank</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <div>
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td><?php echo 'Bank code';?></td>
                            <td><?php echo 'Bank Name';?></td>
                            <td><?php echo 'Currency';?></td>
                            <td><?php echo 'Bank Phone';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php $i = 0;foreach($output['bank'] as $key => $row){ $i++?>
                           <?php if($row['bank_code']){ ?>
                               <tr>
                                <td>
                                    <?php echo $i; ?><br/>
                               </td>
                               <td>
                                   <?php echo $row['bank_code'];?>
                               </td>
                               <td>
                                   <?php echo $row['bank_name'];?>
                               </td>
                               <td>
                                   <?php echo $row['currency'];?>
                               </td>
                               <td>
                                   <?php echo $row['bank_account_phone'];?>
                               </td>
                               </tr>

                            <?php }else{ ?>
                                <tr>
                                    <td> <?php echo 'No Matching Bank';?></td>
                                </tr>
                            <?php }?>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>