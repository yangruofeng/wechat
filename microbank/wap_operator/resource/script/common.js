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
  if(refresh_type == 3) {
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

function addcookie(name,value,expireHours){
	var cookieString=name+"="+escape(value)+"; path=/";
	//判断是否设置过期时间
	if(expireHours>0){
		var date=new Date();
		date.setTime(date.getTime+expireHours*3600*1000);
		cookieString=cookieString+"; expire="+date.toGMTString();
	}
	document.cookie=cookieString;
}

function getcookie(name){
	var strcookie=document.cookie;
	var arrcookie=strcookie.split("; ");
	for(var i=0;i<arrcookie.length;i++){
	var arr=arrcookie[i].split("=");
	if(arr[0]==name)return arr[1];
	}
	return "";
}

//验证金钱格式
function checkMoney(money){
  var reg = /(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/;
  if (reg.test(money)) {
    return true;
  }else{
    return false;
  }
}

//验证正整数
function checkInteger(integer){
  var reg = /^([1-9]\d*|[0]{1,1})$/; //含0正整数
  if (reg.test(integer)) {
    return true;
  }else{
    return false;
  }
}

//验证0-100（两位小数）
function checkSectionZeroToHundred(section){
  var reg = /^(0|\d{1,2})(\.\d{1,2})?$/;
  if (reg.test(section)) {
    return true;
  }else{
    return false;
  };
}