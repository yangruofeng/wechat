
/**
 * 挖红包游戏
 * @constructor
 * @param {number} betAmount - 下注额度
 * @param {number} grid - 玩家选择的格子数
 * @param {number} count - 玩家选择的红包数量
 */
function Game(betAmount, grid, count) {
    this.eventHub = $({});
    /**
     * 常量
     */
    this.define = {
        canvasSize:500,
        font:"iconfont",
    }
    /**
     * 状态
     */
    this.state = {
        playing:true
    }
    var odds = Math.round((grid - count + 1) * 0.95 * 100)/100; // 赔率
    var total = Math.round(betAmount * odds); // 红包总额
    var borderSize = Math.sqrt(grid);
    var gridSize = this.define.canvasSize/borderSize;
    /**
     * 当次游戏设置
     */
    this.setting = { 
        odds: odds, 
        total: total, 
        betAmount: betAmount, 
        grid: grid, 
        count: count,
        borderSize:borderSize,
        gridSize:gridSize,
        fontSize: gridSize/3,
    };
    this.data = {
        bomb:grid-count,
        digged:0,
        remain:count,
        point:0
    };
    /**
     * 红包数组：位置、点数
     */
    this.redPacketData = [];
    /**
     * 格子数组: 坐标、是否红包、红包点数
     */
    this.gridData = [];

    /**
     * 分配红包
     */
    this.initRedPacket = function () {
        var redPacketList = [];
        var points = divideUniformlyRandomly(this.setting.total,this.setting.count);
        while (true) {
            if (redPacketList.length === this.setting.count) {
                redPacketList = redPacketList.distinct();
                if (redPacketList.length === this.setting.count) {
                    break;
                }
            }
            var point = points[redPacketList.length];
            var position = Math.floor(Math.random() * this.setting.grid);
            redPacketList.push(new RedPacket(point, position));
        }
        this.redPacketData = redPacketList;
    }

    /**
     * 初始化方格信息数组函数
     */
    this.initGrid = function () {
        var x, y, rowNum = Math.sqrt(this.setting.grid);
        var lineNum = rowNum;
        var gridList = [];
        for (var i = 0; i < rowNum; i++) {
            gridList[i] = [];
            for (var j = 0; j < lineNum; j++) {
                gridList[i][j] = new GridData(i, j,this);
            }
        }
        for (var k = 0, mlen = this.redPacketData.length; k < mlen; k++) {
            var gridData = this.redPacketData[k];
            x = parseInt(gridData.position / lineNum);
            y = gridData.position % lineNum;
            gridList[x][y].isRedPacket = true;
            if(location.hash == "#debug" || window.debug)
                console.log("x: " + x + ", y: "+ y);

            gridList[x][y].point = gridData.point;
        }
        this.gridData = gridList;
    }
    /**
     * 初始化红包和格子
     */
    this.initData = function () {
        this.initRedPacket();
        this.initGrid();
    }

    /**
     * 按坐标取格子数据
     */
    this.getGridData = function(x,y){
        return this.gridData[x][y];
    }

    this.gameOver = function(){
        this.state.playing = false;
        this.canvas.renderResultCanvas()
    }

    this.initData();
    this.canvas = new Canvas(this);
    this.canvas.renderCanvasToReady();
    // this.canvas.renderCanvasToPlay();

    this.eventHub.on("game-over",function(){
        this.canvas.showAllGrid();
    }.bind(this));

    this.eventHub.on("good-game",function(){
        this.canvas.showAllGrid();
    }.bind(this));
}