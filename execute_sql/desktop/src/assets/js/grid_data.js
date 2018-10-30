
/**
 * 格子信息
 * @constructor
 * @param {number} x - X坐标
 * @param {number} y - Y坐标
 */
function GridData(x, y,game) {
    this.dom = $({});
    this.game = game;
    this.x = x;
    this.y = y;
    this.isDigged = false;
    this.isRedPacket = false;
    // this.isBomb = false;
    this.point = 0;
    /**
     * 处理格子点击时间
     */
    this.handle = function(){
        if(this.isDigged === true) return;

        var borderSize = Math.sqrt(game.setting.grid);
        // this.rect.attr({fill:'#ddd'});
        // this.rect.unhover(this.rect.hoverIn,this.rect.hoverOut);
        
        // //https://git.xuwenliang.com/leo/notes/src/master/svg/get_transform_matrix_parts.md
        // var transform = this.rect.parent().attr('transform').localMatrix.split();//dx,dy
        // var cv = $('#canvas'),cvPos = cv.position(),cvWidth = cv.width();
        // var ratio = cvWidth/game.define.canvasSize;
        // var port = {
        //     x: cvPos.left + transform.dx*ratio,
        //     y: cvPos.top + transform.dy*ratio,
        //     width: this.rect.attr('width')*ratio,
        //     height: this.rect.attr('height')*ratio,
        // }
        // var animate = new Animation(game);
        if(this.isRedPacket){
            game.data.remain--;
            game.data.digged++;
            game.data.point += this.point;
            $("#spnRPDigged").text(game.data.digged);
            $("#spnRPRemaining").text(game.data.remain);
            $("#spnRPPoint").text(game.data.point);


            var progress = game.data.digged/game.setting.count;
            var progressValue = Math.round(progress*100);
            var progressPercent = progressValue + "%";
            $('.progress .progress-bar')
                .attr('aria-valuenow',progressValue)
                .css('width',progressPercent).text(progressPercent);

            if(game.data.remain == 0){
                $('#btnAbort').hide();
                $('.alert.alert-success').show();
                game.gameOver();
                $('#btnAgain').show();
                sound.winning.play();
                // animate.winning();
                game.eventHub.trigger("good-game")
            }else{
                sound.lucky.play();
                
            }
        }else{
            sound.bomb.play();
            
            $('.alert.alert-danger').show();
            $('#btnAbort').hide();
            game.gameOver();
            $('#btnAgain').show();
            
            game.eventHub.trigger("game-over")
        }
        this.isDigged = true;
        this.show();
        
    }

    this.show = function(){
        if(this.isRedPacket){
            this.dom.find('.front>div,.back>div').text(this.point);
            this.dom.flip(true);
        }else{
            this.dom.find('img').attr('src',imgList["bomb-w-bg"]);
        }
    }
}