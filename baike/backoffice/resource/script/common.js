/**
 * Created by sahara on 2018/5/9.
 */

/**
 *  edit by allics
 * @param el
 * @param img_url
 * @param iName  // 加传一个input name参数，可以指定添加或删除到哪个字段，原来的用image_url的寻找方式有bug
 */
function delImageItem(el,img_url, iName){

    $(el).parent().remove();
    var _name = iName?iName:'image_files';
    var _input_ele = $('input[name="'+_name+'"]');
    var imgs = _input_ele.val();
    if( !imgs ){
        return;
    }
    arr = JSON.parse(imgs.replace(/'/g, '"'));
    arr.splice($.inArray(img_url,arr),1);
    _input_ele.val(JSON.stringify(arr));
}

function commonExportExcel(_values) {
    $(".business-content").waiting();
    yo.loadData({
        _c: _values.act,
        _m: "getExportUrl",
        param: _values,
        callback: function (_o) {
            $(".business-content").unmask();
            if (_o.STS) {
                window.location.href = _o.DATA;
            } else {
                alert(_o.MSG);
            }
        }
    });
}

function commonPrintPage(_values) {
    $(".business-content").waiting();
    yo.loadData({
        _c: _values.act,
        _m: "getExportUrl",
        param: _values,
        callback: function (_o) {
            $(".business-content").unmask();
            if (_o.STS) {
                window.location.href = _o.DATA;
                //window.external.showSpecifiedUrlPrintDialog(_o.DATA);
            } else {
                alert(_o.MSG);
            }
        }
    });
}


$('.input-search-box .input-search').keydown(function(event){
    if(event.keyCode==13){return false;}
});

$('.input-search-box .input-search').keyup(function(event){
    if(event.keyCode ==13){
        $('.input-search-box').closest('div').find('.btn-search').trigger('click');
    }
});