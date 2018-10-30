<div>
    <table class="table verify-table">
        <tbody class="table-body">
        <?php foreach ($data['data'] as $key => $row) { ?>
            <tr>
                <td class="magnifier<?php echo $key; ?>" style="width: 380px;">
                    <div class="magnifier" index="<?php echo $key; ?>">
                        <div class="magnifier-container" style="display:none;">
                            <div class="images-cover"></div>
                            <div class="move-view"></div>
                        </div>
                        <div class="magnifier-assembly">
                            <div class="magnifier-btn">
                                <span class="magnifier-btn-left">&lt;</span>
                                <span class="magnifier-btn-right">&gt;</span>
                            </div>
                            <!--按钮组-->
                            <div class="magnifier-line">
                                <ul class="clearfix animation03">
                                    <?php foreach ($row['cert_images'] as $value) { ?>
                                        <li>
                                            <a target="_blank" href="<?php echo getImageUrl($value['image_url']); ?>">
                                                <div class="small-img">
                                                    <img src="<?php echo getImageUrl($value['image_url'], imageThumbVersion::SMALL_IMG); ?>"/>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>

                                </ul>
                            </div>
                            <!--缩略图-->
                        </div>
                        <div class="magnifier-view"></div>
                        <!--经过放大的图片显示容器-->
                    </div>
                </td>
                <td>
                    <div class="cert-info">
                        <p><label class="lab-name">Source Type : </label>
                            <?php echo $lang['cert_source_type_' . $row['source_type']]?>
                        </p>
                        <p><label class="lab-name">Submit Time : </label><?php echo timeFormat($row['create_time']); ?></p>
                        <p><label class="lab-name">State : </label><?php echo $lang['verify_state_' . $row['verify_state']]; ?></p>
                        <p><label class="lab-name">Remark : </label><?php echo $row['verify_remark'] ?: ' /'; ?></p>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>

<?php include_once(template("widget/inc_content_pager")); ?>
<script>
    $('.magnifier-btn-left').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        move(el, thumbnail, index);
    });
    $('.magnifier-btn-right').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        move(el, thumbnail, index);
    });

    function move(magnifier, thumbnail, _boole) {
        magnifier.index = _boole;
        (_boole) ? magnifier.index++ : magnifier.index--;
        var thumbnailImg = thumbnail.find('>*'), lineLenght = thumbnailImg.length;
        var _deviation = Math.ceil(magnifier.width() / thumbnailImg.width() / 2);
        if (lineLenght < _deviation) {
            return false;
        }
        (magnifier.index < 0) ? magnifier.index = 0 : (magnifier.index > lineLenght - _deviation) ? magnifier.index = lineLenght - _deviation : magnifier.index;
        var endLeft = (thumbnailImg.width() * magnifier.index) - thumbnailImg.width();
        thumbnail.css({
            'left': ((endLeft > 0) ? -endLeft : 0) + 'px'
        });
    }
</script>
