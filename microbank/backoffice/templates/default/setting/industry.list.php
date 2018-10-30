<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Industry Name';?></td>
            <td><?php echo 'Industry Code';?></td>
            <td><?php echo 'Survey Info';?></td>
            <td><?php echo 'Credit Rate'?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Creator';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['industry_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['industry_code'] ?><br/>
                </td>
                <td>
                    <?php
                    $industry_arr = my_json_decode($row['industry_json']);
                    $arr_values=array_values($industry_arr);
                    $str_values=join("/",$arr_values);
                    echo $str_values;
                    /*
                    $industry_arr_new = array();
                    foreach ($industry_arr as $key => $val) {
                        $industry_arr_new[] = $val . '(' . $key . ')';
                    }
                    echo implode(' /', $industry_arr_new);
                    */
                    ?><br/>
                </td>
                <td>
                    <?php echo $row['credit_rate']?$row['credit_rate'].'%':''; ?><br/>
                </td>
                <td>
                    <?php echo $row['state'] ? 'Valid' : 'Invalid' ?><br/>
                </td>
                <td>
                    <?php echo $row['creator_name'] ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']); ?><br/>
                </td>
                <td>
                    <a href="<?php echo getUrl('setting', 'editIndustry', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a href="<?php echo getUrl('setting', 'deleteIndustry', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                        <i class="fa fa-trash"></i>
                        Delete
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

