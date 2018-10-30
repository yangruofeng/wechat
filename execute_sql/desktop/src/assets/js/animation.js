/**
 * 集中管理动画相关代码
 * @constructor
 * @param {Game} game 
 */
function Animation(game){
    this.game = game;
    this.shufflePath = game.canvas.svg.paper.path({
        d: "m182,496c-23,-26 -58,-200 -33,-246c24,-45 96,-72 122,-7c26,64 16,130 -16,196c-32,66 -50,82.522171 -73,56z",
        stroke:"none",
        strokeOpacity:0,
        fill:"none",
    });
    this.shufflePathLen = Snap.path.getTotalLength(this.shufflePath); // 获取path的长度
    
    /**
     * 发牌动画
     */
    this.createCardDispatchAnim=function(g,idx,pos){
        var game = this.game;
        var gs = game.setting.gridSize;
        return function(done) {
            var cx = parseInt(g.attr("cx")), cy = parseInt(g.attr('cy')); // 当前坐标
            var gx = parseInt(g.attr("gx")), gy = parseInt(g.attr('gy')); // 当前格子坐标
            var tx = gx*gs, ty = gy*gs; // 目标坐标
            var offset = Math.sqrt(Math.pow(tx - cx,2)+Math.pow(ty - cy,2));//当前距离目标的直线距离
            var ox = cx - tx,oy = cy - ty;//当前距离目标的x,y方向距离

            Snap.animate(0, offset, function(value) {
                var e = new Snap.Matrix;
                e.translate(cx-value*ox/offset,cy-value*oy/offset);
                g.transform(e);
            }, 300, mina.easeout(), function() {done();})
        };
    }
    /**
     * 洗牌动画
     */
    this.createCardShuffleAnim=function(gSet){
        var shufflePathLen = this.shufflePathLen,shufflePath = this.shufflePath;
        return function(done){
            gSet.forEach(function(g,idx){
                setTimeout(function(g,isLastOne){
                    return function(){
                        Snap.animate(0, shufflePathLen, function(val) {
                            var point = Snap.path.getPointAtLength(shufflePath, val); // 根据path长度变化获取坐标
                            var m = new Snap.Matrix();
                            m.translate(point.x, point.y);
                            // m.rotate(point.alpha-90); // 总是朝着曲线方向。point.alpha：点的切线和水平线形成的夹角
                            g.transform(m);
                        }, 1500, mina.easeout(), function() {
                            if(isLastOne) done();
                        });
                    }
                }(g,idx == (gSet.length -1)),idx*50);
            });
        }
    }
    /**
     * 收牌动画
     */
    this.createCardCollectAnim=function(g,idx){
        var game = this.game;
        return function(done) {
            var x = parseInt(g[0].attr("x")), y = parseInt(g[0].attr('y'));
            var ty = game.define.canvasSize, tx = (ty-game.setting.gridSize)/2;
            var offset = Math.sqrt(Math.pow(tx - x,2)+Math.pow(ty - y,2));
            var ox = tx - x,oy = ty -y + 2*game.setting.grid;

            Snap.animate(0, offset, function(value) {
                var e = new Snap.Matrix;
                e.translate(value*ox/offset,value*oy/offset),
                g.transform(e)
            }, 300, mina.easeout(), function() {
                var e = new Snap.Matrix;
                e.translate(ox-2*idx,oy-2*idx),
                g.transform(e);
                done();
            })
        };
    }
    this.winning=function(){
        var sparkleList = new SparkleList();
        sparkleList.add(new Sparkle({
            colors : ['purple', 'pink', 'teal', 'grey'],
            num_sprites: 72,
            lifespan: 500,
            radius: 800,
            sprite_size: 24,
            shape: "triangle"
            }));
        sparkleList.add(new Sparkle({
            colors : ['yellow', 'red'],
            num_sprites: 32,
            lifespan: 700,
            radius: 400,
            sprite_size: 14,
            shape: "circle"
            }));
        sparkleList.add(new Sparkle({
            colors : ['green', 'teal','maroon'],
            num_sprites: 12,
            lifespan: 2000,
            radius: 600,
            sprite_size: 4,
            shape: "square"
            }));
        sparkleList.add(new Sparkle({
            colors : ['purple', 'pink', 'teal', 'grey'],
            num_sprites: 72,
            lifespan: 1500,
            radius: 500,
            sprite_size: 24,
            shape: "triangle"
            }));
        sparkleList.add(new Sparkle({
            colors : ['yellow', 'red'],
            num_sprites: 32,
            lifespan: 1700,
            radius: 300,
            sprite_size: 14,
            shape: "circle"
            }));
        sparkleList.add(new Sparkle({
            colors : ['green', 'teal','maroon'],
            num_sprites: 12,
            lifespan: 2000,
            radius: 600,
            sprite_size: 4,
            shape: "square"
            }));
        var tasks = [];
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        tasks.push(sparkleList.fireAtCenter.bind(sparkleList));
        series(tasks);
    };
    this.explosion=function(x,y,w,h){
        var id = 'explosion';
        var overlay = new Overlay({tag:'canvas', id:id,noBackground:false});
        overlay.show();
        var fb = new FireBomb(id,window.innerWidth,window.innerHeight);
        fb.run();
        setTimeout(function() {
            fb.stop()
            overlay.remove();
        }, 1000);
    }
    this.lucky=function(x,y,w,h){
        var overlay = new Overlay({
            tag:'canvas', 
            id:'explosion',
            x:x,
            y:y,
            width:w,
            height:h,
            noBackground:false
        });
        overlay.show();

        setTimeout(function() {
            overlay.remove();
        }, 300);
    }
}