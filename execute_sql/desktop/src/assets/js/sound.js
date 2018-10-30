
function Sound() {
    this.bomb = new Howl({
        src: ['assets/sound/firebomb_expl1.mp3']
    });
    this.winning = new Howl({
        src: ['assets/sound/winning.mp3']
    });
    this.lucky = new Howl({
        src: ['assets/sound/good-result.mp3']
    });
    this.stopAll = function(){
        this.bomb.stop();
        this.winning.stop();
        this.lucky.stop();
    }
}