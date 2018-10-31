<style>
    #information .item {
        padding: 0 0 7px !important;
    }

    #information .item span {
        float: unset !important;
        margin-left: 0px !important;
    }

     #map-canvas {
         width: 970px;
         height: 500px;
         margin: 0px;
         padding: 0px
     }

</style>
<div role="tabpanel" class="tab-pane active" id="information">
    <table class="table">
        <tbody class="table-body">
        <tr>
            <td>
                <label class="col-sm-3 control-label">WorkType Staff</label>
                <div class="col-sm-9 content">
                    <div style="height: 20px;margin-bottom: 10px">
                        <span class="col-first">Work Type: </span>
                        <span class="col-second"><?php echo $work_type_lang[$item['work_type']];?></span>
                    </div>
                    <div style="height: 20px">
                        <span class="col-first">Own Business: </span>
                            <span class="col-second">
                                <?php
                                if($item['is_with_business'] && count($item['member_industry']) > 0){
                                    $str = '';$i = 0;
                                    foreach ($item['member_industry'] as $v) {
                                        $i < (count($item['member_industry']) - 1) ? $str .= $v['industry_name'].', ' : $str .= $v['industry_name'];
                                        $i++;
                                    }
                                }
                                ?>
                                <?php echo $item['is_with_business'] ? $str : 'None'; ?>
                            </span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label class="col-sm-3 control-label">Residence</label>
                <div class="col-sm-9 content">
                    <?php echo $item['residence'] ? $item['residence']['full_text'] : 'None';?>
                    <?php if ($item['residence']['coord_x'] > 0) {
                        $residence_array = array(
                            0 => array('x' => $item['residence']['coord_x'], 'y' => $item['residence']['coord_y']),
                        );
                        $residence_json = my_json_encode($residence_array)
                        ?>
                        <a href="javascript:void(0)" onclick="showGoogleMap()" style="margin-left: 10px;font-style: italic">Google Map</a>
                    <?php } ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label class="col-sm-3 control-label">Branch</label>
                <div class="col-sm-9 content">
                    <?php echo $item['branch_name'] ? : 'None';?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label class="col-sm-3 control-label">Operator</label>
                <div class="col-sm-9 content">
                    <?php echo $item['operator']['officer_name']?:'None';?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label class="col-sm-3 control-label">CO-List</label>
                <div class="col-sm-9 content">
                    <?php if($item['member_co_list']){ ?>
                        <?php foreach ($item['member_co_list'] as $v) { ?>
                            <div class="item">
                                <span class="col-first"><?php echo $v['officer_name'];?></span>
                                <span class="col-second"><?php echo $v['mobile_phone'];?></span>
                            </div>
                        <?php  } ?>
                    <?php }else{?>
                        No Record
                    <?php }?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label class="col-sm-3 control-label">Product</label>
                <div class="col-sm-9 content">
                    <?php if($item['allow_product']){ ?>
                        <?php foreach ($item['allow_product'] as $v) { ?>
                            <div class="item">
                                <span class="col-first"><?php echo $v['sub_product_name'];?></span>
                            </div>
                        <?php  } ?>
                    <?php }else{?>
                        No Record
                    <?php }?>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Residence Location'?></h4>
            </div>
            <div class="modal-body">
                <div id="map-canvas">
                    <?php
                        $point=array('x' => $item['residence']['coord_x'], 'y' => $item['residence']['coord_y']);
                        include_once(template("widget/google.map.point"));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function showGoogleMap() {
        $('#myModal').modal('show');
    }
</script>
