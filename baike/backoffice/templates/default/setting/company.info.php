<style>
    .table tr td {
        vertical-align: middle !important;
    }

    .table-bordered tr {
        background-color: #FFF !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Company Info</h3>
            <ul class="tab-base">
                <li><a class="current"><span>View</span></a></li>
                <li><a href="<?php echo getUrl('setting', 'editCompanyInfo', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <table class="table table-bordered">
            <tr>
                <td><?php echo 'Company Name';?></td>
                <td style="font-weight: bold"><?php echo $output['company_config']['company_name']?></td>
                <td><?php echo 'Hotline';?></td>
                <td style="font-weight: bold"><?php echo implode('/ ', $output['company_config']['hotline'])?></td>
                <td rowspan="2"><?php echo 'Icon';?></td>
                <td rowspan="2">
                    <img style="max-width: 100px;max-height: 100px;text-align: center" src="<?php echo getImageUrl($output['company_config']['company_icon'], imageThumbVersion::MAX_120,'company');?>">
                </td>
            </tr>
            <tr>
                <td><?php echo 'Email';?></td>
                <td style="font-weight: bold"><?php echo $output['company_config']['email']?></td>
                <td><?php echo 'Location';?></td>
                <td style="font-weight: bold">
                    <?php echo $output['company_config']['address_region'] . $output['company_config']['address_detail'] ?>
                </td>
            </tr>
            <tr>
                <td><?php echo 'Introduction';?></td>
                <td colspan="5">
                    <?php echo $output['company_config']['description']?>
                </td>
            </tr>
        </table>
    <div>
</div>