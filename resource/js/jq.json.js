jQuery.extend( { evalJSON : function (strJson) { return eval( "(" + strJson + ")"); } });
jQuery.extend( { toJSON : function (object) {
if(null==object)return "null";
var type = typeof object;
if ('object' == type) { if (Array == object.constructor) type = 'array';
else if (RegExp == object.constructor) type = 'regexp';
else type = 'object'; }
switch(type) { case 'undefined': case 'unknown': return; break;
case 'function':
case 'boolean':
case 'regexp': return object.toString(); break;
case 'number': return isFinite(object) ? object.toString() : 'null'; break;
case 'string': return '"' + object.replace(/(\\|\")/g,"\\$1").replace(/\n|\r|\t/g, function(){ var a = arguments[0]; return (a == '\n') ? '\\n': (a == '\r') ? '\\r': (a == '\t') ? '\\t': "" }) + '"'; break;
case 'object': if (object === null) return 'null';var pp="";var value ="";try{
var results = []; for (var property in object) {pp=object[property]; value = jQuery.toJSON(pp); if (value !== undefined) results.push('"'+property + '":' + value); } return '{' + results.join(',') + '}';
}catch(e){//alert(property+":"+value+"\n"+results.join(','));
}
break;
case 'array': var results = [];
if(object.length>0){
for(var i = 0; i < object.length; i++) { 
var value = jQuery.toJSON(object[i]);
if (value !== undefined) results.push(value); };
return '[' + results.join(',') + ']';
}
else{
for(k in object) {var kk=k; var value = jQuery.toJSON(object[k]); if (value !== undefined) results.push('"'+kk+'":'+value); }
return '{' + results.join(',') + '}';
}
break;
} } });

