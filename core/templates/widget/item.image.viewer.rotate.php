<!--弃用，我已经放到viewer.js里去了-->
<script type="text/javascript">
    $(function () {
        $('.img-asset-item').load(function(){
            var height = $(this).height(), width = $(this).width(), url = $(this).attr('src');
            if(width > height){
                $(this).css({"transform":"rotate(-90deg)"});  
            }
        });
    })

    function openImageWindow(imgUrl){
        window.open('<?php echo getUrl('operator', 'openImageWindow', array(), false, BACK_OFFICE_SITE_URL); ?>&img='+imgUrl);
    }
</script>