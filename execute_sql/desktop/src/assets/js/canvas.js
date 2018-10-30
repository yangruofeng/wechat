/**
 * 画布操作封装
 * @constructor
 * @param {Game} game 
 */
function Canvas(game) {
    this.game = game;
    this.svg = Snap("#canvas");
    this.table = $("#table");

    /**
     * 初始化画布，并清空
     */
    this.initCanvas = function () {
        var def = this.game.define,
            set = this.game.setting;
        var canvasWidth = def.canvasSize;
        var canvasHeight = def.canvasSize + set.gridSize + 2 * set.grid;

        this.svg.attr({
            viewBox: "0 0 " + canvasWidth + " " + canvasHeight
        });
        var paper = this.svg.paper;
        paper.clear();

        shadowRed = paper.filter(Snap.filter.shadow(2, 2, 3, '#d84a5a', 0.5));
        shadowYellow = paper.filter(Snap.filter.shadow(0, 0, 8, 'yellow', 0.8));

        var gs = this.game.setting.gridSize;
        var imgW = gs * 7.4506 / 9.3132 * 0.9,
            imgH = 0.9 * gs;
        var bgAttr = {
            patternUnits: "objectBoundingBox",
            viewBox: "0 0 " + gs + " " + gs,
        }
        var img = paper.image(imgList["bg"], 0, 0, imgW, imgH)
            .attr({
                x: (gs - imgW) / 2,
                y: gs * 0.05
            });
        if (!isFirefox()) {
            img.attr({
                filter: shadowRed
            })
        }
        this.bgRedPacket = img.toPattern(0, 0, 1, 1);
        this.bgRedPacket.attr(bgAttr);

        img = img.clone();
        if (!isFirefox()) {
            img.attr({
                filter: shadowYellow
            })
        }
        this.bgShadowRedPacket = img.toPattern(0, 0, 1, 1);
        this.bgShadowRedPacket.attr(bgAttr);

        img = paper.image(imgList["bomb-w-bg"], 0, 0, imgW, imgH).attr({
            x: (gs - imgW) / 2,
            y: gs * 0.05
        });
        this.bgBomb = img.toPattern(0, 0, 1, 1);
        this.bgBomb.attr(bgAttr);

        img = paper.image(imgList["gift-w-bg"], 0, 0, imgW, imgH).attr({
            x: (gs - imgW) / 2,
            y: gs * 0.05
        });
        this.bgGift = img.toPattern(0, 0, 1, 1);
        this.bgGift.attr(bgAttr);

        return this.svg;
    }
    /**
     * 将表格呈现到界面
     */
    this.renderCanvasToReady = function () {
        var s = this.initCanvas();
        var bs = this.game.setting.borderSize,
            gs = this.game.setting.gridSize,
            fs = this.game.setting.fontSize;

        var ty = this.game.define.canvasSize,
            tx = (ty - this.game.setting.gridSize) / 2;
        var ox = tx,
            oy = ty + 2 * this.game.setting.grid;

        for (var x = 0; x < bs; x++) {
            for (var y = 0; y < bs; y++) {
                var idx = x * bs + y;
                var tx = ox + 2 * idx,
                    ty = oy + 2 * idx;

                var rect = s.paper.rect(0, 0, gs, gs).attr({
                    strokeOpacity: 0,
                    fill: this.bgRedPacket,
                    tx: tx,
                    ty: ty,
                });

                rect.cv = this;

                var text = s.paper.text(0, 0, "").attr({
                    fontSize: fs + "px",
                    fill: "yellow",
                    fontFamily: this.game.define.font,
                    textLength: 20 * 3,
                    dx: gs / 2,
                    dy: 2 * gs / 3,
                }).addClass('mid');

                var g = s.paper.g(rect, text);
                g.attr({
                    gx: x,
                    gy: y,
                    cx: tx,
                    cy: ty,
                });
                g.addClass("packet");

                var e = new Snap.Matrix;
                e.translate(tx, ty);
                g.transform(e);
            }
        }
        var animate = new Animation(this.game);

        var tasks = [];
        var gSet = s.selectAll('g.packet');
        tasks.push(animate.createCardShuffleAnim(gSet));

        s.selectAll('g.packet').forEach(function (g, idx) {
            tasks.push(function (g, idx) {
                return animate.createCardDispatchAnim(g, idx);
            }(g, idx));
        });

        tasks.push(this.renderGrid.bind(this));

        series(tasks);
    }

    /**
     * 使用html渲染游戏交互界面
     */
    this.renderGrid = function (done) {
        console.log("anim finished!");
        $("#canvas").hide();
        var bs = this.game.setting.borderSize;
        var colW = Math.floor(12 / bs);
        for (var x = 0; x < bs; x++) {
            var row = $('<div class="row"></div>');
            
            for (var y = 0; y < bs; y++) {
                var grid = game.getGridData(x, y);

                var cell = $('<div class="cell"></div>');
                var classes = [];
                classes.push("col-xs-" + colW);
                classes.push("col-sm-" + colW);
                classes.push("col-md-" + colW);
                classes.push("col-lg-" + colW);
                if(bs == 5 && y == 0){
                    classes.push("col-xs-offset-" + 1);
                    classes.push("col-sm-offset-" + 1);
                    classes.push("col-md-offset-" + 1);
                    classes.push("col-lg-offset-" + 1);
                }
                cell.addClass(classes.join(' '));
                
                var front = $('<div class="front"><img src="'+imgList["bg"]+'" /><div></div></div>')
                var back = $('<div class="back"><img src="'+imgList["gift-w-bg"]+'" /><div></div></div>')
                var box = $('<div class="flipper"></div>').append(front).append(back);
                grid.dom = box; box.data('grid',grid);
                box.click(function () {
                    $(this).flip(true, function () {
                        var grid = $(this).data('grid');
                        grid.handle();
                    });
                });
                box.on('flip:done', function () {
                    // console.log("on:",this)
                });
                box.flip({
                    axis: "y",
                    reverse: false,
                    trigger: "manual",
                    speed: 500,
                    forceHeight: false,
                    forceWidth: false,
                    autoSize: true,
                    front: '.front',
                    back: '.back'
                });

                cell.append(box);
                row.append(cell);
            }
            this.table.append(row);
        }
        this.table.show();
    }
    /**
     * 将表格呈现到界面
     */
    this.renderCanvasToPlay = function () {
        var s = this.initCanvas();
        var bs = this.game.setting.borderSize,
            gs = this.game.setting.gridSize,
            fs = this.game.setting.fontSize;

        for (var x = 0; x < bs; x++) {
            for (var y = 0; y < bs; y++) {
                var tx = x * gs,
                    ty = y * gs;
                var rect = s.paper.rect(tx, ty, gs, gs).attr({
                    // fill: "#ccc",
                    stroke: "#fff",
                    // strokeWidth: 1,
                    tx: tx,
                    ty: ty,
                    fill: this.bgRedPacket,
                });
                rect.cv = this;
                rect.hover(function () {
                    this.attr({
                        fill: this.cv.bgShadowRedPacket
                    })
                }, function () {
                    this.attr({
                        fill: this.cv.bgRedPacket
                    })
                })
            }
        }
    }

    /**
     * 游戏结束时显示所有格子状态
     */
    this.renderResultCanvas = function () {
        var s = this.initCanvas();

        var bs = this.game.setting.borderSize,
            gs = this.game.setting.gridSize,
            fs = this.game.setting.fontSize;

        for (var x = 0; x < bs; x++) {
            for (var y = 0; y < bs; y++) {
                var tx = x * gs,
                    ty = y * gs;
                var rect = s.paper.rect(0, 0, gs, gs).attr({
                    stroke: "#ddd",
                    strokeWidth: 1,
                });

                var text = s.paper.text(0, 0, "").attr({
                    fontSize: fs + "px",
                    fontFamily: this.game.define.font,
                    textLength: 20 * 3,
                    dx: gs / 2,
                    dy: 2 * gs / 3,
                }).addClass('mid');

                var g = s.paper.g(rect, text);
                g.addClass('open');

                var e = new Snap.Matrix;
                e.translate(tx, ty);
                g.transform(e);

                var grid = this.game.getGridData(x, y);
                var box = rect.getBBox();
                if (grid.isRedPacket) {
                    rect.attr({
                        fill: this.bgGift
                    });
                    text.attr({
                        text: grid.point, // "\ue63d"+
                        fill: "yellow",
                        fontSize: "20px",
                    })
                } else {
                    rect.attr({
                        fill: this.bgBomb
                    });
                    // text.attr({fill:"black",text:"\uea06",dy:0.8*gs});
                    // var b = text.getBBox();
                }
            }
        }
    }

    this.showAllGrid = function () {
        var size = game.setting.borderSize
        for (var x = 0; x < size; x++) {
            for (var y = 0; y < size; y++) {
                var grid = game.getGridData(x, y);
                if (!grid.isDigged) {
                    grid.show();
                }
            }
        }
    }
}