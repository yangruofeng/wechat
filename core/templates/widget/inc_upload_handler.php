<style>
    .btn-file {
        position: relative;
        display: inline-block;
        overflow: hidden;

    }
    .btn-file input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
    }
    .btn-file:hover {
        background: #AADFFD;
        border-color: #78C3F3;
        color: #004974;
        text-decoration: none;
    }
</style>
<?php
    $inputName=$inputName?:'filedata';
    $uploadDir=$uploadDir?:'draft';
?>
<div>
    <button class="btn btn-default btn-file" id="btn_file_selector"><?php echo $lang['entry_index_choose_photo']?>
        <input type="file" id="<?php echo $inputName;?>" onchange="btn_choose_file_onchange()">
    </button>
    <div id="div_file_selector" style="display: none">
        <ul class="list-group">
            <li class="list-group-item">
                <?php echo $lang['entry_index_file_name']?>:<label id="lbl_file_name"></label>
            </li>
            <li class="list-group-item">
                <?php echo $lang['entry_index_file_size']?>:<label id="lbl_file_size"></label>
            </li>
            <li class="list-group-item"><?php echo $lang['entry_index_file_progress']?>:<label id="lbl_file_progress"></label></li>
            <li class="list-group-item">
                <button class="btn btn-default" type="button" onclick="btn_uploadFile_onclick();"><?php echo $lang['entry_index_file_upload']?></button>
                <button class="btn btn-default" type="button" onclick="hideFilePopup();"><?php echo $lang['entry_index_file_close']?></button>
            </li>
        </ul>
    </div>
</div>
<script>
    $(document).ready(function(){
        var _content= $('#div_file_selector').html();
        $("#btn_file_selector").popover({
            content:_content,
            html:true,
            placement:'bottom'
        });
        $('#div_file_selector').remove();
    });

    function btn_choose_file_onchange() {
        var file = document.getElementById('<?php echo $inputName;?>').files[0];
        if (!file) return;
        $("#btn_file_selector").popover('show');
        inc_fileSelected();
    }
    function hideFilePopup(){
        $("#btn_file_selector").popover('hide');
        // $("#btn_file_selector").popover('destroy');
    }

    function inc_fileSelected() {
        var file = document.getElementById('<?php echo $inputName;?>').files[0];
        if (file) {
            var fileSize = 0;
            if (file.size > 1024 * 1024)
                fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
            else
                fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
            document.getElementById('lbl_file_name').innerHTML = file.name;
            document.getElementById('lbl_file_size').innerHTML = fileSize;
        }
    }

    function btn_uploadFile_onclick() {
        var file = document.getElementById('<?php echo $inputName;?>').files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append("<?php echo $inputName;?>", file);
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", inc_uploadProgress, false);
        xhr.addEventListener("load", inc_uploadComplete, false);
        xhr.addEventListener("error", inc_uploadFailed, false);
        xhr.addEventListener("abort", inc_uploadCanceled, false);
        xhr.open("POST", "<?php echo getUrl("base","uploadPicture",array("dir"=>$uploadDir,"inputName"=>$inputName));?>");
        xhr.send(fd);
    }

    function inc_uploadProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = Math.round(evt.loaded * 100 / evt.total);
            document.getElementById('lbl_file_progress').innerHTML = percentComplete.toString() + '%';
        }
        else {
            document.getElementById('lbl_file_progress').innerHTML = 'unable to compute';
        }
    }
    function inc_uploadComplete(evt) {
        /* This event is raised when the server send back a response */
        if(typeof(after_upload_callback)=="function"){
            after_upload_callback(eval('('+evt.target.responseText+')'))
            hideFilePopup();
        }
    }

    function inc_uploadFailed(evt) {
        document.getElementById('lbl_file_progress').innerHTML = 'Upload Failed';
    }
    function inc_uploadCanceled(evt) {
        document.getElementById('lbl_file_progress').innerHTML = 'The upload has been canceled';
    }
</script>
