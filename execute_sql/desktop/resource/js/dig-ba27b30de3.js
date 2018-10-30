function Animation(t) {
	this.game = t,
	this.shufflePath = t.canvas.svg.paper.path({
		d: "M80 240 C 0 140, 200 140, 80 240",
		stroke: "none",
		strokeOpacity: 0,
		fill: "none"
	}),
	//this.shufflePathLen = Snap.path.getTotalLength(this.shufflePath),
	this.createCardDispatchAnim = function(t, i, un_flag, M) { //发红包
		var e = this.game.setting.gridSize, time = un_flag ? 1 : 100;
		return function(i) {
			var M = parseInt(t.attr("cx")),
			s = parseInt(t.attr("cy")),
			n = parseInt(t.attr("gx")),
			a = parseInt(t.attr("gy")),
			w = n * e,
			g = a * e,
			I = Math.sqrt(Math.pow(w - M, 2) + Math.pow(g - s, 2)),
			A = M - w,
			o = s - g;

			Snap.animate(0, I,
			function(i) {
				var e = new Snap.Matrix;
				e.translate(M - i * A / I, s - i * o / I),
				t.transform(e)
			},
			time, mina.easeout(),
			function() {
				i()
			})
		}
	},
	this.createCardShuffleAnim = function(t) {
		var i = this.shufflePathLen,
		M = this.shufflePath;

		return function(e) {
			t.forEach(function(s, n) {
				setTimeout(function(t, s) {
					return function() {
						Snap.animate(0, i,
						function(i) {
							var e = Snap.path.getPointAtLength(M, i),
							s = new Snap.Matrix;
							s.translate(e.x, e.y),
							t.transform(s)
						},
						0, mina.easeout(),
						function() {
							s && e()
						})
					}
				} (s, n == t.length - 1), 50 * n)
			})
		}
	},
	this.createCardCollectAnim = function(t, i) {
		var M = this.game;
		return function(e) {
			var s = parseInt(t[0].attr("x")),
			n = parseInt(t[0].attr("y")),
			a = M.define.canvasSize,
			w = (a - M.setting.gridSize) / 2,
			g = Math.sqrt(Math.pow(w - s, 2) + Math.pow(a - n, 2)),
			I = w - s,
			A = a - n + 2 * M.setting.grid;
			Snap.animate(0, g,
			function(i) {
				var M = new Snap.Matrix;
				M.translate(i * I / g, i * A / g),
				t.transform(M)
			},
			300, mina.easeout(),
			function() {
				var M = new Snap.Matrix;
				M.translate(I - 2 * i, A - 2 * i),
				t.transform(M),
				e()
			})
		}
	},
	this.winning = function() {
		var t = new SparkleList;
		t.add(new Sparkle({
			colors: ["purple", "pink", "teal", "grey"],
			num_sprites: 72,
			lifespan: 500,
			radius: 800,
			sprite_size: 24,
			shape: "triangle"
		})),
		t.add(new Sparkle({
			colors: ["yellow", "red"],
			num_sprites: 32,
			lifespan: 700,
			radius: 400,
			sprite_size: 14,
			shape: "circle"
		})),
		t.add(new Sparkle({
			colors: ["green", "teal", "maroon"],
			num_sprites: 12,
			lifespan: 2e3,
			radius: 600,
			sprite_size: 4,
			shape: "square"
		})),
		t.add(new Sparkle({
			colors: ["purple", "pink", "teal", "grey"],
			num_sprites: 72,
			lifespan: 1500,
			radius: 500,
			sprite_size: 24,
			shape: "triangle"
		})),
		t.add(new Sparkle({
			colors: ["yellow", "red"],
			num_sprites: 32,
			lifespan: 1700,
			radius: 300,
			sprite_size: 14,
			shape: "circle"
		})),
		t.add(new Sparkle({
			colors: ["green", "teal", "maroon"],
			num_sprites: 12,
			lifespan: 2e3,
			radius: 600,
			sprite_size: 4,
			shape: "square"
		}));
		var i = [];
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		i.push(t.fireAtCenter.bind(t)),
		series(i)
	},
	this.explosion = function(t, i, M, e) {
		var s = "explosion",
		n = new Overlay({
			tag: "canvas",
			id: s,
			noBackground: !1
		});
		n.show();
		var a = new FireBomb(s, window.innerWidth, window.innerHeight);
		a.run(),
		setTimeout(function() {
			a.stop(),
			n.remove()
		},
		1e3)
	},
	this.lucky = function(t, i, M, e) {
		var s = new Overlay({
			tag: "canvas",
			id: "explosion",
			x: t,
			y: i,
			width: M,
			height: e,
			noBackground: !1
		});
		s.show(),
		setTimeout(function() {
			s.remove()
		},
		300)
	}
}
function Canvas(t) {
	this.game = t,
	this.svg = Snap("#canvas"),
	this.table = $("#table"),
	this.initCanvas = function() {
		var t = this.game.define,
		i = this.game.setting,
		M = t.canvasSize,
		e = t.canvasSize + i.gridSize + 2 * i.grid;
		this.svg.attr({
			viewBox: "0 0 " + M + " " + e
		});
		var s = this.svg.paper;
		s.clear(),
		shadowRed = s.filter(Snap.filter.shadow(2, 2, 3, "#d84a5a", .5)),
		shadowYellow = s.filter(Snap.filter.shadow(0, 0, 8, "yellow", .8));
		var n = this.game.setting.gridSize,
		a = 7.4506 * n / 9.3132 * .9,
		w = .9 * n,
		g = {
			patternUnits: "objectBoundingBox",
			viewBox: "0 0 " + n + " " + n
		},
		I = s.image(imgList.bg, 0, 0, a, w).attr({
			x: (n - a) / 2,
			y: .05 * n
		});
		return isFirefox() || I.attr({
			filter: shadowRed
		}),
		this.bgRedPacket = I.toPattern(0, 0, 1, 1),
		this.bgRedPacket.attr(g),
		I = I.clone(),
		isFirefox() || I.attr({
			filter: shadowYellow
		}),
		this.bgShadowRedPacket = I.toPattern(0, 0, 1, 1),
		this.bgShadowRedPacket.attr(g),
		I = s.image(imgList["bomb-bg"], 0, 0, a, w).attr({
			x: (n - a) / 2,
			y: .05 * n
		}),
		this.bgBomb = I.toPattern(0, 0, 1, 1),
		this.bgBomb.attr(g),
		I = s.image(imgList["gift-bg"], 0, 0, a, w).attr({
			x: (n - a) / 2,
			y: .05 * n
		}),
		this.bgGift = I.toPattern(0, 0, 1, 1),
		this.bgGift.attr(g),
		this.svg
	},
	this.renderCanvasToReady = function() {
		for (var t = this.initCanvas(), i = this.game.setting.borderSize, M = this.game.setting.gridSize, e = this.game.setting.fontSize, s = I = ((A = this.game.define.canvasSize) - this.game.setting.gridSize) / 2, n = A + 2 * this.game.setting.grid, a = 0; a < i; a++) for (var w = 0; w < i; w++) {
			var g = a * i + w,
			I = s + 2 * g,
			A = n + 2 * g,
			o = t.paper.rect(0, 0, M, M).attr({
				strokeOpacity: 0,
				fill: this.bgRedPacket,
				tx: I,
				ty: A
			});
			o.cv = this;
			var c = t.paper.text(0, 0, "").attr({
				fontSize: e + "px",
				fill: "yellow",
				fontFamily: this.game.define.font,
				textLength: 60,
				dx: M / 2,
				dy: 2 * M / 3
			}).addClass("mid"),
			C = t.paper.g(o, c);
			C.attr({
				gx: a,
				gy: w,
				cx: I,
				cy: A
			}),
			C.addClass("packet");
			var N = new Snap.Matrix;
			N.translate(I, A),
			C.transform(N)
		}
		var r = new Animation(this.game),
		L = [],
		D = t.selectAll("g.packet");
		var un_flag = this.game.unfinishGame ? true : false;
		//if(!un_flag) L.push(r.createCardShuffleAnim(D)); //转圈圈
		t.selectAll("g.packet").forEach(function(t, i) {
			L.push(function(t, i) {
				return r.createCardDispatchAnim(t, i, un_flag)
			} (t, i))
		}),
		L.push(this.renderGrid.bind(this)),
		series(L)
	},
	this.renderGrid = function(i) {
		$("#canvas").hide();
		var un_game = this.game.unfinishGame ? this.game.unfinishGame.grids : {};
		for (var M = this.game.setting.borderSize, e = Math.floor(12 / M), s = 0; s < M; s++) {
			var p = 0;
			for (var n = $('<div class="row"></div>'), a = 0; a < M; a++) {
				var k = a + (M - 1) * s + s,
				w = t.getGridData(s, a),
				g = $('<div class="cell"></div>'),
				I = [];
				I.push("col-xs-" + e),
				I.push("col-sm-" + e),
				I.push("col-md-" + e),
				I.push("col-lg-" + e),
				5 == M && 0 == a && (I.push("col-xs-offset-1"), I.push("col-sm-offset-1"), I.push("col-md-offset-1"), I.push("col-lg-offset-1")),
				g.addClass(I.join(" "));
				var A = $('<div class="front"><img src="' + imgList.bg + '" /><div></div></div>'),
				o = $('<div class="back" index="' + k + '"><img src="' + imgList["gift-w-bg"] + '" /><div></div></div>'), //' + imgList["gift-bg"] + '
				c = $('<div class="flipper"></div>').append(A).append(o);
				w.dom = c,
				c.data("grid", w),
				c.click(function() {
					$(this).flip(!0,
					function() {
						$(this).data("grid").handle()
					})
				}),
				c.on("flip:done",
				function() {}),
				c.flip({
					axis: "y",
					reverse: !1,
					trigger: "manual",
					speed: 500,
					forceHeight: !1,
					forceWidth: !1,
					autoSize: !0,
					front: ".front",
					back: ".back"
				}),
				g.append(c),
				n.append(g)
			}
			this.table.append(n)
		}

		for(var key in un_game){
			$('#table .flipper').eq(key).flip(!0)
			var  num = un_game[key],count  = num.toString();
			if(num >= 1000 && num < 1000000){
				count = parseFloat((num/1000).toFixed(1)) + 'K';
			}else if(num >= 1000000){
				count = parseFloat((num/10000).toFixed(1)) + 'M';
			}
			$('#table .flipper').eq(key).find(".back").addClass('g-open');
			$('#table .flipper').eq(key).find(".front>div,.back>div").text(count)
		}
		this.table.show()
	},
	this.renderCanvasToPlay = function() {
		for (var t = this.initCanvas(), i = this.game.setting.borderSize, M = this.game.setting.gridSize, e = (this.game.setting.fontSize, 0); e < i; e++) for (var s = 0; s < i; s++) {
			var n = e * M,
			a = s * M,
			w = t.paper.rect(n, a, M, M).attr({
				stroke: "#fff",
				tx: n,
				ty: a,
				fill: this.bgRedPacket
			});
			w.cv = this,
			w.hover(function() {
				this.attr({
					fill: this.cv.bgShadowRedPacket
				})
			},
			function() {
				this.attr({
					fill: this.cv.bgRedPacket
				})
			})
		}
	},
	this.renderResultCanvas = function() {
		for (var t = this.initCanvas(), i = this.game.setting.borderSize, M = this.game.setting.gridSize, e = this.game.setting.fontSize, s = 0; s < i; s++) for (var n = 0; n < i; n++) {
			var a = s * M,
			w = n * M,
			g = t.paper.rect(0, 0, M, M).attr({
				stroke: "#ddd",
				strokeWidth: 1
			}),
			I = t.paper.text(0, 0, "").attr({
				fontSize: e + "px",
				fontFamily: this.game.define.font,
				textLength: 60,
				dx: M / 2,
				dy: 2 * M / 3
			}).addClass("mid"),
			A = t.paper.g(g, I);
			A.addClass("open");
			var o = new Snap.Matrix;
			o.translate(a, w),
			A.transform(o);
			var c = this.game.getGridData(s, n);
			g.getBBox();
			c.isRedPacket ? (g.attr({
				fill: this.bgGift
			}), I.attr({
				text: c.point,
				fill: "yellow",
				fontSize: "20px"
			})) : g.attr({
				fill: this.bgBomb
			})
		}
	},
	this.showAllGrid = function() {
		for (var i = t.setting.borderSize,
		M = 0; M < i; M++) for (var e = 0; e < i; e++) {
			var s = t.getGridData(M, e);
			s.isDigged || s.show()
		}
	}
}
function Particle(t, i) {
	this.colors = ["#6A0000", "#900000", "#902B2B", "#A63232", "#A62626", "#FD5039", "#C12F2A", "#FF6540", "#f93801"],
	this.spring = .1,
	this.friction = .85,
	this.decay = .95,
	this.r = randomIntFromInterval(10, 70),
	this.R = 100 - this.r,
	this.angle = 2 * Math.random() * Math.PI,
	this.center = t,
	this.pos = {},
	this.pos.x = this.center.x + this.r * Math.cos(this.angle),
	this.pos.y = this.center.y + this.r * Math.sin(this.angle),
	this.dest = {},
	this.dest.x = this.center.x + this.R * Math.cos(this.angle),
	this.dest.y = this.center.y + this.R * Math.sin(this.angle),
	this.color = this.colors[~~ (Math.random() * this.colors.length)],
	this.vel = {
		x: 0,
		y: 0
	},
	this.acc = {
		x: 0,
		y: 0
	},
	this.update = function() {
		var t = this.dest.x - this.pos.x,
		i = this.dest.y - this.pos.y;
		this.acc.x = t * this.spring,
		this.acc.y = i * this.spring,
		this.vel.x += this.acc.x,
		this.vel.y += this.acc.y,
		this.vel.x *= this.friction,
		this.vel.y *= this.friction,
		this.pos.x += this.vel.x,
		this.pos.y += this.vel.y,
		this.r > 0 && (this.r *= this.decay)
	},
	this.draw = function() {
		i.ctx.fillStyle = this.color,
		i.ctx.beginPath(),
		i.ctx.arc(this.pos.x, this.pos.y, this.r, 0, 2 * Math.PI),
		i.ctx.fill()
	}
}
function Explosion(t) {
	this.pos = {
		x: Math.random() * t.cw,
		y: Math.random() * t.ch
	},
	this.particles = [];
	for (var i = 0; i < 50; i++) this.particles.push(new Particle(this.pos, t));
	this.update = function() {
		for (var t = 0; t < this.particles.length; t++) this.particles[t].update(),
		this.particles[t].r < .5 && this.particles.splice(t, 1)
	},
	this.draw = function() {
		for (var t = 0; t < this.particles.length; t++) this.particles[t].draw()
	}
}
function FireBomb(t, i, M) {
	this.canvas = document.getElementById(t),
	this.ctx = this.canvas.getContext("2d"),
	this.cw = this.canvas.width = i,
	this.cx = this.cw / 2,
	this.ch = this.canvas.height = M,
	this.cy = this.ch / 2,
	this.ctx.strokeStyle = "#fff",
	this.requestId = null,
	this.rad = Math.PI / 180,
	this.explosions = [],
	this.draw = function() {
		this.requestId = window.requestAnimationFrame(this.draw.bind(this)),
		this.ctx.clearRect(0, 0, this.cw, this.ch),
		this.ctx.globalCompositeOperation = "lighter",
		Math.random() < .1 && this.explosions.push(new Explosion(this));
		for (var t = 0; t < this.explosions.length; t++) this.explosions[t].update(),
		this.explosions[t].draw()
	},
	this.run = function() {
		this.requestId && (window.cancelAnimationFrame(this.requestId), this.requestId = null),
		this.cw = this.canvas.width = window.innerWidth,
		this.cx = this.cw / 2,
		this.ch = this.canvas.height = window.innerHeight,
		this.cy = this.ch / 2,
		this.draw()
	},
	this.stop = function() {
		window.cancelAnimationFrame(this.requestId)
	}
}
function Game(t, i, M, unfinishGame) {
	this.eventHub = $({}),
	this.define = {
		canvasSize: 240,
		font: "iconfont"
	},
	this.state = {
		playing: !0
	};
	var e = Math.round(.95 * (i - M + 1) * 100) / 100,
	s = Math.round(t * e),
	n = Math.sqrt(i),
	a = this.define.canvasSize / n;
	this.setting = {
		odds: e,
		total: s,
		betAmount: t,
		grid: i,
		count: M,
		borderSize: n,
		gridSize: a,
		fontSize: a / 3
	},
	this.data = {
		bomb: i - M,
		digged: 0,
		remain: M,
		point: 0
	},
	this.unfinishGame = unfinishGame,
	this.redPacketData = [],
	this.gridData = [];
	this.initRedPacket = function() {
		for (var t = [], i = divideUniformlyRandomly(this.setting.total, this.setting.count);;) {
			if (t.length === this.setting.count && (t = t.distinct()).length === this.setting.count) break;
			var M = i[t.length],
			e = Math.floor(Math.random() * this.setting.grid);
			t.push(new RedPacket(M, e))
		}
		this.redPacketData = t
	},
	this.initGrid = function() {
		for (var t, i, M = Math.sqrt(this.setting.grid), e = M, s = [], n = 0; n < M; n++) {
			s[n] = [];
			for (var a = 0; a < e; a++) s[n][a] = new GridData(n, a, this)
		}
		for (var w = 0,
		g = this.redPacketData.length; w < g; w++) {
			var I = this.redPacketData[w];
			t = parseInt(I.position / e),
			i = I.position % e,
			s[t][i].isRedPacket = !0,
			("#debug" == location.hash || window.debug) && console.log("x: " + t + ", y: " + i),
			s[t][i].point = I.point
		}
		this.gridData = s
	},
	this.initData = function() {
		this.initRedPacket(),
		this.initGrid()
	},
	this.getGridData = function(t, i) {
		return this.gridData[t][i]
	},
	this.gameOver = function() {
		this.state.playing = !1,
		this.canvas.renderResultCanvas()
	},
	this.initData(),
	this.canvas = new Canvas(this),
	this.canvas.renderCanvasToReady(),
	this.eventHub.on("game-over",
	function() {
		var _packet_status_arr = this.gameDetail, status = 0, i = 0, _packet_list = $('#table .back'), len = _packet_list.length, ele;
		for (i = 0; i < len; i++) {
			ele = $(_packet_list[i]);
			status = _packet_status_arr[i];
			if(status != -1){
				if(!ele.hasClass('g-open')) {
					ele.find('img').attr('src',imgList['gift-bg']);
					var  count = status;
					if(status){
						count  = status.toString();
						if(status >= 1000 && status < 1000000){
							count = parseFloat((status/1000).toFixed(1)) + 'K';
						}else if(status >= 1000000){
							count = parseFloat((status/10000).toFixed(1)) + 'M';
						}
					}
					ele.find('div').text(count);
				}
			}else{
				ele.find('img').attr('src',imgList['bomb-bg']);
			}
		}
		this.canvas.showAllGrid();
	}.bind(this)),
	this.eventHub.on("good-game",
	function() {
		if(window.parent.GameCommonUpdateMemberData){
			window.parent.GameCommonUpdateMemberData();
			setTimeout(function(){
				window.parent.GameCommonUpdateMemberData();
			},5000);
			setTimeout(function(){
				window.parent.GameCommonUpdateMemberData();
			},20000);
		}
		var _packet_status_arr = this.gameDetail, status = 0, i = 0, _packet_list = $('#table .back'), len = _packet_list.length, ele;
		for (i = 0; i < len; i++) {
			ele = $(_packet_list[i]);
			status = _packet_status_arr[i];
			if(status != -1){
				if(!ele.hasClass('g-open')) {
					var  count = status;
					if(status){
						count  = status.toString();
						if(status >= 1000 && status < 1000000){
							count = (status/1000).toFixed(1) + 'K';
						}else if(status >= 1000000){
							count = (status/10000).toFixed(1) + 'M';
						}
					}
					ele.find('div').text(count);
				}
			}else{
				ele.find('img').attr('src',imgList['bomb-bg']);
			}
		}
		this.canvas.showAllGrid();
	}.bind(this))
}
function GridData(t, i, M) {
	this.dom = $({}),
	this.game = M,
	this.x = t,
	this.y = i,
	this.isDigged = !1,
	this.isRedPacket = !1,
	this.point = 0,
	this.handle = function() {
		var gridIndex = this.dom.find('.back').attr('index');
		var _data = ajaxGetPacket(gridIndex,M.setting.count);
		if(!_data) return;
		//_data.game_info.won_amount > M.setting.betAmount ? $('.game-over-wrap .i img').attr('src', 'resource/images/img-over-winner.png') : $('.game-over-wrap .i img').attr('src', 'resource/images/img-over-loser.png');
		$('#spnTotal').text(formatNum(_data.game_info.won_amount));//总共挖到的金额
		if(_data.game_info.red_packet_count <= 0){
			$('.game-over-tip .t.p').hide();
			$('.game-over-tip .t.all').show();
			$('#totalWin1').text(formatNum(_data.game_info.won_amount));
			//$('#packTotal1').text(M.setting.count - _data.game_info.red_packet_count);
		}else{
			$('.game-over-tip .t.p').show();
			$('.game-over-tip .t.all').hide();
			$('#totalWin').text(formatNum(_data.game_info.won_amount));
			$('#packTotal').text(M.setting.count - _data.game_info.red_packet_count);
		}
		$('#packetCount').text(_data.game_info.red_packet_count);//剩余红包个数
		if(_data.result != -1){ //红包
			var t = (M.setting.count - _data.game_info.red_packet_count) / M.setting.count,
			i = Math.round(100 * t),
			e = i + "%";
			$(".progress span").attr("aria-valuenow", i).css("width", e).text(e);
			_data.game_info.game_detail ? (M.gameOver(), M.gameDetail = _data.game_info.game_detail, sound.winning.play(),
			M.eventHub.trigger("good-game"),
			$('#btnBegin').removeClass('disabled'),
			setTimeout(function(){if(!$('#btnBegin').hasClass('disabled') && $('.game-setting-wrap').is(':hidden')) {$('.game-over-tip').show();$('.progress').hide();}},10)) : sound.lucky.play();
		}else{//炸弹
			sound.bomb.play(),
			M.gameOver(),
			M.gameDetail = _data.game_info.game_detail,
			M.eventHub.trigger("game-over"),
			setTimeout(function(){
				if(!$('#btnBegin').hasClass('disabled') && $('.game-setting-wrap').is(':hidden')) {
					$('.game-over-tip').show();
					$('.progress').hide();
				}

			},10);
			if(window.parent.GameCommonUpdateMemberData){
				window.parent.GameCommonUpdateMemberData();
				setTimeout(function(){
					window.parent.GameCommonUpdateMemberData();
				},5000);
				setTimeout(function(){
					window.parent.GameCommonUpdateMemberData();
				},20000);
			}
			$('#btnBegin').removeClass('disabled');
		}
		this.show(_data.result);
		_data.result != -1 ? this.dom.find('.back').addClass('g-open') : this.dom.find('.back').addClass('b-open');
		$('#table .bomb:not(.b-open) img').attr('src',imgList['bomb-bg']);
	},
	this.show = function(rets) {
		var  count = rets;
		if(rets != -1 && rets){
			count  = rets.toString();
			if(rets >= 1000 && rets < 1000000){
				count = parseFloat((rets/1000).toFixed(1)) + 'K';
			}else if(rets >= 1000000){
					count = parseFloat((rets/10000).toFixed(1)) + 'M';
			}
		}
		rets != -1 ? (this.dom.find(".front>div,.back>div").text(count), this.dom.flip(!0)) :  (this.dom.find(".front>div,.back>div").text(''), this.dom.find('.front,.back').addClass('bomb'), this.dom.find("img").attr("src", imgList["bomb-w-bg"]))
	}
}


