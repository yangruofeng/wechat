<style>
    .analysis-list-item-remark{
        font-size: 0.6rem;color: #808080;font-style: italic;
    }
</style>
<ul class="aui-list analysis-list aui-margin-b-10">
    <?php if(!$analysis['analysis_asset']){?>
        <li class="aui-list-item info-item paddingleft10">
            <div class="aui-list-item-inner content fontweight700">
                Business as usual,No Notices
            </div>
        </li>
    <?php }?>
    <?php foreach($analysis['analysis_asset'] as $notice_item){?>
        <li class="aui-list-item paddingleft10">
            <div class="aui-list-item-inner content">
                <?php echo $notice_item;?>
            </div>
        </li>
    <?php }?>
</ul>