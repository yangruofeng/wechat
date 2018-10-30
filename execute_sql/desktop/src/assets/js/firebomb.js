
function Particle(o, fireBomb) {

    this.colors = ["#6A0000", "#900000", "#902B2B", "#A63232", "#A62626", "#FD5039", "#C12F2A", "#FF6540", "#f93801"];
    //this.colors = ["#66C2FF", "#48819C", "#205487", "#1DA7D1", "#1FC3FF"];

    this.spring = 1 / 10;
    this.friction = .85;

    this.decay = .95; //randomIntFromInterval(80, 95)/100;//
    this.r = randomIntFromInterval(10, 70);
    this.R = 100 - this.r;
    this.angle = Math.random() * 2 * Math.PI;
    this.center = o; //{x:cx,y:cy} 
    this.pos = {};
    this.pos.x = this.center.x + this.r * Math.cos(this.angle);
    this.pos.y = this.center.y + this.r * Math.sin(this.angle);
    this.dest = {};
    this.dest.x = this.center.x + this.R * Math.cos(this.angle);
    this.dest.y = this.center.y + this.R * Math.sin(this.angle);
    this.color = this.colors[~~(Math.random() * this.colors.length)];
    this.vel = {
        x: 0,
        y: 0
    };
    this.acc = {
        x: 0,
        y: 0
    };

    this.update = function () {
        var dx = (this.dest.x - this.pos.x);
        var dy = (this.dest.y - this.pos.y);

        this.acc.x = dx * this.spring;
        this.acc.y = dy * this.spring;
        this.vel.x += this.acc.x;
        this.vel.y += this.acc.y;

        this.vel.x *= this.friction;
        this.vel.y *= this.friction;

        this.pos.x += this.vel.x;
        this.pos.y += this.vel.y;

        if (this.r > 0) this.r *= this.decay;
    }

    this.draw = function () {

        fireBomb.ctx.fillStyle = this.color;
        fireBomb.ctx.beginPath();
        fireBomb.ctx.arc(this.pos.x, this.pos.y, this.r, 0, 2 * Math.PI);
        fireBomb.ctx.fill();

    }

}

function Explosion(fireBomb) {

    this.pos = {
        x: Math.random() * fireBomb.cw,
        y: Math.random() * fireBomb.ch
    };
    this.particles = [];
    for (var i = 0; i < 50; i++) {
        this.particles.push(new Particle(this.pos, fireBomb));
    }

    this.update = function () {
        for (var i = 0; i < this.particles.length; i++) {
            this.particles[i].update();
            if (this.particles[i].r < .5) {
                this.particles.splice(i, 1)
            }
        }

    }

    this.draw = function () {
        for (var i = 0; i < this.particles.length; i++) {
            this.particles[i].draw();
        }
    }
}

function FireBomb(cvId, width, height) {
    this.canvas = document.getElementById(cvId);
    this.ctx = this.canvas.getContext("2d");
    this.cw = this.canvas.width = width;
    this.cx = this.cw / 2;
    this.ch = this.canvas.height = height;
    this.cy = this.ch / 2;
    this.ctx.strokeStyle = "#fff";

    this.requestId = null;
    this.rad = Math.PI / 180;

    this.explosions = [];



    this.draw = function () {
        this.requestId = window.requestAnimationFrame(this.draw.bind(this));
        this.ctx.clearRect(0, 0, this.cw, this.ch);
        this.ctx.globalCompositeOperation = "lighter";
        if (Math.random() < .1) {
            this.explosions.push(new Explosion(this));
        }

        for (var j = 0; j < this.explosions.length; j++) {

            this.explosions[j].update();
            this.explosions[j].draw();

        }

    }
    this.run = function () {

        if (this.requestId) {
            window.cancelAnimationFrame(this.requestId);
            this.requestId = null;
        }
        this.cw = this.canvas.width = window.innerWidth,
            this.cx = this.cw / 2;
        this.ch = this.canvas.height = window.innerHeight,
            this.cy = this.ch / 2;

        this.draw();
    }
    this.stop = function () {
        window.cancelAnimationFrame(this.requestId);
    }
}
// window.setTimeout(function() {
//   Init();
//   window.addEventListener('resize', Init, false);
// }, 15);

// var overlay = new Overlay('canvas', 'overlay');
// overlay.show();

// var fb = new FireBomb('overlay',window.innerWidth,window.innerHeight);
// fb.run();
// // fb.stop();