function ajaxGetPacket(gridIndex, count){
	if(G_OVER) return;
	var res = {}, _game_id = $('#game_order_id').val();
	$.ajax({
		url: API_SITE_URL + '/index.php?act=game&op=dig',
		type: 'get',
		async: false,
		data: {game_order_id: _game_id, grid_id: gridIndex},
		dataType: 'json',
		success: function(ret){
			if(ret.CODE == 200){
				res  = ret.DATA;
				G_OVER = res.result == -1 || res.game_info.red_packet_count == 0 ? true : false;
			}else{
				$('.error-tip-wrap').show();
				var msg = CODE_ARR[ret.CODE] ? CODE_ARR[ret.CODE] : MSG_SYSYTEM_ERROR;
				$('#tipMsg').html(msg);
				hideErrorTip();
			}
		}
	});
	return res;
}

function RedPacket(t, i) {
	this.point = t,
	this.position = i
}
function Sound() {
	this.bomb = new Howl({
		src: ["resource/sound/firebomb_expl1.mp3"]
	}),
	this.winning = new Howl({
		src: ["resource/sound/winning.mp3"]
	}),
	this.lucky = new Howl({
		src: ["resource/sound/good-result.mp3"]
	}),
	this.stopAll = function() {
		this.bomb.stop(),
		this.winning.stop(),
		this.lucky.stop()
	}
}
function Sparkle(t) {
	function i(t, i) {
		for (var M = 0; M < e; M++) {
			var c = document.createElement("div"),
			C = 360 * Math.random();
			if ("true" == g || 1 == g) N = Math.random() * (2 * n - n / 4) + n / 4;
			else var N = Math.random() * (n + n) - n;
			I && $(c).css({
				"background-size": "contain",
				backgroundImage: "url(" + I + ")"
			}),
			"circle" == w ? $(c).css({
				backgroundColor: s[M % s.length],
				left: t,
				top: i,
				width: a,
				height: a,
				borderRadius: "100%",
				position: "absolute"
			}) : "square" == w ? $(c).css({
				backgroundColor: s[M % s.length],
				left: t,
				top: i,
				width: a,
				height: a,
				borderRadius: "0px",
				position: "absolute"
			}) : "triangle" == w ? (o = 0, $(c).css({
				width: "0px",
				height: "0px",
				"border-top": a + "px solid transparent",
				"border-right": a + "px solid transparent",
				"border-top": a + "px solid " + s[M % s.length],
				left: t,
				top: i,
				position: "absolute",
				"-webkit-transform": "rotate(" + C + "deg)",
				"-moz-transform": "rotate(" + C + "deg)",
				"-o-transform": "rotate(" + C + "deg)",
				"-ms-transform": "rotate(" + C + "deg)",
				transform: "rotate(" + C + "deg)"
			})) : 0 == M && console.log("the shape chosen on this object is invalid. Try 'circle', 'triangle', or 'square'"),
			$(c).animate({
				opacity: [o, "swing"],
				left: t + Math.random() * (n + n) - n,
				top: i + N,
				width: [0, "easeInQuart"],
				height: [0, "easeInQuart"]
			},
			A, "easeOutQuad",
			function() {
				document.body.removeChild(this)
			}),
			document.body.appendChild(c)
		}
	}
	var M = {
		colors: ["#2DE7F0", "#FA5C46"],
		num_sprites: 22,
		lifespan: 1e3,
		radius: 300,
		sprite_size: 10,
		shape: "circle",
		gravity: "false"
	},
	t = $.extend({},
	M, t);
	this.opts = t;
	var e = t.num_sprites,
	s = t.colors,
	n = t.radius,
	a = t.sprite_size,
	w = t.shape.toLowerCase(),
	g = t.gravity,
	I = t.image,
	A = t.lifespan,
	o = 100;
	this.FireAtElement = function(t) {
		if ("true" == g) var M = t.width() / 2 + t.offset().left,
		e = t.offset().top;
		else var M = t.width() / 2 + t.offset().left,
		e = t.offset().top + t.height() / 2;
		i(M, e)
	}
}
function SparkleList() {
	this.list = [],
	this.lifespan = 100,
	this.add = function(t) {
		t.opts.lifespan > this.lifespan && (this.lifespan = t.opts.lifespan),
		this.list.push(t)
	},
	this.fireAtCenter = function(t) {
		var i = new Overlay({
			tag: "div",
			id: "sparkle",
			noBackground: !0
		});
		i.show(),
		this.list.forEach(function(t) {
			this.lifespan += 200,
			setTimeout(function(t) {
				return function() {
					t.FireAtElement($("#sparkle"))
				}
			} (t), 200)
		}),
		setTimeout(function() {
			i.remove(),
			t()
		},
		this.lifespan)
	}
}
function divideUniformlyRandomly(t, i) {
	if (i < 2) return [t];
	for (var M = i - i % 2,
	e = new Array(M), s = t % M, n = (t -= s) / M, a = 0, w = 0; w < M / 2; w++) {
		var g = n - Math.round(Math.random() * n);
		g == n && (g = Math.round(n / 2)),
		e[w] = n - g,
		e[i - w - 1] = n + g,
		a += e[w] + e[i - w - 1]
	}
	var I = i % 2 == 1 ? Math.floor(i / 2) : e.indexOfMin(),
	A = t + s - a;
	return e[I] = (e[I] || 0) + A,
	e.shuffle()
}
function series(t, i, M) {
	function e(M) {
		t[M](function(t) {
			return t && i ? i(t) : M < n - 1 ? e(M + 1) : i && i()
		})
	}
	var s, n = t.length;
	if (!n && i) return nextTick(i, 1);
	M && (s = e, e = function(t) {
		nextTick(function() {
			s(t)
		},
		1)
	}),
	e(0)
}
function isFirefox() {
	return navigator.userAgent.toLowerCase().indexOf("firefox") > -1
}
function randomIntFromInterval(t, i) {
	return Math.floor(Math.random() * (i - t + 1) + t)
}
function scrollPos() {
	return {
		x: self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
		y: self.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
	}
}
function centerPos() {
	var t = void 0 != window.screenLeft ? window.screenLeft: screen.left,
	i = void 0 != window.screenTop ? window.screenTop: screen.top,
	M = window.innerWidth ? window.innerWidth: document.documentElement.clientWidth ? document.documentElement.clientWidth: screen.width,
	e = window.innerHeight ? window.innerHeight: document.documentElement.clientHeight ? document.documentElement.clientHeight: screen.height,
	s = s();
	return {
		x: M / 2 + t + s.x,
		y: e / 2 + i + s.y
	}
}
function Overlay(t) {
	this.id = t.id,
	this.tag = t.tag,
	this.show = function() {
		var i = scrollPos(),
		M = $("<" + this.tag + ' id="' + this.id + '">'),
		e = {
			width: t.width || "100%",
			height: t.height || "100%",
			position: "absolute",
			top: t.y || i.y,
			left: t.x || i.x,
			zIndex: 1e3
		};
		t.noBackground || (e.backgroundColor = "grey", e.opacity = .5, e.backgroundImage = "radial-gradient(farthest-side ellipse at right bottom , #900, black)"),
		M.css(e),
		$("body").append(M),
		M.on("scroll touchmove mousewheel",
		function(t) {
			return t.preventDefault(),
			t.stopPropagation(),
			!1
		})
	},
	this.remove = function() {
		$("#" + this.id).remove()
	}
}
var imgList = {
	bg: "resource/images/open.svg",
	"bomb-bg": "resource/images/bomb-bg.svg?v=3",
	"bomb-w-bg": "resource/images/bomb-w-bg.svg?v=3",
	bomb: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwwIHtmaWxsOiMxRjFBMTd9DQogICAgLmZpbDEge2ZpbGw6d2hpdGV9DQogICAgLmZpbDIge2ZpbGw6IzFGMUExNztmaWxsLXJ1bGU6bm9uemVyb30NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0i5Zu+5bGCX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8ZyBpZD0iXzExMjE0MDQ0MCI+DQogICA8Y2lyY2xlIGlkPSJfMTQ3NzkzNTkyIiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoMC42NjYxMTggMC41MjAxNzcgLTAuNTIwMTc3IDAuNjY2MTE4IDMuMjU3NzggNS4yMjY1KSIgcj0iMS45Njg1Ii8+DQogICA8cmVjdCBpZD0iXzE0NzAxOTUyOCIgY2xhc3M9ImZpbDAiIHRyYW5zZm9ybT0ibWF0cml4KDAuODQxODg3IDAuNjU3NDM3IC0wLjYxNzk0NSAwLjc5MTMxNiAzLjk3MTU4IDMuMzY0NjcpIiB3aWR0aD0iMS4wOTIyIiBoZWlnaHQ9IjAuNDU3MiIvPg0KICAgPHBhdGggaWQ9Il8xMTIxNDAxMDQiIGNsYXNzPSJmaWwxIiBkPSJNMy4zNzE2IDQuMTAzNGwwLjU4MTIgMC40NTM4IC0wLjAwMTkgMC4wMDI1Yy0wLjQyNTUsLTAuMjQxNiAtMC45NjExLC0wLjE3OTUgLTEuMzIwMSwwLjE1MDZsLTAuMDAzNiAtMC4wMDI4IDAuNDQyOCAtMC41NjdjMC4wNzI3LC0wLjA5MzIgMC4yMDg1LC0wLjEwOTkgMC4zMDE2LC0wLjAzNzF6Ii8+DQogICA8cGF0aCBpZD0iXzExNTQwOTk0NCIgY2xhc3M9ImZpbDIiIGQ9Ik00LjM2NDcgMy42NzE3bDAuMjQ2OCAtMC4zMTYgMC4wMDQxIC0wLjAwNDhjMC4wMDYzLC0wLjAwNjYgMC4yNzI0LC0wLjI5NDggMC41NjAyLC0wLjMzMDYgMC4wNDUxLDAuMDA3MyAwLjA3NSwwLjA0NzMgMC4wNzg2LDAuMDc1MSAwLjAwMTUsMC4wMTI3IDAuMDA2OCwwLjA1MzkgLTAuMDUzNCwwLjA4NjggLTAuMTkxLDAuMDIwOSAtMC40MjY2LDAuMjM5NiAtMC40NTc3LDAuMjc5M2wtMC4yNDUzIDAuMzE0MiAtMC4xMzMzIC0wLjEwNHoiLz4NCiAgIDxyZWN0IGlkPSJfMTUwODc0MjcyIiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoMC43ODg3NjcgMS4wMDg3NCAtMC45MTQxNTMgMC43MTQ4MDkgNC41NzQ1MyAyLjM4MjkpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xMTUxMzI3MjAiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMC4zMjUxODMgMS4yMzg1MyAtMS4xMjI0IC0wLjI5NDY5MyA1LjM4MDEyIDIuMjYzNzYpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xNTA4NzM4NjQiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMS4xMTkyIDAuNjIyMTY2IC0wLjU2MzgyOSAtMS4wMTQyNiA1LjkyMjc0IDIuNzM2NTEpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xNTEyNzQwMzIiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMS4yNDkwMSAtMC4yODIyNDggMC4yNTU3ODMgLTEuMTMxOSA1Ljk3ODkgMy4zODgyNykiIHdpZHRoPSIwLjQzMDEiIGhlaWdodD0iMC4wOTU4IiByeD0iMC4wNDc5IiByeT0iMC4wNDc5Ii8+DQogICA8cmVjdCBpZD0iXzg4Njg1NjQ4IiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoLTAuNzc3NTMgLTEuMDE3NDIgMC45MjIwMjUgLTAuNzA0NjI2IDUuNjEyMSAzLjg5NDcpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICA8L2c+DQogPC9nPg0KPC9zdmc+DQo=",
	"gift-bg": "resource/images/gift-bg.svg?v=3",
	"gift-w-bg": "resource/images/gift-w-bg.svg?v=3",
	gift: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5zdHIwIHtzdHJva2U6I0ZGRjUwMDtzdHJva2Utd2lkdGg6MC4yfQ0KICAgIC5maWwwIHtmaWxsOm5vbmV9DQogICBdXT4NCiAgPC9zdHlsZT4NCiA8L2RlZnM+DQogPGcgaWQ9IuWbvuWxgl94MDAyMF8xIj4NCiAgPG1ldGFkYXRhIGlkPSJDb3JlbENvcnBJRF8wQ29yZWwtTGF5ZXIiLz4NCiAgPGcgaWQ9Il8xNDc3OTM5MjgiPg0KICAgPHBhdGggaWQ9Il84ODY4NTY3MiIgY2xhc3M9ImZpbDAgc3RyMCIgZD0iTTMuNzI1MyAyLjUxNmwxLjM1NzYgMGMwLjA1MDYsMCAwLjA5MiwwLjAzNzQgMC4wOTIsMC4wODMxbDAgMC42NjU0YzAsMC4wNDU3IC0wLjA0MTQsMC4wODMxIC0wLjA5MiwwLjA4MzFsLTEuMzU3NiAwIDAgLTAuODMxNnoiLz4NCiAgIDxwYXRoIGlkPSJfMTUwODc0MDA4IiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMy43MjUzIDIuNTE2bC0xLjM1NzYgMGMtMC4wNTA2LDAgLTAuMDkyLDAuMDM3NCAtMC4wOTIsMC4wODMxbDAgMC42NjU0YzAsMC4wNDU3IDAuMDQxNCwwLjA4MzEgMC4wOTIsMC4wODMxbDEuMzU3NiAwIDAgLTAuODMxNnoiLz4NCiAgIDxwYXRoIGlkPSJfMTE1NDA5MzkyIiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMi41MzgzIDQuNTU5NWwxLjE4NyAwIDAgLTEuMjExNSAtMS4xODcgMGMtMC4wMzQ1LDAgLTAuMDYyNiwwLjAyNTIgLTAuMDYyNiwwLjA1NjRsMCAxLjA5ODdjMCwwLjAzMTIgMC4wMjgxLDAuMDU2NCAwLjA2MjYsMC4wNTY0eiIvPg0KICAgPHBhdGggaWQ9Il8xNTA4NzcxNzYiIGNsYXNzPSJmaWwwIHN0cjAiIGQ9Ik00LjkxMjMgNC41NTk1bC0xLjE4NyAwIDAgLTEuMjExNSAxLjE4NyAwYzAuMDM0NSwwIDAuMDYyNiwwLjAyNTIgMC4wNjI2LDAuMDU2NGwwIDEuMDk4N2MwLDAuMDMxMiAtMC4wMjgxLDAuMDU2NCAtMC4wNjI2LDAuMDU2NHoiLz4NCiAgIDxwYXRoIGlkPSJfMTQ3Nzk0MDk2IiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMy43MDkzIDIuNTE2MWMwLjI1NiwtMC4zMTk5IDAuNTA1NiwtMC41NTQ0IDAuNzU4OCwtMC41NjMyIDAuMjUyMiwtMC4wNDE4IDAuNTg4NywwLjI0NjcgMC4yNTQ3LDAuNTYzMiIvPg0KICAgPHBhdGggaWQ9Il8xNTA4Nzc0ODgiIGNsYXNzPSJmaWwwIHN0cjAiIGQ9Ik0zLjcwOTMgMi41MTYxYy0wLjI1NiwtMC4zMTk5IC0wLjUwNTUsLTAuNTU0NCAtMC43NTg3LC0wLjU2MzIgLTAuMjUyMiwtMC4wNDE4IC0wLjU4ODcsMC4yNDY3IC0wLjI1NDcsMC41NjMyIi8+DQogIDwvZz4NCiA8L2c+DQo8L3N2Zz4NCg==",
	"my-bg": "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KIDwhLS0gQ3JlYXRlZCB3aXRoIFNWRy1lZGl0IC0gaHR0cDovL3N2Zy1lZGl0Lmdvb2dsZWNvZGUuY29tLyAtLT4KIDxkZWZzPgogIDxjbGlwUGF0aCBpZD0iY3AiPgogICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgcnk9IjE1IiByeD0iMTUiIGlkPSJzdmdfNCIvPgogIDwvY2xpcFBhdGg+CiA8L2RlZnM+CiA8Zz4KICA8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+CiAgPGcgaWQ9InN2Z18zIj4KICAgPHJlY3Qgcnk9IjE1IiByeD0iMTUiIGZpbGw9IiNkNzA5MTUiIHN0cm9rZT0iIzAwMDAwMCIgc3Ryb2tlLXdpZHRoPSIwIiB4PSIwIiB5PSIwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjIwMCIgaWQ9InN2Z18xIi8+CiAgIDxjaXJjbGUgY2xpcC1wYXRoPSJ1cmwoI2NwKSIgaWQ9InN2Z18yIiByPSIxMzcuMjk1MyIgY3k9Ii01MCIgY3g9Ijc2IiBzdHJva2UtbGluZWNhcD0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtZGFzaGFycmF5PSJudWxsIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMDAwMCIgZmlsbD0iI2ZjMGQxYiIvPgogIDwvZz4KICA8ZyBpZD0ic3ZnXzciPgogICA8Y2lyY2xlIGZpbGw9IiNmZmZmMDAiIHN0cm9rZS13aWR0aD0iMCIgc3Ryb2tlLWRhc2hhcnJheT0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtbGluZWNhcD0ibnVsbCIgY3g9Ijc4LjUiIGN5PSI4NS41IiByPSIzMCIgaWQ9InN2Z181IiBzdHJva2U9IiMwMDAwMDAiLz4KICAgPHRleHQgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9InNlcmlmIiBmb250LXNpemU9IjIwIiBpZD0ic3ZnXzYiIHk9IjkzLjUiIHg9Ijc5IiBzdHJva2UtbGluZWNhcD0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtZGFzaGFycmF5PSJudWxsIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMDAwMCIgZmlsbD0iIzAwMDAwMCI+T1BFTjwvdGV4dD4KICA8L2c+CiA8L2c+Cjwvc3ZnPg==",
	open: "resource/images/open.svg"
}; !
function(t) {
	var i = function() {
		var t, i = document.createElement("fakeelement"),
		M = {
			transition: "transitionend",
			OTransition: "oTransitionEnd",
			MozTransition: "transitionend",
			WebkitTransition: "webkitTransitionEnd"
		};
		for (t in M) if (void 0 !== i.style[t]) return M[t]
	},
	M = function(i, M, e) {
		this.setting = {
			axis: "y",
			reverse: !1,
			trigger: "click",
			speed: 500,
			forceHeight: !1,
			forceWidth: !1,
			autoSize: !0,
			front: ".front",
			back: ".back"
		},
		this.setting = t.extend(this.setting, M),
		"string" != typeof M.axis || "x" !== M.axis.toLowerCase() && "y" !== M.axis.toLowerCase() || (this.setting.axis = M.axis.toLowerCase()),
		"boolean" == typeof M.reverse && (this.setting.reverse = M.reverse),
		"string" == typeof M.trigger && (this.setting.trigger = M.trigger.toLowerCase());
		var s = parseInt(M.speed);
		isNaN(s) || (this.setting.speed = s),
		"boolean" == typeof M.forceHeight && (this.setting.forceHeight = M.forceHeight),
		"boolean" == typeof M.forceWidth && (this.setting.forceWidth = M.forceWidth),
		"boolean" == typeof M.autoSize && (this.setting.autoSize = M.autoSize),
		("string" == typeof M.front || M.front instanceof t) && (this.setting.front = M.front),
		("string" == typeof M.back || M.back instanceof t) && (this.setting.back = M.back),
		this.element = i,
		this.frontElement = this.getFrontElement(),
		this.backElement = this.getBackElement(),
		this.isFlipped = !1,
		this.init(e)
	};
	t.extend(M.prototype, {
		flipDone: function(t) {
			var M = this;
			M.element.one(i(),
			function() {
				M.element.trigger("flip:done"),
				"function" == typeof t && t.call(M.element)
			})
		},
		flip: function(t) {
			if (!this.isFlipped) {
				this.isFlipped = !0;
				var i = "rotate" + this.setting.axis;
				this.frontElement.css({
					transform: i + (this.setting.reverse ? "(-180deg)": "(180deg)"),
					"z-index": "0"
				}),
				this.backElement.css({
					transform: i + "(0deg)",
					"z-index": "1"
				}),
				this.flipDone(t)
			}
		},
		unflip: function(t) {
			if (this.isFlipped) {
				this.isFlipped = !1;
				var i = "rotate" + this.setting.axis;
				this.frontElement.css({
					transform: i + "(0deg)",
					"z-index": "1"
				}),
				this.backElement.css({
					transform: i + (this.setting.reverse ? "(180deg)": "(-180deg)"),
					"z-index": "0"
				}),
				this.flipDone(t)
			}
		},
		getFrontElement: function() {
			return this.setting.front instanceof t ? this.setting.front: this.element.find(this.setting.front)
		},
		getBackElement: function() {
			return this.setting.back instanceof t ? this.setting.back: this.element.find(this.setting.back)
		},
		init: function(t) {
			var i = this,
			M = i.frontElement.add(i.backElement),
			e = "rotate" + i.setting.axis,
			s = {
				perspective: 2 * i.element["outer" + ("rotatex" === e ? "Height": "Width")](),
				position: "relative"
			},
			n = {
				transform: e + "(" + (i.setting.reverse ? "180deg": "-180deg") + ")",
				"z-index": "0",
				position: "relative"
			},
			a = {
				"backface-visibility": "hidden",
				"transform-style": "preserve-3d",
				position: "absolute",
				"z-index": "1"
			};
			i.setting.forceHeight ? M.outerHeight(i.element.height()) : i.setting.autoSize && (a.height = "100%"),
			i.setting.forceWidth ? M.outerWidth(i.element.width()) : i.setting.autoSize && (a.width = "100%"),
			(window.chrome || window.Intl && Intl.v8BreakIterator) && "CSS" in window && (s["-webkit-transform-style"] = "preserve-3d"),
			M.css(a).find("*").css({
				"backface-visibility": "hidden"
			}),
			i.element.css(s),
			i.backElement.css(n),
			setTimeout(function() {
				var e = i.setting.speed / 1e3 || .5;
				M.css({
					transition: "all " + e + "s ease-out"
				}),
				"function" == typeof t && t.call(i.element)
			},
			20),
			i.attachEvents()
		},
		clickHandler: function(i) {
			i || (i = window.event),
			this.element.find(t(i.target).closest('button, a, input[type="submit"]')).length || (this.isFlipped ? this.unflip() : this.flip())
		},
		hoverHandler: function() {
			var i = this;
			i.element.off("mouseleave.flip"),
			i.flip(),
			setTimeout(function() {
				i.element.on("mouseleave.flip", t.proxy(i.unflip, i)),
				i.element.is(":hover") || i.unflip()
			},
			i.setting.speed + 150)
		},
		attachEvents: function() {
			var i = this;
			"click" === i.setting.trigger ? i.element.on(t.fn.tap ? "tap.flip": "click.flip", t.proxy(i.clickHandler, i)) : "hover" === i.setting.trigger && (i.element.on("mouseenter.flip", t.proxy(i.hoverHandler, i)), i.element.on("mouseleave.flip", t.proxy(i.unflip, i)))
		},
		flipChanged: function(t) {
			this.element.trigger("flip:change"),
			"function" == typeof t && t.call(this.element)
		},
		changeSettings: function(t, i) {
			var M = this,
			e = !1;
			if (void 0 !== t.axis && M.setting.axis !== t.axis.toLowerCase() && (M.setting.axis = t.axis.toLowerCase(), e = !0), void 0 !== t.reverse && M.setting.reverse !== t.reverse && (M.setting.reverse = t.reverse, e = !0), e) {
				var s = M.frontElement.add(M.backElement),
				n = s.css(["transition-property", "transition-timing-function", "transition-duration", "transition-delay"]);
				s.css({
					transition: "none"
				});
				var a = "rotate" + M.setting.axis;
				M.isFlipped ? M.frontElement.css({
					transform: a + (M.setting.reverse ? "(-180deg)": "(180deg)"),
					"z-index": "0"
				}) : M.backElement.css({
					transform: a + (M.setting.reverse ? "(180deg)": "(-180deg)"),
					"z-index": "0"
				}),
				setTimeout(function() {
					s.css(n),
					M.flipChanged(i)
				},
				0)
			} else M.flipChanged(i)
		}
	}),
	t.fn.flip = function(i, e) {
		return "function" == typeof i && (e = i),
		"string" == typeof i || "boolean" == typeof i ? this.each(function() {
			var M = t(this).data("flip-model");
			"toggle" === i && (i = !M.isFlipped),
			i ? M.flip(e) : M.unflip(e)
		}) : this.each(function() {
			if (t(this).data("flip-model")) {
				var s = t(this).data("flip-model"); ! i || void 0 === i.axis && void 0 === i.reverse || s.changeSettings(i, e)
			} else t(this).data("flip-model", new M(t(this), i || {},
			e))
		}),
		this
	}
} (jQuery),
function() {
	var odds = [];
	initOdds();
	resetOdds();

	$('#coustomBtn').click(function(){
		if($('#btnBegin').hasClass('disabled')) return;
		$('.game-setting-wrap').show();
		window.Logger.log('coustomBtn', 'click');
	});
	$('.close-setting').click(function(){
		$('.game-setting-wrap').hide();
		window.Logger.log('close-setting', 'click');
	});
	$('.btn-link').click(function(){
		$('.start-page').hide();
		$('.game-page').show();
	});

	$('.bet-number span').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		if($('#btnBegin').hasClass('disabled')) return;
		var _betAmount = parseInt($('#betAmount').val()) || 0, _num = parseInt($(this).attr('value')), min_bet = parseInt($('#min_bet').val()), max_bet = parseInt($('#max_bet').val());
		var _count = _betAmount + _num;
		if(_count > max_bet){
			_count = max_bet;
			$('.error-tip-wrap').show();
			var str1 = _bet_range.replace('xx',min_bet);
			$('#tipMsg').html( str1.replace('ss',max_bet));
			hideErrorTip();
		}
		var _odd = $('#odds').val(), _odd_str = _count ? accMul(parseFloat(_odd),parseInt(_count)) : _odd + '*?', _odd_val = _count ? formatNum(_odd_str) : _odd_str;
		$('#spnOdds').text(_odd_val);
		$('#betAmount').val(_count);
		$('#spnAmount').html(formatNum(_count));
		$('#spnTotal').text('0.00');
		window.Logger.log('num-list','click',_num.toString(), _betAmount.toString());
	});

	$('input[name=gameTypeRadios]').click(function(){
		var _gt = parseInt($(this).val()) || 9, _pcount = parseInt($("input[name=redPacketRadios9]:checked").val()),_bcount;

		if(_gt === 9){
			$('#redPacketRedios9').show();
			$('#redPacketRedios16').hide();
			var _len9 = $("input[name=redPacketRadios9]:checked").length;
			if(_len9 == 0){
				$("input[name=redPacketRadios9]").eq(0).prop('checked',true);
			}
			_pcount = parseInt($("input[name=redPacketRadios9]:checked").val())
			_bcount = _gt - _pcount;
		}else{
			$('#redPacketRedios9').hide();
			$('#redPacketRedios16').show();
			_len16 = $("input[name=redPacketRadios16]:checked").length;
			if(_len16 == 0){
				$("input[name=redPacketRadios16]").eq(0).prop('checked',true);
			}
			_pcount = parseInt($("input[name=redPacketRadios16]:checked").val())
			_bcount = _gt - _pcount;
		}
		$('#bombCount').val(_bcount);
		window.Logger.log('gameTypeRadios', 'change', _gt);
		resetOdds();
	});

	$('input[class=redPacketRadios]').click(function(){
		var _gt = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9, _pcount = parseInt($(this).val()) || 1;
		var _bcount = _gt - _pcount;
		$('#bombCount').val(_bcount);
		window.Logger.log('redPacketRadios', 'change', _pcount);
		resetOdds();
	});

	function hideErrorTip(){
		setTimeout(function(){
			$('.error-tip-wrap').hide();
		},2000);
	}

	function resetOdds() {
		var _type = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9,
				ck_9 = parseInt($("input[name=redPacketRadios9]:checked").val()),
				ck_16 = parseInt($("input[name=redPacketRadios16]:checked").val()),
				_count = _type == 9 ? ck_9 : ck_16, _odd = odds[_type][_count],
				_bet_amount = parseFloat($('#betAmount').val()),
				_odd_str = _bet_amount > 0 ? accMul(parseFloat(_odd),parseInt(_bet_amount)) : _odd + '*?';
				_odd_val = _bet_amount > 0 ? formatNum(_odd_str) : _odd_str;
		$('#spnOdds').text(_odd_val);
		$('#odds').val(_odd);
	}

	$('#saveBtn').click(function(){
		var _type = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9,
				ck_9 = parseInt($("input[name=redPacketRadios9]:checked").val()),
				ck_16 = parseInt($("input[name=redPacketRadios16]:checked").val()),
				_count = _type == 9 ? ck_9 : ck_16, _odd = odds[_type][_count],
				_pcount = $('#bombCount').val();

		$('#bombCount').val(_pcount);
		$('#packetCount').text(_count);
		$('#spnBombCount').html(_pcount);
		$('#allPacketCount').text(_count);
		$('#spnAmount').text("0.00");
		$('#spnTotal').text("0.00");
		$('#betAmount').val("0");
		resetOdds();

		$('.game-setting-wrap').hide();
		$('.btn-list .left-btn').removeClass('active');
		$('#coustomBtn').addClass('active');

		window.Logger.log('saveBtn', 'click');
	});

	$('.close-over-tip').click(function(){
		$('.game-over-wrap').hide();
		window.Logger.log('close-over-tip', 'click');
		//$('#btnBegin').removeClass('disabled');
	});

	$('.o-easy').click(function(){
		overInitGameData(9, 8, $('#defalutAmount').val());
	});
	$('.o-normal').click(function(){
		overInitGameData(16, 8, $('#defalutAmount').val());
	});
	$('.o-advance').click(function(){
		overInitGameData(9, 1, $('#defalutAmount').val());
	});
	$('.o-custom').click(function(){
		var _gt = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9,
				ck_9 = parseInt($("input[name=redPacketRadios9]:checked").val()),
				ck_16 = parseInt($("input[name=redPacketRadios16]:checked").val()),
				_pcount = _gt == 9 ? ck_9 : ck_16,
				 _amount = $.trim($('#betAmount').val()) || 5;
		overInitGameData(_gt, _pcount, _amount);
		$('.game-setting-wrap').show();
	});
	$('#playAgainBtn').click(function(){
		var _gt = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9,
				ck_9 = parseInt($("input[name=redPacketRadios9]:checked").val()),
				ck_16 = parseInt($("input[name=redPacketRadios16]:checked").val()),
				_pcount = _gt == 9 ? ck_9 : ck_16,
				_amount = $.trim($('#betAmount').val()) || '';
		overInitGameData(_gt, _pcount, _amount);
		$("#btnBegin").click();
	});

	$('#btnClear').click(function(){
		if($('#btnBegin').hasClass('disabled')) return;
		var _v = $('#betAmount').val();
		$('#betAmount').val('');
		$('#spnAmount').text('0.00');
		$('#spnOdds').text($('#odds').val() + '*?');
		$('.bet-number span').removeClass('active');
		window.Logger.log('btnClear', 'click', _v);
	});

	$('#closeTip').click(function(){
		$('.error-tip-wrap').hide();
		window.Logger.log('closeTip', 'click');
	});

	function overInitGameData(game_type, pack_count, bet_amount){
		$('#defalutPacketGrid').attr('class','default-packet-grid grid-' + game_type);
		$('#defalutPacketGrid').html('');
		var _tmp_span = '<span class="packet-item"><img src="resource/images/open.svg" alt=""></span>', _hide = game_type == 9 ? 16 : 9;
		for (var i = 0; i < game_type; i++) {
			$('#defalutPacketGrid').append(_tmp_span);
		}
		//$('.game-over-wrap').hide(),
		$('.game-over-tip').hide(),
		$('.progress').show(),
		//$('#btnBegin').removeClass('disabled'),
		$("#canvas").hide(),
		$("#table").empty().hide(),
		$('#defalutPacketGrid').show();
		$('#gameType' + game_type).prop('checked', true);
		$('#redPacket' + game_type + pack_count).prop('checked', true);
		$('#betAmount').val(bet_amount);
		$('#spnAmount').text(formatNum(bet_amount));
		$('.bet-number span').removeClass('active');
		$('.bet-number span').eq(0).addClass('active'),
		$('#redPacketRedios'+game_type).show(),
		$('#redPacketRedios'+_hide).hide();
	}

	$('.left-btn-easy').click(function(){
		if($('#btnBegin').hasClass('disabled')) return;
		$('.btn-list .left-btn').removeClass('active');
		$(this).addClass('active');
		window.Logger.log('left-btn-easy','click');
		btnResetBegin(9,8);
	});
	$('.left-btn-normal').click(function(){
		if($('#btnBegin').hasClass('disabled')) return;
		$('.btn-list .left-btn').removeClass('active');
		$(this).addClass('active');
		window.Logger.log('left-btn-normal','click');
		btnResetBegin(16,8);
	});
	$('.left-btn-advance').click(function(){
		if($('#btnBegin').hasClass('disabled')) return;
		$('.btn-list .left-btn').removeClass('active');
		$(this).addClass('active');
		window.Logger.log('left-btn-advance','click');
		btnResetBegin(9,1);
	});

	function btnResetBegin(game_type, red_packet){
		if($('#btnBegin').hasClass('disabled')) return;
		$('#gameType' + game_type).prop('checked',true);
		_hide_div = game_type == 9 ? 16 : 9;
		$('#redPacketRedios' + game_type).show();
		$('#redPacketRedios' + _hide_div).hide();
		$('#redPacket' + game_type + red_packet).prop('checked',true);
		var _pcount = parseInt(game_type) - parseInt(red_packet);
		$('#bombCount').val(_pcount);
		$('#packetCount').html(red_packet);
		$('#spnBombCount').html(_pcount);
		$('#allPacketCount').text(red_packet);
		$('#spnAmount').text("0.00");
		$('#spnTotal').text("0.00");
		$('#betAmount').val("0");
		resetOdds();
	}


	function initData(){
		var _data = ajaxOpenGame();
		$('#spnAmount').text(formatNum(_data.bet_amount));
		$('#spnTotal').text(formatNum(_data.won_amount));
		$('#spnOdds').text(formatNum(accMul(parseFloat(_data.odds),parseInt(_data.bet_amount))));
		$('#odds').val(_data.odds);
		$('#packetCount').text(_data.red_packet_count);
		$('#allPacketCount').text(_data.red_packet_count);
		$('#spnBombCount').text(_data.bomb_count);
	}

	function initOdds(){
		var _odds = ARR_ODDS, _odds = eval("("+_odds+")"),len = _odds.length, type_9 = [], type_16 = [], new_odds = [];
		for (var i = 0; i < len; i++) {
			if(_odds[i]['game_type'] == 9){
					type_9[_odds[i]['red_packet_count']] = _odds[i]['odds'];
			}else{
					type_16[_odds[i]['red_packet_count']] = _odds[i]['odds'];
			}
		}
		new_odds[9] = type_9;
		new_odds[16] = type_16;
		odds = new_odds;
	}

	function ajaxOpenGame(){
		var res, _type = parseInt($(":radio[name=gameTypeRadios]:checked").val()) || 9,
				ck_9 = parseInt($("input[name=redPacketRadios9]:checked").val()),
				ck_16 = parseInt($("input[name=redPacketRadios16]:checked").val()),
				_count = _type == 9 ? ck_9 : ck_16,
				_amount = $.trim($('#betAmount').val()) || $('#defalutAmount').val();
		$.ajax({
			url: API_SITE_URL + '/index.php?act=game&op=start',
			type: 'get',
			async: false,
			data: {game_type: _type,red_packet_count: _count, bet_amount: _amount },
			dataType: 'json',
			success: function(ret){
				if(ret.CODE == 200){
					res  = ret.DATA;
				}else{
					$('.error-tip-wrap').show();
					var msg = CODE_ARR[ret.CODE] ? CODE_ARR[ret.CODE] : MSG_SYSYTEM_ERROR;
					$('#tipMsg').html(msg);
					hideErrorTip();
				}
			}
		});
		return res;
	}

	function formatNum(num) {
		num = parseFloat(num);
		return (num.toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
	}

	function t() {
		var _ajaxData = ajaxOpenGame();
		var t = {
			ok: !0
		},
		i = parseInt(_ajaxData.game_type) || 9;
		t.grid = i;
		t.data = _ajaxData;
		var M = $("#betAmount"),
		s = parseInt(_ajaxData.bet_amount),
		n = parseInt(_ajaxData.red_packet_count);
		return isNaN(s) ? (t.ok = !1) :(t.betAmount = s),
		      isNaN(n) || n < 1 || n >= i ? (t.ok = !1) : (t.redPacketCount = n),
		t
	}
	function i() {
		$('.default-packet-grid').hide();
		e.grid == 9 ? $('#table').attr('class', 'container-fluid t-grid-9') : $('#table').attr('class', 'container-fluid t-grid-16');
		sound.stopAll(),
		M = new Game(e.betAmount, e.grid, e.redPacketCount), _data = e.data,
		$("#canvas").show(),
		$("#table").empty().hide(),
		$("#betSetting").hide(),
		$("#playGame").show(),
		$('#game_order_id').val(_data.game_order_id);
		$("#spnAmount").text(formatNum(e.betAmount)),
		$("#spnOdds").text(formatNum(accMul(parseFloat(_data.odds),parseInt(e.betAmount)))),
		$("#spnTotal").text(formatNum(_data.won_amount)),
		$("#packetCount").text(_data.red_packet_count),
		$("#spnBombCount").text(_data.bomb_count),
		$(".progress span").attr("aria-valuenow", "0").css("width", "0").text("0%"),
		location.hash = "#btnAbort"
	}
	window.sound = new Sound;
	var M, e;
	$(function() {
		$("#btnBegin").click(function() {
			if($(this).hasClass('disabled')) return;
			G_OVER = false;
			var s = parseInt($('#betAmount').val()), min_bet = parseInt($('#min_bet').val()), max_bet = parseInt($('#max_bet').val());
			if(isNaN(s)){
				$('.error-tip-wrap').show();
				$('#tipMsg').html(_tip_reenter_bet_amount);
				hideErrorTip();
				return;
			}

			if(s < min_bet) {
				$('.error-tip-wrap').show();
				var str1 = _bet_range.replace('xx',min_bet);
				$('#tipMsg').html( str1.replace('ss',max_bet));
				hideErrorTip();
				return;
			}

			if(s > max_bet) {
				$('.error-tip-wrap').show();
				var str1 = _bet_range.replace('xx',min_bet);
				$('#tipMsg').html(str1.replace('ss',max_bet));
				hideErrorTip();
				return;
			}

       ! 0 === (e = t()).ok && i();
			 if(window.parent.GameCommonUpdateMemberData){
	 			window.parent.GameCommonUpdateMemberData();
	 			}
				$('.game-over-tip').hide();
				$('.progress').show();
			 $(this).addClass('disabled');
				window.Logger.log('btnBegin', 'click');
		}),
		$("#btnAgain").click(function() {
			$("#btnAgain").hide(),
			i()
		}),
		$("#btnAbort").click(function() {
			location.hash = "",
			$("#betSetting").show(),
			$("#playGame").hide(),
			$("#btnAgain").hide()
		})
	})
} (),
RedPacket.prototype.toString = function() {
	return this.position + ""
},
Array.prototype.distinct = function() {
	for (var t = {},
	i = [], M = 0, e = this.length; M < e; M++) void 0 === t[this[M].toString()] && i.push(this[M]),
	t[this[M].toString()] = 0;
	return i
},
Array.prototype.sum = function() {
	for (var t = 0,
	i = 0,
	M = this.length; i < M; i++) t += this[i];
	return t
},
Array.prototype.indexOfMax = function() {
	if (0 === this.length) return - 1;
	for (var t = this[0], i = 0, M = 1; M < this.length; M++) this[M] > t && (i = M, t = this[M]);
	return i
},
Array.prototype.indexOfMin = function() {
	if (0 === this.length) return - 1;
	for (var t = this[0], i = 0, M = 1; M < this.length; M++) this[M] < t && (i = M, t = this[M]);
	return i
},
Array.prototype.shuffle = function() {
	for (var t, i, M = this.length; M; t = parseInt(Math.random() * M), i = this[--M], this[M] = this[t], this[t] = i);
	return this
};

