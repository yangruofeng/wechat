<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

     .ibox-title {
         padding-top: 12px!important;
         min-height: 40px;
     }
</style>
<?php
$client_info=$output['member_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Profile</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'clientProfileIndex', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Request</span></a></li>
                <li><a  class="current"><span>Branch</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Branch</h5>
                </div>
                <div class="content">
                    <form id="frm_branch">
                        <input type="hidden" name="uid" value="<?php echo $client_info['uid']?>">
                        <table class="table">
                            <tr>
                                <td><label class="control-label">Branch</label></td>
                                <td>
                                    <select class="form-control" name="branch_id" style="width: 250px">
                                        <?php foreach ($output['branch_list'] as $key => $branch) {?>
                                            <option value="<?php echo $branch['uid']?>" <?php echo $branch['uid'] == $client_info['branch_id'] ? 'selected' : ''?>><?php echo $branch['branch_name']?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                    <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger">
                                        <i class="fa fa-check"></i>
                                        Submit
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        var _values = $("#frm_branch").getValues();
        yo.loadData({
            _c: 'operator',
            _m: 'submitClientProfileBranch',
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('operator', 'clientProfileIndex', array(), false, BACK_OFFICE_SITE_URL);?>';
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

</script>






