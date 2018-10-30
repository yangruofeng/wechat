<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>

    .container{
        width: 700px!important;
    }

    .text{
        margin-left: -40px!important;
    }

    .magin_bottom{
        margin-bottom: 20px!important;
        background-color: white;!important;
    }
    .ibox-title{
        padding-top: 10px;
        height: 40px!important;
        min-height: 0!important;
    }


</style>
<?php $data = $output['data']?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Complaint And  Advice</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'addComplaintAdvice', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
                <li><a href="<?php echo getUrl('operator', 'complaintAdvice', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="collection-div">
        <div class="basic-info container">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-id-card-o"></i> Type : <?php echo ucwords($data['type'])?></h5>
            </div>
            <div class="content">
                <div class="form-group magin_bottom">
                    <label class="col-sm-3 control-label"><?php echo 'Client Name';?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control text" placeholder="" value="<?php echo $data['contact_name']?>"  disabled>
                        <div class="error_msg"></div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-group magin_bottom">
                    <label class="col-sm-3 control-label"><?php echo 'Client Phone';?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control text" placeholder="" value="<?php echo $data['contact_phone']?>"  disabled>
                        <div class="error_msg"></div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-group magin_bottom">
                    <label class="col-sm-3 control-label"><?php echo 'Title';?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control text" name="title" placeholder="" value="<?php echo $data['title']?>"  disabled>
                        <div class="error_msg"></div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-group magin_bottom">
                    <label class="col-sm-3 control-label"><?php echo 'Content' ?></label>
                    <div class="col-sm-9">
                        <div style="width: 471px;height:200px; border: 1px solid lightgrey;margin-left: -40px!important;padding: 5px">
                            <?php echo $data['content']?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-sm-10 form-group" style="text-align: center;margin-top: 20px">
            <button type="button" class="btn btn-danger" style="margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
        </div>
    </div>
</div>