if(UNFINISH != 'null'){
	initUnfinishGame();
}
function initUnfinishGame(){
	var _unfinishGame = UNFINISH, _unfinishGame = eval("("+_unfinishGame+")"),
			_game_type = _unfinishGame.game_type,
			_red_count = parseInt(_unfinishGame.game_type) - parseInt(_unfinishGame.bomb_count),
			_hide_div = _game_type == 9 ? 16 : 9;
			$('#btnBegin').addClass('disabled')
			var _data = {};
			_data.bet_amount = _unfinishGame.bet_amount;
			_data.bomb_count = _unfinishGame.bomb_count;
			_data.game_order_id = _unfinishGame.game_order_id;
			_data.game_type = _game_type;
			_data.grids = null;
			_data.odds = _unfinishGame.odds;
			_data.red_packet_count = _red_count;
			_data.won_amount = _unfinishGame.won_amount;
			var t = {
				ok: !0
			},
			i = _game_type || 9;
			t.grid = i;
			t.data = _data;
			t.betAmount = _unfinishGame.bet_amount;
			t.redPacketCount = _red_count;
			e = t;
			//M = new Game(parseFloat(_unfinishGame.bet_amount), parseInt(_unfinishGame.game_type), red_count),
			M = new Game(parseFloat(e.betAmount), parseInt(e.grid), e.redPacketCount, _unfinishGame),
			$("#canvas").show(),
			$("#table").empty().hide(),
			$("#betSetting").hide(),
			$('#defalutPacketGrid').hide(),
			$("#playGame").show();
			var w = (_red_count - _unfinishGame.red_packet_count) / _red_count,
			i = Math.round(100 * w),
			p = i + "%";
			$(".progress span").attr("aria-valuenow", "0").css("width", p).text(p);
	// M = new Game(parseFloat(_unfinishGame.bet_amount), parseInt(_unfinishGame.game_type), red_count),
	$("#canvas").show(),
	$("#table").empty().hide(),
	$("#betSetting").hide(),
	$("#playGame").show();
	//数据初始化
	$('#game_order_id').val(_unfinishGame.game_order_id);
	$('#spnAmount').text(formatNum(_unfinishGame.bet_amount));
	$('#betAmount').val(parseFloat(_unfinishGame.bet_amount));
	$('#spnTotal').text(formatNum(_unfinishGame.won_amount));
	$('#spnOdds').text(formatNum(accMul(parseFloat(_unfinishGame.odds),parseInt(_unfinishGame.bet_amount))));
	$('#odds').val(_unfinishGame.odds);
	$('#packetCount').text(_unfinishGame.red_packet_count);
	$('#allPacketCount').text(_unfinishGame.game_type - _unfinishGame.bomb_count);
	$('#spnBombCount').text(_unfinishGame.bomb_count);
	$('#bombCount').val(_unfinishGame.bomb_count);
	$('#gameType' + _game_type).prop('checked', true);
	$('#redPacketRedios' + _game_type).show();
	$('#redPacketRedios' + _hide_div).hide();
	$('#redPacket' +_unfinishGame. game_type + _red_count).prop('checked', true);
}
function accMul(arg1, arg2){
	var m=0,s1=arg1.toString(),s2=arg2.toString();
	try{m+=s1.split(".")[1].length}catch(e){}
	try{m+=s2.split(".")[1].length}catch(e){}
	return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
}
function formatNum(num) {
	num = parseFloat(num);
	return (num.toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
}
var nextTick = "undefined" != typeof process ? process.nextTick: "undefined" != typeof setImmediate ? setImmediate: setTimeout; !
function() {
	for (var t = 0,
	i = ["ms", "moz", "webkit", "o"], M = 0; M < i.length && !window.requestAnimationFrame; ++M) window.requestAnimationFrame = window[i[M] + "RequestAnimationFrame"],
	window.cancelAnimationFrame = window[i[M] + "CancelAnimationFrame"] || window[i[M] + "CancelRequestAnimationFrame"];
	window.requestAnimationFrame || (window.requestAnimationFrame = function(i, M) {
		var e = (new Date).getTime(),
		s = Math.max(0, 16 - (e - t)),
		n = window.setTimeout(function() {
			i(e + s)
		},
		s);
		return t = e + s,
		n
	}),
	window.cancelAnimationFrame || (window.cancelAnimationFrame = function(t) {
		clearTimeout(t)
	})
} (),
Snap.plugin(function(t, i, M, e) {
	i.prototype.flip = function() {
		this.animate({
			transform: "r360,150,150"
		},
		1e3, mina.bounce)
	}
});
