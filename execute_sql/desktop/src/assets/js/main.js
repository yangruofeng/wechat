!function(){
    window.sound = new Sound();
/**
 * 采集、验证用户设置
 */
function gatherUserSetting(){
    var setting = {ok:true};
    
    var grid = parseInt($(':radio[name=gameType]:checked').val()) || 9;
    setting.grid = grid;

    var ba = $('#betAmount'),rpc = $("#redPacketCount");
    var betAmount = parseInt(ba.val());
    var redPacketCount = parseInt(rpc.val());

    if(isNaN(betAmount) || betAmount< 100 || betAmount > 10000){
        setting.ok = false;
        ba.parent().removeClass('has-success');
        ba.parent().addClass('has-error');
        ba.parent().find('.help-block').removeClass('hide');
    }else{
        setting.betAmount = betAmount;
        ba.parent().removeClass('has-error');
        ba.parent().addClass('has-success');
        ba.parent().find('.help-block').addClass('hide');
    }

    if(isNaN(redPacketCount) || redPacketCount < 1 || redPacketCount >= grid){
        setting.ok = false;
        rpc.parent().removeClass('has-success');
        rpc.parent().addClass('has-error');
        rpc.parent().find('.help-block').removeClass('hide');
    }else{
        setting.redPacketCount = redPacketCount;
        rpc.parent().removeClass('has-error');
        rpc.parent().addClass('has-success');
        rpc.parent().find('.help-block').addClass('hide');
    }
    return setting;
}
var game;
function play(){
    sound.stopAll();
    game = new Game(setting.betAmount, setting.grid, setting.redPacketCount);
    $('#canvas').show();
    $('#table').empty().hide();


    $('#betSetting').hide();
    $('#playGame').show();

    $("#spnAmount").text(setting.betAmount);
    $("#spnGrid").text(setting.grid);
    $("#spnOdds").text(game.setting.odds);
    $("#spnTotal").text(game.setting.total);

    $("#spnRPDigged").text(game.data.digged);
    $("#spnRPRemaining").text(game.data.remain);
    $("#spnBombCount").text(game.data.bomb);
    $("#spnRPPoint").text(game.data.point);

    $('.progress .progress-bar').attr('aria-valuenow','0').css('width','0').text('0%');
    $('.alert').hide();
    $('#btnAbort').show();
    location.hash = "#btnAbort";
}
var setting;
$(function () {
    $('#btnBegin').click(function () {
        setting = gatherUserSetting();
        if(setting.ok !== true) return;
        play();
    });

    $('#btnAgain').click(function(){
        // $('#betSetting').show();
        // $('#playGame').hide();
        $('#btnAgain').hide();
        play();
    });
    $('#btnAbort').click(function(){
        location.hash = "";
        $('#betSetting').show();
        $('#playGame').hide();
        $('#btnAgain').hide();
    });
});
}();
