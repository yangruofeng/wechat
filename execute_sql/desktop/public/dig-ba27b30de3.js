function Animation(t) {
	this.game = t,
	this.shufflePath = t.canvas.svg.paper.path({
		d: "m182,496c-23,-26 -58,-200 -33,-246c24,-45 96,-72 122,-7c26,64 16,130 -16,196c-32,66 -50,82.522171 -73,56z",
		stroke: "none",
		strokeOpacity: 0,
		fill: "none"
	}),
	this.shufflePathLen = Snap.path.getTotalLength(this.shufflePath),
	this.createCardDispatchAnim = function(t, i, M) {
		var e = this.game.setting.gridSize;
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
			300, mina.easeout(),
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
						1500, mina.easeout(),
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
		I = s.image(imgList["bomb-w-bg"], 0, 0, a, w).attr({
			x: (n - a) / 2,
			y: .05 * n
		}),
		this.bgBomb = I.toPattern(0, 0, 1, 1),
		this.bgBomb.attr(g),
		I = s.image(imgList["gift-w-bg"], 0, 0, a, w).attr({
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
		L.push(r.createCardShuffleAnim(D)),
		t.selectAll("g.packet").forEach(function(t, i) {
			L.push(function(t, i) {
				return r.createCardDispatchAnim(t, i)
			} (t, i))
		}),
		L.push(this.renderGrid.bind(this)),
		series(L)
	},
	this.renderGrid = function(i) {
		console.log("anim finished!"),
		$("#canvas").hide();
		for (var M = this.game.setting.borderSize,
		e = Math.floor(12 / M), s = 0; s < M; s++) {
			for (var n = $('<div class="row"></div>'), a = 0; a < M; a++) {
				var w = t.getGridData(s, a),
				g = $('<div class="cell"></div>'),
				I = [];
				I.push("col-xs-" + e),
				I.push("col-sm-" + e),
				I.push("col-md-" + e),
				I.push("col-lg-" + e),
				5 == M && 0 == a && (I.push("col-xs-offset-1"), I.push("col-sm-offset-1"), I.push("col-md-offset-1"), I.push("col-lg-offset-1")),
				g.addClass(I.join(" "));
				var A = $('<div class="front"><img src="' + imgList.bg + '" /><div></div></div>'),
				o = $('<div class="back"><img src="' + imgList["gift-w-bg"] + '" /><div></div></div>'),
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
function Game(t, i, M) {
	this.eventHub = $({}),
	this.define = {
		canvasSize: 500,
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
	this.redPacketData = [],
	this.gridData = [],
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
		this.canvas.showAllGrid()
	}.bind(this)),
	this.eventHub.on("good-game",
	function() {
		this.canvas.showAllGrid()
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
		if (!0 !== this.isDigged) {
			Math.sqrt(M.setting.grid);
			if (this.isRedPacket) {
				M.data.remain--,
				M.data.digged++,
				M.data.point += this.point,
				$("#spnRPDigged").text(M.data.digged),
				$("#spnRPRemaining").text(M.data.remain),
				$("#spnRPPoint").text(M.data.point);
				var t = M.data.digged / M.setting.count,
				i = Math.round(100 * t),
				e = i + "%";
				$(".progress .progress-bar").attr("aria-valuenow", i).css("width", e).text(e),
				0 == M.data.remain ? ($("#btnAbort").hide(), $(".alert.alert-success").show(), M.gameOver(), $("#btnAgain").show(), sound.winning.play(), M.eventHub.trigger("good-game")) : sound.lucky.play()
			} else sound.bomb.play(),
			$(".alert.alert-danger").show(),
			$("#btnAbort").hide(),
			M.gameOver(),
			$("#btnAgain").show(),
			M.eventHub.trigger("game-over");
			this.isDigged = !0,
			this.show()
		}
	},
	this.show = function() {
		this.isRedPacket ? (this.dom.find(".front>div,.back>div").text(this.point), this.dom.flip(!0)) : this.dom.find("img").attr("src", imgList["bomb-w-bg"])
	}
}
function RedPacket(t, i) {
	this.point = t,
	this.position = i
}
function Sound() {
	this.bomb = new Howl({
		src: ["assets/sound/firebomb_expl1.mp3"]
	}),
	this.winning = new Howl({
		src: ["assets/sound/winning.mp3"]
	}),
	this.lucky = new Howl({
		src: ["assets/sound/good-result.mp3"]
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
	bg: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI3LjQ1MDZtbSIgaGVpZ2h0PSI5LjMxMzJtbSIgc3R5bGU9InNoYXBlLXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IHRleHQtcmVuZGVyaW5nOmdlb21ldHJpY1ByZWNpc2lvbjsgaW1hZ2UtcmVuZGVyaW5nOm9wdGltaXplUXVhbGl0eTsgZmlsbC1ydWxlOmV2ZW5vZGQ7IGNsaXAtcnVsZTpldmVub2RkIgp2aWV3Qm94PSIwIDAgNy40NTA2IDkuMzEzMiIKIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KIDxkZWZzPgogIDxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+CiAgIDwhW0NEQVRBWwogICAgLmZpbDAge2ZpbGw6I0I1MjgyMn0KICAgIC5maWwxIHtmaWxsOiNEQjMyMjZ9CiAgICAuZmlsMiB7ZmlsbDojRkZGNTAwfQogICAgLmZpbDMge2ZpbGw6IzFGMUExNztmaWxsLXJ1bGU6bm9uemVyb30KICAgXV0+CiAgPC9zdHlsZT4KIDwvZGVmcz4KIDxnPgogIDxtZXRhZGF0YSBpZD0iQ29yZWxDb3JwSURfMENvcmVsLUxheWVyIi8+CiAgPHBhdGggY2xhc3M9ImZpbDAiIGQ9Ik0wLjgyOTcgMGw1LjgyNSAwYzAuNDAwNiwwIDAuNzI4MiwwLjI0MTggMC43MjgyLDAuNTM3NmwwIDguMjM4YzAsMC4yOTU4IC0wLjMyNzYsMC41Mzc2IC0wLjcyODIsMC41Mzc2bC01LjgyNSAwYy0wLjQwMDYsMCAtMC43MjgyLC0wLjI0MTggLTAuNzI4MiwtMC41Mzc2bDAgLTguMjM4YzAsLTAuMjk1OCAwLjMyNzYsLTAuNTM3NiAwLjcyODIsLTAuNTM3NnoiLz4KICA8cGF0aCBjbGFzcz0iZmlsMSIgZD0iTTAuODI5NyAwbDUuODI1IDBjMC40MDA2LDAgMC43MjgyLDAuMjQxOSAwLjcyODIsMC41Mzc2bDAgMi45MDRjMCwwLjUwMDkgLTIuNTIyNiwxLjMwNDQgLTMuNTUxMywxLjMwNDQgLTEuMDUxMywwIC0zLjczMDEsLTAuODY4NSAtMy43MzAxLC0xLjMwNDRsMCAtMi45MDRjMCwtMC4yOTU4IDAuMzI3NiwtMC41Mzc2IDAuNzI4MiwtMC41Mzc2eiIvPgogIDxwYXRoIGNsYXNzPSJmaWwyIiBkPSJNMy43NDIyIDMuMjE2N2MwLjc0OCwwIDEuMzU0NywwLjYwNjcgMS4zNTQ3LDEuMzU0NyAwLDAuNzQ4IC0wLjYwNjcsMS4zNTQ3IC0xLjM1NDcsMS4zNTQ3IC0wLjc0OCwwIC0xLjM1NDcsLTAuNjA2NyAtMS4zNTQ3LC0xLjM1NDcgMCwtMC43NDggMC42MDY3LC0xLjM1NDcgMS4zNTQ3LC0xLjM1NDd6Ii8+CiAgPHBhdGggY2xhc3M9ImZpbDMiIGQ9Ik0yLjU5ODUgNC41Nzg5YzAsLTAuMDk0NyAwLjAyNTQsLTAuMTY4OCAwLjA3NjMsLTAuMjIyMyAwLjA1MDgsLTAuMDUzNSAwLjExNjQsLTAuMDgwMyAwLjE5NjksLTAuMDgwMyAwLjA1MjYsMCAwLjEwMDIsMC4wMTI2IDAuMTQyNCwwLjAzNzcgMC4wNDIyLDAuMDI1MiAwLjA3NDUsMC4wNjAzIDAuMDk2NywwLjEwNTMgMC4wMjIyLDAuMDQ0OSAwLjAzMzIsMC4wOTYgMC4wMzMyLDAuMTUzIDAsMC4wNTc5IC0wLjAxMTYsMC4xMDk3IC0wLjAzNDksMC4xNTUzIC0wLjAyMzQsMC4wNDU3IC0wLjA1NjUsMC4wODAyIC0wLjA5OTIsMC4xMDM3IC0wLjA0MjksMC4wMjM1IC0wLjA4OSwwLjAzNTIgLTAuMTM4NiwwLjAzNTIgLTAuMDUzOCwwIC0wLjEwMTcsLTAuMDEyOSAtMC4xNDQsLTAuMDM4OSAtMC4wNDIzLC0wLjAyNTkgLTAuMDc0MywtMC4wNjE0IC0wLjA5NjEsLTAuMTA2MyAtMC4wMjE4LC0wLjA0NDcgLTAuMDMyNywtMC4wOTIzIC0wLjAzMjcsLTAuMTQyNHptMC4wNzc4IDAuMDAxMmMwLDAuMDY4OCAwLjAxODUsMC4xMjI5IDAuMDU1NCwwLjE2MjUgMC4wMzcxLDAuMDM5NiAwLjA4MzQsMC4wNTkzIDAuMTM5MiwwLjA1OTMgMC4wNTY5LDAgMC4xMDM2LC0wLjAxOTkgMC4xNDAzLC0wLjA1OTkgMC4wMzY2LC0wLjAzOTkgMC4wNTUxLC0wLjA5NjYgMC4wNTUxLC0wLjE3MDEgMCwtMC4wNDYzIC0wLjAwOCwtMC4wODY5IC0wLjAyMzYsLTAuMTIxNSAtMC4wMTU3LC0wLjAzNDcgLTAuMDM4NywtMC4wNjE1IC0wLjA2ODgsLTAuMDgwNiAtMC4wMzAzLC0wLjAxOSAtMC4wNjQyLC0wLjAyODYgLTAuMTAxOCwtMC4wMjg2IC0wLjA1MzUsMCAtMC4wOTk1LDAuMDE4MyAtMC4xMzgsMC4wNTUxIC0wLjAzODUsMC4wMzY3IC0wLjA1NzgsMC4wOTggLTAuMDU3OCwwLjE4Mzh6bTAuNjA5NCAwLjI3NjdsMCAtMC41NzA0IDAuMjE1MiAwYzAuMDM3OCwwIDAuMDY2OCwwLjAwMTggMC4wODY3LDAuMDA1NCAwLjAyOCwwLjAwNDYgMC4wNTE1LDAuMDEzNSAwLjA3MDUsMC4wMjY3IDAuMDE4OSwwLjAxMyAwLjAzNDEsMC4wMzE1IDAuMDQ1NywwLjA1NSAwLjAxMTYsMC4wMjM2IDAuMDE3MywwLjA0OTUgMC4wMTczLDAuMDc3OCAwLDAuMDQ4NSAtMC4wMTU0LDAuMDg5NyAtMC4wNDYzLDAuMTIzMyAtMC4wMzA5LDAuMDMzNCAtMC4wODY3LDAuMDUwMyAtMC4xNjczLDAuMDUwM2wtMC4xNDY0IDAgMCAwLjIzMTkgLTAuMDc1NCAwem0wLjA3NTQgLTAuMjk5MmwwLjE0NzYgMGMwLjA0ODcsMCAwLjA4MzQsLTAuMDA5MSAwLjEwMzksLTAuMDI3MyAwLjAyMDQsLTAuMDE4MiAwLjAzMDYsLTAuMDQzNyAwLjAzMDYsLTAuMDc2NiAwLC0wLjAyMzkgLTAuMDA1OSwtMC4wNDQ0IC0wLjAxOCwtMC4wNjE0IC0wLjAxMjEsLTAuMDE3IC0wLjAyNzksLTAuMDI4MSAtMC4wNDc3LC0wLjAzMzYgLTAuMDEyOCwtMC4wMDMzIC0wLjAzNjEsLTAuMDA1IC0wLjA3MDQsLTAuMDA1bC0wLjE0NiAwIDAgMC4yMDM5em0wLjUwMiAwLjI5OTJsMCAtMC41NzA0IDAuNDEyNCAwIDAgMC4wNjczIC0wLjMzNyAwIDAgMC4xNzQ2IDAuMzE1NiAwIDAgMC4wNjcgLTAuMzE1NiAwIDAgMC4xOTQyIDAuMzUwMyAwIDAgMC4wNjczIC0wLjQyNTcgMHptMC41NzM0IDBsMCAtMC41NzA0IDAuMDc3NCAwIDAuMjk5NyAwLjQ0NzggMCAtMC40NDc4IDAuMDcyMyAwIDAgMC41NzA0IC0wLjA3NzQgMCAtMC4yOTk2IC0wLjQ0ODMgMCAwLjQ0ODMgLTAuMDcyNCAweiIvPgogPC9nPgo8L3N2Zz4K",
	"bomb-bg": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwwIHtmaWxsOm5vbmV9DQogICAgLmZpbDEge2ZpbGw6I0I1MjgyMn0NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0i5Zu+5bGCX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8cmVjdCBjbGFzcz0iZmlsMCIgd2lkdGg9IjcuNDUwNiIgaGVpZ2h0PSI5LjMxMzIiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDEiIGQ9Ik0wLjgxMjggMGw1LjgyNSAwYzAuNDAwNiwwIDAuNzI4MiwwLjI0MTggMC43MjgyLDAuNTM3NmwwIDguMjM4YzAsMC4yOTU4IC0wLjMyNzYsMC41Mzc2IC0wLjcyODIsMC41Mzc2bC01LjgyNSAwYy0wLjQwMDYsMCAtMC43MjgyLC0wLjI0MTggLTAuNzI4MiwtMC41Mzc2bDAgLTguMjM4YzAsLTAuMjk1OCAwLjMyNzYsLTAuNTM3NiAwLjcyODIsLTAuNTM3NnoiLz4NCiA8L2c+DQo8L3N2Zz4NCg==",
	"bomb-w-bg": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwxIHtmaWxsOiMxRjFBMTd9DQogICAgLmZpbDAge2ZpbGw6I0I1MjgyMn0NCiAgICAuZmlsMiB7ZmlsbDojMUYxQTE3O2ZpbGwtcnVsZTpub256ZXJvfQ0KICAgXV0+DQogIDwvc3R5bGU+DQogPC9kZWZzPg0KIDxnIGlkPSLlm77lsYJfeDAwMjBfMSI+DQogIDxtZXRhZGF0YSBpZD0iQ29yZWxDb3JwSURfMENvcmVsLUxheWVyIi8+DQogIDxwYXRoIGNsYXNzPSJmaWwwIiBkPSJNMC44MTI4IDBsNS44MjUgMGMwLjQwMDYsMCAwLjcyODIsMC4yNDE4IDAuNzI4MiwwLjUzNzZsMCA4LjIzOGMwLDAuMjk1OCAtMC4zMjc2LDAuNTM3NiAtMC43MjgyLDAuNTM3NmwtNS44MjUgMGMtMC40MDA2LDAgLTAuNzI4MiwtMC4yNDE4IC0wLjcyODIsLTAuNTM3NmwwIC04LjIzOGMwLC0wLjI5NTggMC4zMjc2LC0wLjUzNzYgMC43MjgyLC0wLjUzNzZ6Ii8+DQogIDxnIGlkPSJfMTUxMjc0NjU2Ij4NCiAgIDxwYXRoIGlkPSJfMTUxMjcyMjA4IiBjbGFzcz0iZmlsMSIgZD0iTTQuMjEzNiA0LjAwMjljMC43MjQxLDAuNTY1NSAwLjg1MjYsMS42MTEyIDAuMjg3MywyLjMzNTIgLTAuNTY1NSwwLjcyNDEgLTEuNjExMiwwLjg1MjggLTIuMzM1MywwLjI4NzMgLTAuNzI0LC0wLjU2NTQgLTAuODUyNywtMS42MTExIC0wLjI4NzIsLTIuMzM1MiAwLjU2NTMsLTAuNzI0IDEuNjExMSwtMC44NTI3IDIuMzM1MiwtMC4yODczem0tMC45MTAyIDAuMTg4MmwwLjU4MTIgMC40NTM5IC0wLjAwMTkgMC4wMDI0Yy0wLjQyNTUsLTAuMjQxNSAtMC45NjExLC0wLjE3OTYgLTEuMzIwMSwwLjE1MDZsLTAuMDAzNiAtMC4wMDI4IDAuNDQyOCAtMC41NjdjMC4wNzI4LC0wLjA5MzIgMC4yMDg1LC0wLjEwOTggMC4zMDE2LC0wLjAzNzF6Ii8+DQogICA8cmVjdCBpZD0iXzE0NzAxOTUyOCIgY2xhc3M9ImZpbDEiIHRyYW5zZm9ybT0ibWF0cml4KDAuODQxODg3IDAuNjU3NDM3IC0wLjYxNzk0NSAwLjc5MTMxNiAzLjkwMzM4IDMuNDUyMzcpIiB3aWR0aD0iMS4wOTIyIiBoZWlnaHQ9IjAuNDU3MiIvPg0KICAgPHBhdGggaWQ9Il8xMTU0MDk5NDQiIGNsYXNzPSJmaWwyIiBkPSJNNC4yOTY1IDMuNzU5NGwwLjI0NjggLTAuMzE2IDAuMDA0MSAtMC4wMDQ4YzAuMDA2MywtMC4wMDY2IDAuMjcyNCwtMC4yOTQ4IDAuNTYwMiwtMC4zMzA2IDAuMDQ1MSwwLjAwNzMgMC4wNzUsMC4wNDczIDAuMDc4NiwwLjA3NTEgMC4wMDE1LDAuMDEyNyAwLjAwNjgsMC4wNTM5IC0wLjA1MzQsMC4wODY4IC0wLjE5MSwwLjAyMDkgLTAuNDI2NiwwLjIzOTYgLTAuNDU3NywwLjI3OTNsLTAuMjQ1MyAwLjMxNDIgLTAuMTMzMyAtMC4xMDR6Ii8+DQogICA8cmVjdCBpZD0iXzE1MDg3NDI3MiIgY2xhc3M9ImZpbDEiIHRyYW5zZm9ybT0ibWF0cml4KDAuNzg4NzY3IDEuMDA4NzQgLTAuOTE0MTUzIDAuNzE0ODA5IDQuNTA2MzMgMi40NzA2KSIgd2lkdGg9IjAuNDMwMSIgaGVpZ2h0PSIwLjA5NTgiIHJ4PSIwLjA0NzkiIHJ5PSIwLjA0NzkiLz4NCiAgIDxyZWN0IGlkPSJfMTE1MTMyNzIwIiBjbGFzcz0iZmlsMSIgdHJhbnNmb3JtPSJtYXRyaXgoLTAuMzI1MTgzIDEuMjM4NTMgLTEuMTIyNCAtMC4yOTQ2OTMgNS4zMTE5MiAyLjM1MTQ2KSIgd2lkdGg9IjAuNDMwMSIgaGVpZ2h0PSIwLjA5NTgiIHJ4PSIwLjA0NzkiIHJ5PSIwLjA0NzkiLz4NCiAgIDxyZWN0IGlkPSJfMTUwODczODY0IiBjbGFzcz0iZmlsMSIgdHJhbnNmb3JtPSJtYXRyaXgoLTEuMTE5MiAwLjYyMjE2NiAtMC41NjM4MjkgLTEuMDE0MjYgNS44NTQ1NCAyLjgyNDIxKSIgd2lkdGg9IjAuNDMwMSIgaGVpZ2h0PSIwLjA5NTgiIHJ4PSIwLjA0NzkiIHJ5PSIwLjA0NzkiLz4NCiAgIDxyZWN0IGlkPSJfMTUxMjc0MDMyIiBjbGFzcz0iZmlsMSIgdHJhbnNmb3JtPSJtYXRyaXgoLTEuMjQ5MDEgLTAuMjgyMjQ4IDAuMjU1NzgzIC0xLjEzMTkgNS45MTA3IDMuNDc1OTcpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il84ODY4NTY0OCIgY2xhc3M9ImZpbDEiIHRyYW5zZm9ybT0ibWF0cml4KC0wLjc3NzUzIC0xLjAxNzQyIDAuOTIyMDI1IC0wLjcwNDYyNiA1LjU0MzkgMy45ODI0KSIgd2lkdGg9IjAuNDMwMSIgaGVpZ2h0PSIwLjA5NTgiIHJ4PSIwLjA0NzkiIHJ5PSIwLjA0NzkiLz4NCiAgPC9nPg0KIDwvZz4NCjwvc3ZnPg0K",
	bomb: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwwIHtmaWxsOiMxRjFBMTd9DQogICAgLmZpbDEge2ZpbGw6d2hpdGV9DQogICAgLmZpbDIge2ZpbGw6IzFGMUExNztmaWxsLXJ1bGU6bm9uemVyb30NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0i5Zu+5bGCX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8ZyBpZD0iXzExMjE0MDQ0MCI+DQogICA8Y2lyY2xlIGlkPSJfMTQ3NzkzNTkyIiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoMC42NjYxMTggMC41MjAxNzcgLTAuNTIwMTc3IDAuNjY2MTE4IDMuMjU3NzggNS4yMjY1KSIgcj0iMS45Njg1Ii8+DQogICA8cmVjdCBpZD0iXzE0NzAxOTUyOCIgY2xhc3M9ImZpbDAiIHRyYW5zZm9ybT0ibWF0cml4KDAuODQxODg3IDAuNjU3NDM3IC0wLjYxNzk0NSAwLjc5MTMxNiAzLjk3MTU4IDMuMzY0NjcpIiB3aWR0aD0iMS4wOTIyIiBoZWlnaHQ9IjAuNDU3MiIvPg0KICAgPHBhdGggaWQ9Il8xMTIxNDAxMDQiIGNsYXNzPSJmaWwxIiBkPSJNMy4zNzE2IDQuMTAzNGwwLjU4MTIgMC40NTM4IC0wLjAwMTkgMC4wMDI1Yy0wLjQyNTUsLTAuMjQxNiAtMC45NjExLC0wLjE3OTUgLTEuMzIwMSwwLjE1MDZsLTAuMDAzNiAtMC4wMDI4IDAuNDQyOCAtMC41NjdjMC4wNzI3LC0wLjA5MzIgMC4yMDg1LC0wLjEwOTkgMC4zMDE2LC0wLjAzNzF6Ii8+DQogICA8cGF0aCBpZD0iXzExNTQwOTk0NCIgY2xhc3M9ImZpbDIiIGQ9Ik00LjM2NDcgMy42NzE3bDAuMjQ2OCAtMC4zMTYgMC4wMDQxIC0wLjAwNDhjMC4wMDYzLC0wLjAwNjYgMC4yNzI0LC0wLjI5NDggMC41NjAyLC0wLjMzMDYgMC4wNDUxLDAuMDA3MyAwLjA3NSwwLjA0NzMgMC4wNzg2LDAuMDc1MSAwLjAwMTUsMC4wMTI3IDAuMDA2OCwwLjA1MzkgLTAuMDUzNCwwLjA4NjggLTAuMTkxLDAuMDIwOSAtMC40MjY2LDAuMjM5NiAtMC40NTc3LDAuMjc5M2wtMC4yNDUzIDAuMzE0MiAtMC4xMzMzIC0wLjEwNHoiLz4NCiAgIDxyZWN0IGlkPSJfMTUwODc0MjcyIiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoMC43ODg3NjcgMS4wMDg3NCAtMC45MTQxNTMgMC43MTQ4MDkgNC41NzQ1MyAyLjM4MjkpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xMTUxMzI3MjAiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMC4zMjUxODMgMS4yMzg1MyAtMS4xMjI0IC0wLjI5NDY5MyA1LjM4MDEyIDIuMjYzNzYpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xNTA4NzM4NjQiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMS4xMTkyIDAuNjIyMTY2IC0wLjU2MzgyOSAtMS4wMTQyNiA1LjkyMjc0IDIuNzM2NTEpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICAgPHJlY3QgaWQ9Il8xNTEyNzQwMzIiIGNsYXNzPSJmaWwwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMS4yNDkwMSAtMC4yODIyNDggMC4yNTU3ODMgLTEuMTMxOSA1Ljk3ODkgMy4zODgyNykiIHdpZHRoPSIwLjQzMDEiIGhlaWdodD0iMC4wOTU4IiByeD0iMC4wNDc5IiByeT0iMC4wNDc5Ii8+DQogICA8cmVjdCBpZD0iXzg4Njg1NjQ4IiBjbGFzcz0iZmlsMCIgdHJhbnNmb3JtPSJtYXRyaXgoLTAuNzc3NTMgLTEuMDE3NDIgMC45MjIwMjUgLTAuNzA0NjI2IDUuNjEyMSAzLjg5NDcpIiB3aWR0aD0iMC40MzAxIiBoZWlnaHQ9IjAuMDk1OCIgcng9IjAuMDQ3OSIgcnk9IjAuMDQ3OSIvPg0KICA8L2c+DQogPC9nPg0KPC9zdmc+DQo=",
	"gift-bg": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwwIHtmaWxsOm5vbmV9DQogICAgLmZpbDEge2ZpbGw6I0RCMzIyNn0NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0i5Zu+5bGCX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8cmVjdCBjbGFzcz0iZmlsMCIgd2lkdGg9IjcuNDUwNiIgaGVpZ2h0PSI5LjMxMzIiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDEiIGQ9Ik0wLjgxMjggMGw1LjgyNSAwYzAuNDAwNiwwIDAuNzI4MiwwLjI0MTggMC43MjgyLDAuNTM3NmwwIDguMjM4YzAsMC4yOTU4IC0wLjMyNzYsMC41Mzc2IC0wLjcyODIsMC41Mzc2bC01LjgyNSAwYy0wLjQwMDYsMCAtMC43MjgyLC0wLjI0MTggLTAuNzI4MiwtMC41Mzc2bDAgLTguMjM4YzAsLTAuMjk1OCAwLjMyNzYsLTAuNTM3NiAwLjcyODIsLTAuNTM3NnoiLz4NCiA8L2c+DQo8L3N2Zz4NCg==",
	"gift-w-bg": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5zdHIwIHtzdHJva2U6I0ZGRjUwMDtzdHJva2Utd2lkdGg6MC4yfQ0KICAgIC5maWwxIHtmaWxsOm5vbmV9DQogICAgLmZpbDAge2ZpbGw6I0RCMzIyNn0NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0i5Zu+5bGCX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8cGF0aCBjbGFzcz0iZmlsMCIgZD0iTTAuODEyOCAwbDUuODI1IDBjMC40MDA2LDAgMC43MjgyLDAuMjQxOCAwLjcyODIsMC41Mzc2bDAgOC4yMzhjMCwwLjI5NTggLTAuMzI3NiwwLjUzNzYgLTAuNzI4MiwwLjUzNzZsLTUuODI1IDBjLTAuNDAwNiwwIC0wLjcyODIsLTAuMjQxOCAtMC43MjgyLC0wLjUzNzZsMCAtOC4yMzhjMCwtMC4yOTU4IDAuMzI3NiwtMC41Mzc2IDAuNzI4MiwtMC41Mzc2eiIvPg0KICA8ZyBpZD0iXzE0Nzc5MzkyOCI+DQogICA8cGF0aCBpZD0iXzg4Njg1NjcyIiBjbGFzcz0iZmlsMSBzdHIwIiBkPSJNMy43MjUzIDIuNTE2bDEuMzU3NiAwYzAuMDUwNiwwIDAuMDkyLDAuMDM3NCAwLjA5MiwwLjA4MzFsMCAwLjY2NTRjMCwwLjA0NTcgLTAuMDQxNCwwLjA4MzEgLTAuMDkyLDAuMDgzMWwtMS4zNTc2IDAgMCAtMC44MzE2eiIvPg0KICAgPHBhdGggaWQ9Il8xNTA4NzQwMDgiIGNsYXNzPSJmaWwxIHN0cjAiIGQ9Ik0zLjcyNTMgMi41MTZsLTEuMzU3NiAwYy0wLjA1MDYsMCAtMC4wOTIsMC4wMzc0IC0wLjA5MiwwLjA4MzFsMCAwLjY2NTRjMCwwLjA0NTcgMC4wNDE0LDAuMDgzMSAwLjA5MiwwLjA4MzFsMS4zNTc2IDAgMCAtMC44MzE2eiIvPg0KICAgPHBhdGggaWQ9Il8xMTU0MDkzOTIiIGNsYXNzPSJmaWwxIHN0cjAiIGQ9Ik0yLjUzODMgNC41NTk1bDEuMTg3IDAgMCAtMS4yMTE1IC0xLjE4NyAwYy0wLjAzNDUsMCAtMC4wNjI2LDAuMDI1MiAtMC4wNjI2LDAuMDU2NGwwIDEuMDk4N2MwLDAuMDMxMiAwLjAyODEsMC4wNTY0IDAuMDYyNiwwLjA1NjR6Ii8+DQogICA8cGF0aCBpZD0iXzE1MDg3NzE3NiIgY2xhc3M9ImZpbDEgc3RyMCIgZD0iTTQuOTEyMyA0LjU1OTVsLTEuMTg3IDAgMCAtMS4yMTE1IDEuMTg3IDBjMC4wMzQ1LDAgMC4wNjI2LDAuMDI1MiAwLjA2MjYsMC4wNTY0bDAgMS4wOTg3YzAsMC4wMzEyIC0wLjAyODEsMC4wNTY0IC0wLjA2MjYsMC4wNTY0eiIvPg0KICAgPHBhdGggaWQ9Il8xNDc3OTQwOTYiIGNsYXNzPSJmaWwxIHN0cjAiIGQ9Ik0zLjcwOTMgMi41MTYxYzAuMjU2LC0wLjMxOTkgMC41MDU2LC0wLjU1NDQgMC43NTg4LC0wLjU2MzIgMC4yNTIyLC0wLjA0MTggMC41ODg3LDAuMjQ2NyAwLjI1NDcsMC41NjMyIi8+DQogICA8cGF0aCBpZD0iXzE1MDg3NzQ4OCIgY2xhc3M9ImZpbDEgc3RyMCIgZD0iTTMuNzA5MyAyLjUxNjFjLTAuMjU2LC0wLjMxOTkgLTAuNTA1NSwtMC41NTQ0IC0wLjc1ODcsLTAuNTYzMiAtMC4yNTIyLC0wLjA0MTggLTAuNTg4NywwLjI0NjcgLTAuMjU0NywwLjU2MzIiLz4NCiAgPC9nPg0KIDwvZz4NCjwvc3ZnPg0K",
	gift: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5zdHIwIHtzdHJva2U6I0ZGRjUwMDtzdHJva2Utd2lkdGg6MC4yfQ0KICAgIC5maWwwIHtmaWxsOm5vbmV9DQogICBdXT4NCiAgPC9zdHlsZT4NCiA8L2RlZnM+DQogPGcgaWQ9IuWbvuWxgl94MDAyMF8xIj4NCiAgPG1ldGFkYXRhIGlkPSJDb3JlbENvcnBJRF8wQ29yZWwtTGF5ZXIiLz4NCiAgPGcgaWQ9Il8xNDc3OTM5MjgiPg0KICAgPHBhdGggaWQ9Il84ODY4NTY3MiIgY2xhc3M9ImZpbDAgc3RyMCIgZD0iTTMuNzI1MyAyLjUxNmwxLjM1NzYgMGMwLjA1MDYsMCAwLjA5MiwwLjAzNzQgMC4wOTIsMC4wODMxbDAgMC42NjU0YzAsMC4wNDU3IC0wLjA0MTQsMC4wODMxIC0wLjA5MiwwLjA4MzFsLTEuMzU3NiAwIDAgLTAuODMxNnoiLz4NCiAgIDxwYXRoIGlkPSJfMTUwODc0MDA4IiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMy43MjUzIDIuNTE2bC0xLjM1NzYgMGMtMC4wNTA2LDAgLTAuMDkyLDAuMDM3NCAtMC4wOTIsMC4wODMxbDAgMC42NjU0YzAsMC4wNDU3IDAuMDQxNCwwLjA4MzEgMC4wOTIsMC4wODMxbDEuMzU3NiAwIDAgLTAuODMxNnoiLz4NCiAgIDxwYXRoIGlkPSJfMTE1NDA5MzkyIiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMi41MzgzIDQuNTU5NWwxLjE4NyAwIDAgLTEuMjExNSAtMS4xODcgMGMtMC4wMzQ1LDAgLTAuMDYyNiwwLjAyNTIgLTAuMDYyNiwwLjA1NjRsMCAxLjA5ODdjMCwwLjAzMTIgMC4wMjgxLDAuMDU2NCAwLjA2MjYsMC4wNTY0eiIvPg0KICAgPHBhdGggaWQ9Il8xNTA4NzcxNzYiIGNsYXNzPSJmaWwwIHN0cjAiIGQ9Ik00LjkxMjMgNC41NTk1bC0xLjE4NyAwIDAgLTEuMjExNSAxLjE4NyAwYzAuMDM0NSwwIDAuMDYyNiwwLjAyNTIgMC4wNjI2LDAuMDU2NGwwIDEuMDk4N2MwLDAuMDMxMiAtMC4wMjgxLDAuMDU2NCAtMC4wNjI2LDAuMDU2NHoiLz4NCiAgIDxwYXRoIGlkPSJfMTQ3Nzk0MDk2IiBjbGFzcz0iZmlsMCBzdHIwIiBkPSJNMy43MDkzIDIuNTE2MWMwLjI1NiwtMC4zMTk5IDAuNTA1NiwtMC41NTQ0IDAuNzU4OCwtMC41NjMyIDAuMjUyMiwtMC4wNDE4IDAuNTg4NywwLjI0NjcgMC4yNTQ3LDAuNTYzMiIvPg0KICAgPHBhdGggaWQ9Il8xNTA4Nzc0ODgiIGNsYXNzPSJmaWwwIHN0cjAiIGQ9Ik0zLjcwOTMgMi41MTYxYy0wLjI1NiwtMC4zMTk5IC0wLjUwNTUsLTAuNTU0NCAtMC43NTg3LC0wLjU2MzIgLTAuMjUyMiwtMC4wNDE4IC0wLjU4ODcsMC4yNDY3IC0wLjI1NDcsMC41NjMyIi8+DQogIDwvZz4NCiA8L2c+DQo8L3N2Zz4NCg==",
	"my-bg": "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KIDwhLS0gQ3JlYXRlZCB3aXRoIFNWRy1lZGl0IC0gaHR0cDovL3N2Zy1lZGl0Lmdvb2dsZWNvZGUuY29tLyAtLT4KIDxkZWZzPgogIDxjbGlwUGF0aCBpZD0iY3AiPgogICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgcnk9IjE1IiByeD0iMTUiIGlkPSJzdmdfNCIvPgogIDwvY2xpcFBhdGg+CiA8L2RlZnM+CiA8Zz4KICA8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+CiAgPGcgaWQ9InN2Z18zIj4KICAgPHJlY3Qgcnk9IjE1IiByeD0iMTUiIGZpbGw9IiNkNzA5MTUiIHN0cm9rZT0iIzAwMDAwMCIgc3Ryb2tlLXdpZHRoPSIwIiB4PSIwIiB5PSIwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjIwMCIgaWQ9InN2Z18xIi8+CiAgIDxjaXJjbGUgY2xpcC1wYXRoPSJ1cmwoI2NwKSIgaWQ9InN2Z18yIiByPSIxMzcuMjk1MyIgY3k9Ii01MCIgY3g9Ijc2IiBzdHJva2UtbGluZWNhcD0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtZGFzaGFycmF5PSJudWxsIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMDAwMCIgZmlsbD0iI2ZjMGQxYiIvPgogIDwvZz4KICA8ZyBpZD0ic3ZnXzciPgogICA8Y2lyY2xlIGZpbGw9IiNmZmZmMDAiIHN0cm9rZS13aWR0aD0iMCIgc3Ryb2tlLWRhc2hhcnJheT0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtbGluZWNhcD0ibnVsbCIgY3g9Ijc4LjUiIGN5PSI4NS41IiByPSIzMCIgaWQ9InN2Z181IiBzdHJva2U9IiMwMDAwMDAiLz4KICAgPHRleHQgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9InNlcmlmIiBmb250LXNpemU9IjIwIiBpZD0ic3ZnXzYiIHk9IjkzLjUiIHg9Ijc5IiBzdHJva2UtbGluZWNhcD0ibnVsbCIgc3Ryb2tlLWxpbmVqb2luPSJudWxsIiBzdHJva2UtZGFzaGFycmF5PSJudWxsIiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZT0iIzAwMDAwMCIgZmlsbD0iIzAwMDAwMCI+T1BFTjwvdGV4dD4KICA8L2c+CiA8L2c+Cjwvc3ZnPg==",
	open: "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNy40NTA2bW0iIGhlaWdodD0iOS4zMTMybW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA3LjQ1MDYgOS4zMTMyIg0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwwIHtmaWxsOiNCNTI4MjJ9DQogICAgLmZpbDEge2ZpbGw6I0RCMzIyNn0NCiAgICAuZmlsMiB7ZmlsbDojRkZGNTAwfQ0KICAgIC5maWwzIHtmaWxsOiMxRjFBMTc7ZmlsbC1ydWxlOm5vbnplcm99DQogICBdXT4NCiAgPC9zdHlsZT4NCiA8L2RlZnM+DQogPGcgaWQ9IuWbvuWxgl94MDAyMF8xIj4NCiAgPG1ldGFkYXRhIGlkPSJDb3JlbENvcnBJRF8wQ29yZWwtTGF5ZXIiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDAiIGQ9Ik0wLjgyOTcgMGw1LjgyNSAwYzAuNDAwNiwwIDAuNzI4MiwwLjI0MTggMC43MjgyLDAuNTM3NmwwIDguMjM4YzAsMC4yOTU4IC0wLjMyNzYsMC41Mzc2IC0wLjcyODIsMC41Mzc2bC01LjgyNSAwYy0wLjQwMDYsMCAtMC43MjgyLC0wLjI0MTggLTAuNzI4MiwtMC41Mzc2bDAgLTguMjM4YzAsLTAuMjk1OCAwLjMyNzYsLTAuNTM3NiAwLjcyODIsLTAuNTM3NnoiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDEiIGQ9Ik0wLjgyOTcgMGw1LjgyNSAwYzAuNDAwNiwwIDAuNzI4MiwwLjI0MTkgMC43MjgyLDAuNTM3NmwwIDIuOTA0YzAsMC41MDA5IC0yLjUyMjYsMS4zMDQ0IC0zLjU1MTMsMS4zMDQ0IC0xLjA1MTMsMCAtMy43MzAxLC0wLjg2ODUgLTMuNzMwMSwtMS4zMDQ0bDAgLTIuOTA0YzAsLTAuMjk1OCAwLjMyNzYsLTAuNTM3NiAwLjcyODIsLTAuNTM3NnoiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDIiIGQ9Ik0zLjc0MjIgMy4yMTY3YzAuNzQ4LDAgMS4zNTQ3LDAuNjA2NyAxLjM1NDcsMS4zNTQ3IDAsMC43NDggLTAuNjA2NywxLjM1NDcgLTEuMzU0NywxLjM1NDcgLTAuNzQ4LDAgLTEuMzU0NywtMC42MDY3IC0xLjM1NDcsLTEuMzU0NyAwLC0wLjc0OCAwLjYwNjcsLTEuMzU0NyAxLjM1NDcsLTEuMzU0N3oiLz4NCiAgPHBhdGggY2xhc3M9ImZpbDMiIGQ9Ik0yLjU5ODUgNC41Nzg5YzAsLTAuMDk0NyAwLjAyNTQsLTAuMTY4OCAwLjA3NjMsLTAuMjIyMyAwLjA1MDgsLTAuMDUzNSAwLjExNjQsLTAuMDgwMyAwLjE5NjksLTAuMDgwMyAwLjA1MjYsMCAwLjEwMDIsMC4wMTI2IDAuMTQyNCwwLjAzNzcgMC4wNDIyLDAuMDI1MiAwLjA3NDUsMC4wNjAzIDAuMDk2NywwLjEwNTMgMC4wMjIyLDAuMDQ0OSAwLjAzMzIsMC4wOTYgMC4wMzMyLDAuMTUzIDAsMC4wNTc5IC0wLjAxMTYsMC4xMDk3IC0wLjAzNDksMC4xNTUzIC0wLjAyMzQsMC4wNDU3IC0wLjA1NjUsMC4wODAyIC0wLjA5OTIsMC4xMDM3IC0wLjA0MjksMC4wMjM1IC0wLjA4OSwwLjAzNTIgLTAuMTM4NiwwLjAzNTIgLTAuMDUzOCwwIC0wLjEwMTcsLTAuMDEyOSAtMC4xNDQsLTAuMDM4OSAtMC4wNDIzLC0wLjAyNTkgLTAuMDc0MywtMC4wNjE0IC0wLjA5NjEsLTAuMTA2MyAtMC4wMjE4LC0wLjA0NDcgLTAuMDMyNywtMC4wOTIzIC0wLjAzMjcsLTAuMTQyNHptMC4wNzc4IDAuMDAxMmMwLDAuMDY4OCAwLjAxODUsMC4xMjI5IDAuMDU1NCwwLjE2MjUgMC4wMzcxLDAuMDM5NiAwLjA4MzQsMC4wNTkzIDAuMTM5MiwwLjA1OTMgMC4wNTY5LDAgMC4xMDM2LC0wLjAxOTkgMC4xNDAzLC0wLjA1OTkgMC4wMzY2LC0wLjAzOTkgMC4wNTUxLC0wLjA5NjYgMC4wNTUxLC0wLjE3MDEgMCwtMC4wNDYzIC0wLjAwOCwtMC4wODY5IC0wLjAyMzYsLTAuMTIxNSAtMC4wMTU3LC0wLjAzNDcgLTAuMDM4NywtMC4wNjE1IC0wLjA2ODgsLTAuMDgwNiAtMC4wMzAzLC0wLjAxOSAtMC4wNjQyLC0wLjAyODYgLTAuMTAxOCwtMC4wMjg2IC0wLjA1MzUsMCAtMC4wOTk1LDAuMDE4MyAtMC4xMzgsMC4wNTUxIC0wLjAzODUsMC4wMzY3IC0wLjA1NzgsMC4wOTggLTAuMDU3OCwwLjE4Mzh6bTAuNjA5NCAwLjI3NjdsMCAtMC41NzA0IDAuMjE1MiAwYzAuMDM3OCwwIDAuMDY2OCwwLjAwMTggMC4wODY3LDAuMDA1NCAwLjAyOCwwLjAwNDYgMC4wNTE1LDAuMDEzNSAwLjA3MDUsMC4wMjY3IDAuMDE4OSwwLjAxMyAwLjAzNDEsMC4wMzE1IDAuMDQ1NywwLjA1NSAwLjAxMTYsMC4wMjM2IDAuMDE3MywwLjA0OTUgMC4wMTczLDAuMDc3OCAwLDAuMDQ4NSAtMC4wMTU0LDAuMDg5NyAtMC4wNDYzLDAuMTIzMyAtMC4wMzA5LDAuMDMzNCAtMC4wODY3LDAuMDUwMyAtMC4xNjczLDAuMDUwM2wtMC4xNDY0IDAgMCAwLjIzMTkgLTAuMDc1NCAwem0wLjA3NTQgLTAuMjk5MmwwLjE0NzYgMGMwLjA0ODcsMCAwLjA4MzQsLTAuMDA5MSAwLjEwMzksLTAuMDI3MyAwLjAyMDQsLTAuMDE4MiAwLjAzMDYsLTAuMDQzNyAwLjAzMDYsLTAuMDc2NiAwLC0wLjAyMzkgLTAuMDA1OSwtMC4wNDQ0IC0wLjAxOCwtMC4wNjE0IC0wLjAxMjEsLTAuMDE3IC0wLjAyNzksLTAuMDI4MSAtMC4wNDc3LC0wLjAzMzYgLTAuMDEyOCwtMC4wMDMzIC0wLjAzNjEsLTAuMDA1IC0wLjA3MDQsLTAuMDA1bC0wLjE0NiAwIDAgMC4yMDM5em0wLjUwMiAwLjI5OTJsMCAtMC41NzA0IDAuNDEyNCAwIDAgMC4wNjczIC0wLjMzNyAwIDAgMC4xNzQ2IDAuMzE1NiAwIDAgMC4wNjcgLTAuMzE1NiAwIDAgMC4xOTQyIDAuMzUwMyAwIDAgMC4wNjczIC0wLjQyNTcgMHptMC41NzM0IDBsMCAtMC41NzA0IDAuMDc3NCAwIDAuMjk5NyAwLjQ0NzggMCAtMC40NDc4IDAuMDcyMyAwIDAgMC41NzA0IC0wLjA3NzQgMCAtMC4yOTk2IC0wLjQ0ODMgMCAwLjQ0ODMgLTAuMDcyNCAweiIvPg0KIDwvZz4NCjwvc3ZnPg0K"
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
	function t() {
		var t = {
			ok: !0
		},
		i = parseInt($(":radio[name=gameType]:checked").val()) || 9;
		t.grid = i;
		var M = $("#betAmount"),
		e = $("#redPacketCount"),
		s = parseInt(M.val()),
		n = parseInt(e.val());
		return isNaN(s) || s < 100 || s > 1e4 ? (t.ok = !1, M.parent().removeClass("has-success"), M.parent().addClass("has-error"), M.parent().find(".help-block").removeClass("hide")) : (t.betAmount = s, M.parent().removeClass("has-error"), M.parent().addClass("has-success"), M.parent().find(".help-block").addClass("hide")),
		isNaN(n) || n < 1 || n >= i ? (t.ok = !1, e.parent().removeClass("has-success"), e.parent().addClass("has-error"), e.parent().find(".help-block").removeClass("hide")) : (t.redPacketCount = n, e.parent().removeClass("has-error"), e.parent().addClass("has-success"), e.parent().find(".help-block").addClass("hide")),
		t
	}
	function i() {
		sound.stopAll(),
		M = new Game(e.betAmount, e.grid, e.redPacketCount),
		$("#canvas").show(),
		$("#table").empty().hide(),
		$("#betSetting").hide(),
		$("#playGame").show(),
		$("#spnAmount").text(e.betAmount),
		$("#spnGrid").text(e.grid),
		$("#spnOdds").text(M.setting.odds),
		$("#spnTotal").text(M.setting.total),
		$("#spnRPDigged").text(M.data.digged),
		$("#spnRPRemaining").text(M.data.remain),
		$("#spnBombCount").text(M.data.bomb),
		$("#spnRPPoint").text(M.data.point),
		$(".progress .progress-bar").attr("aria-valuenow", "0").css("width", "0").text("0%"),
		$(".alert").hide(),
		$("#btnAbort").show(),
		location.hash = "#btnAbort"
	}
	window.sound = new Sound;
	var M, e;
	$(function() {
		$("#btnBegin").click(function() { ! 0 === (e = t()).ok && i()
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
