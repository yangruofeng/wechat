<style>
    .btn-day{
        width: 25px;
        padding: 3px;
    }
    .li-day{
        padding: 0!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Daily Report</h3>
            <ul class="tab-base">
                <li><a class="current">
                        <span style="cursor: pointer" onclick="javascript:history.go(-1);"> BACK </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row" >
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <ul class="list-inline list-day">
                        <li>
                            <select id="sel_year" onchange="setDaysOfMonth();">
                                <?php for($i=2018;$i<2100;$i++){?>
                                    <option value="<?php echo $i?>>" <?php if(date('Y')==$i) echo 'selected'?>><?php echo $i?></option>
                                <?php }?>
                            </select>
                        </li>
                        <li>
                            <select id="sel_month" onchange="setDaysOfMonth();">
                                <?php for($i=1;$i<=12;$i++){?>
                                    <option value="<?php echo $i?>>" <?php if(date('m')==$i) echo 'selected'?>><?php echo $i?></option>
                                <?php }?>
                            </select>
                        </li>
                    </ul>
                </div>
                <div class="content" id="div_report_content">

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function setDaysOfMonth(){
        var _date=new Date();
        var _year=parseInt($("#sel_year").val());
        var _month=parseInt($("#sel_month").val());
        _date.setYear(_year);
        _date.setMonth(_month);
        _date.setDate(0);
        var _cnt=_date.getDate();

        $(".list-day").find(".li-day").remove();
        for(var _i=1;_i<=_cnt;_i++){
            var _li=[];
            _li.push('<li class="li-day">');
            _li.push('<button class="btn btn-default btn-day" data-day="'+_i+'" onclick="btn_day_onclick(this)">'+_i+'</button>');
            _li.push('</li>')
            _li=$(_li.join("\n"));
            $(".list-day").append(_li);
        }

    }
    $(document).ready(function () {
        setDaysOfMonth();
        var _cur_day="<?php echo date('d')?>";
        var _btn=$(".list-day").find('.btn[data-day="'+_cur_day+'"]');
        _btn=_btn[0];
        btn_day_onclick(_btn);
    });
    function btn_day_onclick(_e){
        $(".list-day").find(".li-day").find(".btn").removeClass("btn-primary");
        $(_e).addClass("btn-primary");
        $(document).waiting();

        var _year=parseInt($("#sel_year").val());
        var _month=parseInt($("#sel_month").val());
        var _day=$(_e).data("day");
        yo.dynamicTpl({
            tpl:"data_center_branch/report.daily.content",
            dynamic:{
                api:"data_center_branch",
                method:"getDailyReportByDay",
                param:{
                    year:_year,
                    month:_month,
                    day:_day,
                    branch_id:<?php echo $_GET['branch_id'];?>,
                }
            },
            callback:function(_tpl){
                $(document).unmask();
                $("#div_report_content").html(_tpl);
            }
        });
    }



</script>