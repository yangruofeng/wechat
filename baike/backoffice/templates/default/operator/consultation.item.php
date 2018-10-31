<style>
    .btn {
        border-radius: 0;
        padding: 5px 10px;
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
$client_info=$output['client_info'];
$state_list=(new loanConsultStateEnum())->Dictionary();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Profile</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'consultation', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Consultation List</span></a></li>
                <li><a  class="current"><span>Consultation Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php $row=$output['consultation']?>
    <div class="basic-info container" style="margin-top: 70px;max-width: 800px">
        <?php if(count($client_info)){?>
            <div class="business-condition">
                <?php require_once template("widget/item.member.summary")?>
            </div>
        <?php }?>
        <div class="business-content">
            <div>
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Consultation</h5>
                </div>
                <div class="content">
                    <table class="table table-bordered">
                        <tbody class="table-body">
                         <tr>
                             <td>Applicant Name</td>
                             <td><?php echo $row['applicant_name']?></td>
                             <td>Contact Phone</td>
                             <td><?php echo $row['contact_phone']?></td>
                         </tr>
                         <tr>
                             <td>Apply Amount</td>
                             <td><?php echo $row['apply_amount']." ".$row['currency']?></td>
                             <td>Apply Terms</td>
                             <td><?php echo $row['loan_time']." ".$row['loan_time_unit']?></td>
                         </tr>
                         <tr>
                             <td>Purpose</td>
                             <td><?php echo $row['loan_purpose']?></td>
                             <td>Mortgage</td>
                             <td><?php echo $row['mortgage']?></td>
                         </tr>
                         <tr>
                             <td colspan="10">Operator Remark:<code><?php echo $row['operator_remark']?></code></td>
                         </tr>
                         <tr>
                             <td colspan="10">
                                 <?php echo $row['memo']?>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                 State
                             </td>
                             <td>
                                 <?php echo $state_list[$row['state']]?>
                             </td>
                             <td colspan="2">
                                <?php if($row['state'] == loanConsultStateEnum::LOCKED || $row['state'] == loanConsultStateEnum::OPERATOR_APPROVED || $row['state'] == loanConsultStateEnum::OPERATOR_REJECT){?>
                                    <button class="btn btn-default" onclick="showChangeStateModal();">Change State</button>
                                <?php }?>
                             </td> 
                         </tr>
                         <tr>
                             <td>Branch</td>
                             <td><?php echo $row['branch_name']?></td>
                             <td colspan="2"><button class="btn btn-default" onclick="showSelectBranchModal();">Change Branch</button></td>
                         </tr>
                         <tr>
                             <td colspan="10">BranchManager Remark:<code><?php echo $row['bm_name']?:'Pending Allot'?></code></td>
                         </tr>
                         <tr>
                             <td colspan="10">
                                 <?php echo $row['bm_remark']?:'NONE'?>
                             </td>
                         </tr>
                         <tr>
                             <td colspan="10">CO Remark:<code><?php echo $row['co_name']?:'Pending Allot'?></code></td>
                         </tr>
                         <tr>
                             <td colspan="10">
                                 <?php echo $row['co_remark']?:'NONE'?>
                             </td>
                         </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="stateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'State'?></h4>
            </div>
            <div class="modal-body">
                <form class="clearfix" id="state_form" method="POST" action="" style="margin-bottom: 0;">
                    <input type="hidden" name="uid" value="<?php echo $row['uid'];?>">
                    <div class="col-sm-12 form-group">
                        <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>State</label>
                        <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="state" <?php if($row['state'] == loanConsultStateEnum::OPERATOR_APPROVED){echo 'checked';}?> value="<?php echo loanConsultStateEnum::OPERATOR_APPROVED;?>"> <?php echo $state_list[loanConsultStateEnum::OPERATOR_APPROVED];?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="state" <?php if($row['state'] == loanConsultStateEnum::OPERATOR_REJECT){echo 'checked';}?> value="<?php echo loanConsultStateEnum::OPERATOR_REJECT;?>"> <?php echo $state_list[loanConsultStateEnum::OPERATOR_REJECT];?>
                        </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="btn_close_modal();"><i class="fa fa-reply"></i>Back</button>
                <button type="button" onclick="btnSubmitState()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Branch List'?></h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="business-condition">
                    <form class="form-inline" id="frm_search_condition">
                        <table class="search-table">
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for branch" style="min-width: 150px">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                                <i class="fa fa-search"></i>
                                                <?php echo 'Search'; ?>
                                            </button>
                                         </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="modal-table">
                    <div>
                        <table class="table table-bordered">
                            <thead>
                            <tr class="table-header" style="background-color: #EEE">
                                <td>CID</td>
                                <td>Branch Name</td>
                                <td>Address</td>
                                <td>Function</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_close_modal(){
        $('#stateModal').modal('hide');
    }

    function showChangeStateModal() {
        $('#stateModal').modal('show');
    }

    function btnSubmitState(){
        $('#stateModal').modal('hide');
        var values = $('#state_form').getValues();
        yo.loadData({
            _c: 'operator',
            _m: 'submitConsultApplicantState',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function showSelectBranchModal() {
        btn_search_onclick();
        $('#branchModal').modal('show');
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();
        yo.dynamicTpl({
            tpl: "operator/branch.list",
            dynamic: {
                api: "operator",
                method: "getBranchList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $("#branchModal .modal-table").html(_tpl);
            }
        });
    }

    function selectBranch(branch_id, branch_name) {
        $('#branchModal').modal('hide');
        yo.loadData({
            _c: 'operator',
            _m: 'submitConsultApplicantBranch',
            param: {uid: '<?php echo $output['consultation']['uid'];?>', branch_id: branch_id},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });

        
    }
</script>






