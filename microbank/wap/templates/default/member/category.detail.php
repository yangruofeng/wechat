<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $output['html_title'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, minimum-scale=0.1, user-scalable=yes" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui.2.0.css?v=2">
    <link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/init.css?v=23">
    <link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=7">
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL ?>/editormd/css/editormd.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/marked.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/prettify.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/raphael.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/underscore.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/sequence-diagram.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/flowchart.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/lib/jquery.flowchart.min.js' ?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/editormd/editormd.js?v=1' ?>"></script>

    <script type="text/javascript">
        var CURRENT_LANGUAGE_CODE = "<?php echo Language::currentCode(); ?>";
    </script>
    <style>
        .category-detail {
            margin-bottom: 20px;
        }
        .category-detail .icon img {
            width: 1.3rem;
        }
        .category-detail li {
            display: block!important;
            margin-top: 10px!important;
            font-size: .7rem;
        }
        .category-detail li .top {
            display: flex;
        }
        .category-description {
            display: flex;
            background-color: #fff;
        }
        .category-description .editormd-html-preview {
            padding: 10px 0;
        }
        .category-description .icon {
            padding-left: 10px;
        }
        .icon-description {
            background: red;
            padding: 10px;
            border-radius: 10px;
            line-height: 0;
        }
        .icon-description img {
            width: 100%;
        }
        .rate-detail {
            font-size: 11px;
            background-color: #fff;
            margin-top: 10px;
            text-align: center;
            overflow-x: auto;
        }

        .table{
            width: 100%;
        }
        .table tr td {
            padding: 0 8px;
        }
        .table tbody, .table tbody p {
            color: #651A5C;
            font-weight: 500;
        }
        .table thead td span {
            white-space:nowrap;
        }
        .table p {
            font-size: 12px;
        }
        .table,.table td,table th{
            border:1px solid #e8e8e8;
            border-collapse:collapse;
        }
        .p10{
            padding: 10px;
        }
    </style>
</head>
<body>
<?php
$interest_type_lang = enum_langClass::getLoanInstallmentTypeLang();
include_once(template('widget/inc_header'));
?>
<div class="wrap">
    <?php
    $data = $output['data'];
    $product_info = $data['product_info'];
    $product_description = urldecode($product_info['product_description']);
    $product_qualification = urldecode($product_info['product_qualification']);
    $product_feature = urldecode($product_info['product_feature']);
    $product_required = urldecode($product_info['product_required']);
    $product_notice = urldecode($product_info['product_notice']);
    $rate_list = $data['rate_list'];
    ?>
    <div class="category-description aui-list">
        <div class="aui-list-item-label-icon icon">
            <div class="icon-description">
                <img src="<?php echo WAP_SITE_URL;?>/resource/image/product1.png" alt="" class="icon-item">
            </div>
        </div>
        <div class="aui-list-item-inner content">
            <?php if($product_description){?>
                <!--转html-->
                <div class="markdown_content" id="markdown_description">
                    <textarea style="display: none;" placeholder=""><?php echo $product_description;?></textarea>
                </div>
            <?php }else{?> <?php }?>
        </div>
    </div>
    <div class="rate-detail">
        <table class="table table-bordered">
            <thead>
            <tr>
                <td rowspan="2"><span>Loan Size</span></td>
                <td rowspan="2"><span>Loan Fee</span></td>
                <td rowspan="2"><span>Admin Fee</span></td>
                <?php if( $product_info['special_key'] != specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){ ?>
                    <td rowspan="2"><span>Loan Term Time</span></td>
                    <td colspan="3"><span>Interest Rate</span></td>
                    <td colspan="3"><span>Operate Rate</span></td>
                <?php } ?>

                <?php if( $product_info['special_key'] == specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){ ?>
                    <td rowspan="2">
                        Service Fee
                    </td>
                <?php } ?>

                <td rowspan="2"><span>Repayment Type</span></td>
            </tr>
            <tr>
                <?php if( $product_info['special_key'] != specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){ ?>
                    <td><span>No mortgage</span></td>
                    <td><span>MortgageSoft</span></td>
                    <td><span>MortgageHard</span></td>
                    <td><span>No mortgage</span></td>
                    <td><span>MortgageSoft</span></td>
                    <td><span>MortgageHard</span></td>
                <?php } ?>



            </tr>
            </thead>
            <tbody>
            <?php foreach($rate_list as $v){?>
                <tr>
                    <td>
                        <?php echo ncPriceFormat($v['loan_size_min'])?>~<?php echo ncPriceFormat($v['loan_size_max'])?>
                        <p><?php echo $v['currency'];?></p>
                    </td>
                    <td>
                        <?php echo $v['loan_fee'].($v['loan_fee_type']==1?'':'%');?>
                    </td>
                    <td>
                        <?php echo $v['admin_fee'].($v['admin_fee_type']==1?'':'%');?>
                    </td>

                    <?php if( $product_info['special_key'] != specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){ ?>
                        <td>
                            <?php echo $v['loan_term_time'];?>
                        </td>
                        <td>
                            <?php echo $v['interest_rate'];?>%
                            <p>(<?php echo $v['interest_rate_unit'];?>)</p>
                        </td>
                        <td>
                            <?php echo $v['interest_rate_mortgage1'];?>%
                            <p>(<?php echo $v['interest_rate_unit'];?>)</p>
                        </td>
                        <td>
                            <?php echo $v['interest_rate_mortgage2'];?>%
                            <p>(<?php echo $v['interest_rate_unit'];?>)</p>
                        </td>
                        <td>
                            <?php echo $v['operation_fee'];?>%
                            <p>(<?php echo $v['operation_fee_unit'];?>)</p>
                        </td>
                        <td>
                            <?php echo $v['operation_fee_mortgage1'];?>%
                            <p>(<?php echo $v['operation_fee_unit'];?>)</p>
                        </td>
                        <td>
                            <?php echo $v['operation_fee_mortgage2'];?>%
                            <p>(<?php echo $v['operation_fee_unit'];?>)</p>
                        </td>
                    <?php } ?>

                    <?php if( $product_info['special_key'] == specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){ ?>
                        <td >
                            <?php echo $v['service_fee'].($v['service_fee_type']==1?'':'%'); ?>
                        </td>
                    <?php } ?>


                    <td>
                        <?php echo $interest_type_lang[$v['repayment_type']]?:$v['repayment_type'];?>
                        <?php if( interestTypeClass::isPeriodicRepayment($v['repayment_type']) ){ ?>
                            <p>(<?php echo $v['repayment_period'];?>)</p>
                        <?php } ?>

                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
    <div class="aui-content aui-margin-b-10">
        <ul class="aui-list category-detail aui-margin-b-10">
            <li class="aui-list-item">
                <div class="top title">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_client_qualification.png" alt="" class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content">
                        <?php echo $lang['label_product_qualification'];?>
                        <i class="aui-iconfont aui-icon-down"></i>
                    </div>
                </div>
                <?php if($product_qualification){?>
                    <!--转html-->
                    <div class="markdown_content" id="markdown_qualification" style="display: none;">
                        <div class="p10">
                            <?php echo $product_qualification;?>
                        </div>
                    </div>
                <?php }else{?><?php }?>
            </li>
            <li class="aui-list-item">
                <div class="top title">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_client_feature.png" alt="" class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content">
                        <?php echo $lang['label_product_feature'];?>
                        <i class="aui-iconfont aui-icon-down"></i>
                    </div>
                </div>
                <?php if($product_feature){?>
                    <!--转html-->
                    <div class="markdown_content" id="markdown_feature" style="display: none;">
                        <div class="p10">
                            <?php echo $product_feature;?>
                        </div>
                    </div>
                <?php }else{?><?php }?>
            </li>
            <li class="aui-list-item">
                <div class="top title">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_document_required.png" alt="" class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content">
                        <?php echo $lang['label_product_required'];?>
                        <i class="aui-iconfont aui-icon-down"></i>
                    </div>
                </div>
                <?php if($product_required){?>
                    <!--转html-->
                    <div class="markdown_content" id="markdown_required" style="display: none;">
                        <div class="p10">
                            <?php echo $product_required;?>
                        </div>
                    </div>
                <?php }else{?><?php }?>
            </li>
            <li class="aui-list-item">
                <div class="top title">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_notice.png" alt="" class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content">
                        <?php echo $lang['label_product_notice'];?>
                        <i class="aui-iconfont aui-icon-down"></i>
                    </div>
                </div>
                <?php if($product_notice){?>
                    <!--转html-->
                    <div class="markdown_content" id="markdown_notice" style="display: none;">
                        <div class="p10">
                            <?php echo $product_notice;?>
                        </div>
                    </div>
                <?php }else{?><?php }?>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    var type = '<?php echo $_GET['source']?>', lang = '<?php echo $_GET['lang']?>';
    if (type == 'app') {
        app_show(type);
    }
    if(lang && CURRENT_LANGUAGE_CODE != lang){
        changeLang(lang, 2);
    }
    function app_show(type) {
        if (type == 'app') {
            $('#header').hide();
        } else {
            $('#header').show();
        }
    }


    /*editormd.markdownToHTML("markdown_description", {
        htmlDecode      : "style,script,iframe",
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : false  // 默认不解析
    });
    editormd.markdownToHTML("markdown_qualification", {
        htmlDecode      : "style,script,iframe",
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : false  // 默认不解析
    });
    editormd.markdownToHTML("markdown_feature", {
        htmlDecode      : "style,script,iframe",
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : false  // 默认不解析
    });
    editormd.markdownToHTML("markdown_required", {
        htmlDecode      : "style,script,iframe",
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : false  // 默认不解析
    });
    editormd.markdownToHTML("markdown_notice", {
        htmlDecode      : "style,script,iframe",
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : false  // 默认不解析
    });*/

    $('.title').click(function(){
        var content = $(this).parent('li').find('.markdown_content'), i = $(this).parent('li').find('i');
        if(content.css('display') == 'none'){
            i.removeClass('aui-icon-down').addClass('aui-icon-top');
            content.show();
        }else{
            i.removeClass('aui-icon-top').addClass('aui-icon-down');
            content.hide();
        }
    });
</script>
</body>

</html>








