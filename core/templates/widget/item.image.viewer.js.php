<script>
$(function() {
    refreshImageViewer();
    $('.magnifier-btn-left').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        magnifier_move(el, thumbnail, index);
    });
    $('.magnifier-btn-right').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        magnifier_move(el, thumbnail, index);
    });
});
function refreshImageViewer(){
    $('.docs-pictures').viewer({
        url: 'data-original'
    });
}

    function magnifier_move(magnifier, thumbnail, _boole) {
        magnifier.index = magnifier.data('inx');
        _boole ? magnifier.index++ : magnifier.index--;

        var thumbnailImg = thumbnail.find('>*'), lineLenght = thumbnailImg.length;

//        var _deviation = Math.ceil(magnifier.find('.magnifier-line').width() / thumbnailImg.width() / 2);
        var _deviation = parseInt(magnifier.find('.magnifier-line').width() / thumbnailImg.width()) - 1;

        if (lineLenght < _deviation) {
            return false;
        }
        (magnifier.index < 0) ? magnifier.index = 0 : (magnifier.index > lineLenght - _deviation) ? magnifier.index = lineLenght - _deviation : magnifier.index;

        magnifier.index = magnifier.index == 0 ? 1 : magnifier.index;

        var endLeft = (thumbnailImg.width() * magnifier.index) - thumbnailImg.width();
        thumbnail.css({
            'left': ((endLeft > 0) ? -endLeft : 0) + 'px'
        });
        magnifier.data('inx', magnifier.index);
    }
</script>