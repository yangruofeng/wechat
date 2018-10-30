<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
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
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php require_once template('widget/branch.balance'); ?>
    <div class="row" >
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <ul class="list-inline list-day">
                        <li>
                            <select id="sel_year" onchange="setDaysOfMonth();">
                                <?php for($i=2018;$i<2100;$i++){?>
                                    <option value="<?php echo $i?>" <?php if(date('Y')==$i) echo 'selected'?>><?php echo $i?></option>
                                <?php }?>
                            </select>
                        </li>
                        <li>
                            <select id="sel_month" onchange="setDaysOfMonth();">
                                <?php for($i=1;$i<=12;$i++){?>
                                    <option value="<?php echo $i?>" <?php if(date('m')==$i) echo 'selected'?>><?php echo $i?></option>
                                <?php }?>
                            </select>
                        </li>
                    </ul>
                </div>
                <div class="content" id="div_report_content">

                </div>
            </div>
        </div>
        <div class="col-sm-12" style="text-align:center;margin-top: 30px">
            <a class="btn btn-danger" style="min-width: 80px;margin-left: -40px" onclick="print_ct_daily_report()"><i class="fa fa-check"></i><?php echo 'Print' ?></a>
        </div>
    </div>
</div>

<script>
    function setDaysOfMonth(){
        var _date=new Date('2000-0-1');
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
        var _cur_day=parseInt("<?php echo date('d')?>");
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
           tpl:"cash_in_vault/report.daily.content",
            control:'counter_base',
            dynamic:{
                api:"cash_in_vault",
                method:"getDailyReportByDay",
                param:{
                    year:_year,
                    month:_month,
                    day:_day
                }
            },
            callback:function(_tpl){
                $(document).unmask();
                $("#div_report_content").html(_tpl);
            }
        });
    }


    function print_ct_daily_report() {

        var _year=parseInt($("#sel_year").val());
        var _month=parseInt($("#sel_month").val());
        var _day = parseInt($('.li-day .btn-primary').data('day'));
        if(!_day){
            alert('Please Choose Date');
            return;
        }
//        window.location.href = "<?php //echo getUrl('print_form', 'printCTDailyReport', array(), false, ENTRY_COUNTER_SITE_URL)?>//&year=" + _year+"&month="+_month+"&day="+_day;

        if(window.external){
            window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printCTDailyReport', array(), false, ENTRY_COUNTER_SITE_URL)?>&year=" + _year+"&month="+_month+"&day="+_day);
        }


    }

</script>



