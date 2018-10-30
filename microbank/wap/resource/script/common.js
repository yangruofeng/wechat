function convertImgDataToBlob(base64Data) {
  var format = "image/jpeg";
  var base64 = base64Data;
  var code = window.atob(base64.split(",")[1]);
  var aBuffer = new window.ArrayBuffer(code.length);
  var uBuffer = new window.Uint8Array(aBuffer);
  for(var i = 0; i < code.length; i++){
      uBuffer[i] = code.charCodeAt(i) & 0xff ;
  }
  var blob = null;
  try{
      blob = new Blob([uBuffer], {type : format});
  }
  catch(e){
      window.BlobBuilder = window.BlobBuilder ||
      window.WebKitBlobBuilder ||
      window.MozBlobBuilder ||
      window.MSBlobBuilder;
      if(e.name == 'TypeError' && window.BlobBuilder){
          var bb = new window.BlobBuilder();
          bb.append(uBuffer.buffer);
          blob = bb.getBlob("image/jpeg");

      }
      else if(e.name == "InvalidStateError"){
          blob = new Blob([aBuffer], {type : format});
      }
      else{

      }
  }
  return blob;
};

function changeLang(lang, refresh_type){
  var _domain = COOKIE_DOMAIN;
  if(!_domain || _domain == ''){
      _domain = document.domain;
  }
  $.fn.cookie((COOKIE_PRE || '')  + 'lang', lang, {path:'/', domain: _domain, expires: 3652.1});
  var href = location.href.substr(0,location.href.length-location.hash.length);

  if(!refresh_type)
    location.href = href;
  if(refresh_type == 2) {
      href = (href.indexOf('?') > 0 ? href.substr(0,href.indexOf('?'))+'?act=loan' : href);
      location.href = href;
  }
}

function formatDate(str){
  var format_date = '';
  var date1 = new Date(str); //要对比的时间
  var date2 = new Date();		//获取当前时间对象
  var num = 24*60*60*1000 ;  	//一天的毫秒数
  var cha = date2.getTime() - date1.getTime(); //两个时间的毫秒差
  if(cha > 0){
    if(cha > num){
      var m = date1.getMonth() + 1, d = date1.getDate();
      format_date = m + '/' + d;
    }else if(date1.getDate() != date2.getDate()){
      var m = date1.getMonth() + 1, d = date1.getDate();
      format_date = m + '/' + d;
    }else {
      var h = date1.getHours(), s = date1.getMinutes();
      if(s<10){
          format_date = h + ':0' + s;
      }else{
        format_date = h + ':' + s;
      }
    }
  }
  return format_date;
}

function hasClass(ele, cls) {
  return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}
  
function addClass(ele, cls) {
  if (!this.hasClass(ele,cls)) ele.className += " "+cls;
}
  
function removeClass(ele, cls) {
  if (hasClass(ele,cls)) {
    var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
    ele.className=ele.className.replace(reg,' ');
  }
}
