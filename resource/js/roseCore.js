// JavaScript Document
var roseCore={
	getUrl:function(){
			var _tmp_url=window.location.href;
			_tmp_url=_tmp_url.split("?")[0];
			_tmp_url=_tmp_url.split("#")[0];
 			var _url=_tmp_url+"?_s="+window['_s']+"&lang="+window['_lang']+"&debug="+window['debug'];	
			return _url;
	},
	log:function(_key,_value){
		if(!_key && !_value) return;
		var _k,_v;
		if(!_value){
			_v=_key;
			_k="roseCore-log";
		}else{
			_k=_key;_v=_value;
		}
		if(console){
			console.log(_k,_v);
		}	
	},
	loadData:function(_config,_callback,_url,_ignoreError){
		if(!_url){
			 _url=roseCore.getUrl();
		}

		jQuery.ajax({
			timeout:10000, 
			data:$.toJSON(_config),
			complete: function(XMLHttpRequest, flagOK){
			  this.running = 0;
			},
			beforeSend: function(XMLHttpRequest){
			  this.running = 1;
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				if(_ignoreError){
					_callback({STS:true,'data':false});
					return;
				}
				if(textStatus){
					roseCore.log("loadData-textStatus",textStatus);
					switch(textStatus){
						case "timeout":
							_callback({STS:false,MSG:'ajax-error:'+'链接服务器超时！'});
							break;
						case "error":
							_callback({STS:false,MSG:'ajax-error:'+'未知错误！'});
							break;
						case "notmodified":
							_callback({STS:false,MSG:'ajax-error:'+'没编辑？'});
							break;
						case "parsererror":
						alert(errorThrown);
							_callback({STS:false,MSG:'ajax-error:'+'提交了非法数据，解析错误！'});
							break;
						default:
							_callback({STS:false,MSG:'ajax-error:'+'未捕获在其他错误类型！'});
							break;
					}
					return;
				}
				if(errorThrown){
					roseCore.log("loadData-errorThrown",errorThrown);
					_callback({STS:false,MSG:'ajax-error-errorThrown:'+errorThrown});
					return;
					 
				}
				if(_callback){
					_callback({STS:false,MSG:'rose-erro:可能是网络速度不稳定，导致和服务器通讯失败！'});
					return;
				}
					 
				alert("WHY???");
				
			},
			success: function(obj, textStatus, XMLHttpRequest){
 				if (!$.isEmptyObject(obj)){
					if(_ignoreError){
						_callback({STS:false,'MSG':"Backoffice-MSG:"+obj['errmsg']});
						return;
					}
					if(obj['errmsg']){
						alert("ERROR-MSG:"+obj['errmsg']);					 		
						return;
					}
				}else if(obj == null){
					if(_ignoreError){
						_callback({STS:true,'data':false});
						return;
					}
					_callback({STS:false,MSG:'rose-Error:返回了空对象！'});				
					return;
				}else{
					//如果obj['STS']返回了false值，请在_callback函数里处理
				}
				
				if(typeof _callback=='function'){
					_callback(obj);
				}
			},
			processData:false,
			dataType:"text json",
			url:_url,
			type:"POST"
		});	
	},
	getTplCache:function(_tplName){
		//todo:发布后保存到window级变量
		return roseCore.tplCache[_tplName];		
	},
	setTplCache:function(_tplName,_tpl){
		roseCore.tplCache[_tplName]=_tpl;
	},
	tplCache:{},
	loadTpl:function(_config){
		if(!_config) _config={};
		var _preload_entry="?method=LoadTPL&lang="+window['_lang']+"&_s="+window['_s']+"&name=";
		var _json_name=_config.jsonName;
		if(!_json_name) _json_name='_json';
		var _callback=_config.callback;
		var _data=_config.data;
		var _async=_config.async;
		if(_async==null) _async=true;
		var _tpl=_config.tpl;		
		var _tarr=_tpl.split("/");
		var _tpl_name="";
		var _tpl_path="";
 		if(_tarr.length==1){
			_tpl_name=_tarr[0];
		}else{
			for(var i=0;i<_tarr.length-1;i++){
				_tpl_path+=_tarr[i]+"/"
			}
			_tpl_name=_tarr[_tarr.length-1];
		} 
		
 		var _rt='';
		var div=null;
		var fn=function(){
			if(_rt!=''){
				div=$(_rt);			
			}
			/* 权限的统一入口
			div.find(".btn").each(function(){							 
					var aid=$(this).attr("authority_id");					
					if(aid){
						$(this).bind("mousedown",function(){
							//todo:check authority					
							if(parseInt(aid)>120){//模拟check
								alert($(this).attr("authority_id"));						
							}else{
								$(this).click();
							}
						});
					}			
			
			});
			*/
			if(_callback){
				setTimeout(function(){ _callback(div); },100);
			}
			return div;
		};
 		if(roseCore.getTplCache(_tpl)){		
			var _html=roseCore.getTplCache(_tpl);
			try{ 
				if(_data){
					_rt=roseCore.render(_data,_html,_json_name);
				}else{
					_rt=_html;
				}	
				fn(); 
			}catch(e){
 			}
						
		}
		else{			 			 
 			jQuery.ajax({
				url:_tpl_path + _preload_entry + _tpl_name,
				dataType:"text",
				processData:true,
				async:_async,
				beforeSend:function(){
 				},
				error:function(){
 				},
				complete:function(aXMLHttpRequest,sts){
 					var _html=aXMLHttpRequest.responseText;
					roseCore.setTplCache(_tpl,_html);					
					try{
						if(_data){
 							_rt=roseCore.render(_data,_html,_json_name);
						}else{
 							_rt=_html;
						}												 
						fn();
 					}catch(e){
								
 					} 				 				
				},
				error:function(){
 				}
			});//ajax
		}//
	},//end loadTpl
	formatTpl:function(_data,_jsonName,tpl){		 
 			tpl=tpl.replace(/&lt;%/g, "<%");
			tpl=tpl.replace(/%&gt;/g, "%>");
			tpl=tpl.replace(/\r|\*+="/g, ' ');
			tpl=tpl.split('<%').join("\r");
			tpl=tpl.replace(/(?:^|%>)[^\r]*/g, function(){return arguments[0].replace(/'|\\/g, "\\$&").replace(/\n/g, "\\n");}).replace(/\r=(.*?)%>/g, "',$1,'").split("\r").join("');");
			tpl=tpl.split('%>').join("write.push('");
			var nf=null;
			try{				
				var _s="try{var write=[];with("+_jsonName+"){write.push('"+ tpl +"');};return write.join('');}catch(e){alert('template runtime error: ' + e);}";
				nf= new Function(_jsonName, _s);
			}catch(e){
				alert("template compile error: "+e);				
				 
			}
			if(typeof(nf)=="function"){
				var _html=nf(_data);
				return _html;
			}else{
				return "name_tpl is not a function";
			}
			
	},
	render:function(json_obj,tpl_s,name_tpl){
		var _micro_templates_=window['_micro_templates_'];
		if(!_micro_templates_){
			_micro_templates_=window['_micro_templates_']={};
		}
		//临时变量，用于compile出现问题时来得到临时返回...
		var _micro_templates_s_=window['_micro_templates_s_'];
		if(!_micro_templates_s_){
			_micro_templates_s_=window['_micro_templates_s_']={};
		}
	
		var _func_tmp=function(){return arguments[0].replace(/'|\\/g, "\\$&").replace(/\n/g, "\\n");};//这个函数返回一个可以把单引号或者反斜杠全换成\$&，以及把真回车换成字符串\n
	
		if(!_micro_templates_[name_tpl]){
			var tpl=tpl_s;
			tpl=tpl
			.replace(/&lt;%/g, "<%")//因为有时把html拿出来的时候是会做了这样的转换
			.replace(/%&gt;/g, "%>")
			.replace(/\r|\*+="/g, ' ')//把换行或者连续的空格变成单一空格..
			.split('<%').join("\r") //把左注释换成换行
			.replace(/(?:^|%>)[^\r]*/g, _func_tmp) //这一个暂时还不是很明白，似乎是把 %>之后的空行给处理一下??
			.replace(/\r=(.*?)%>/g, "',$1,'")
			.split("\r").join("');");//join回来..
	
			tpl=tpl.split('%>').join("\n"+"_write.push('");
			_micro_templates_s_[name_tpl]=tpl;
			var obj_name="mgrender_arg_obj";
			var _s="";
			try{
				_s="try{";
					_s+="var _write=[];with("+obj_name+"){"+"\n"+"_write.push('"+ tpl +"');};return _write.join('');";
					_s+="}catch(ex){try{roseCore.log('err in tpl "+name_tpl+"');roseCore.log(window['_micro_templates_s_']['"+name_tpl+"']);roseCore.log(''+ex);}catch(e){alert(e);}}";
					var nf= new Function(obj_name, _s);
			}catch(ex){
				try{
					roseCore.log("tpl error");
					roseCore.log(_micro_templates_s_[name_tpl]);
				}catch(e){}
				throw ex;
			}
			window['_micro_templates_'][name_tpl]=_micro_templates_[name_tpl] = nf;
		}
	
		var _nf=_micro_templates_[name_tpl];
		if(typeof(_nf)=="function"){
			return _nf(json_obj);
		}else{
			throw new Error(""+name_tpl+"tpl not found");
		}
	
		//如果玩的是debug模式，把编译好的模板缓存给清掉...
		if(window['debug'] && window['debug']>0){
			_micro_templates_[name_tpl]=null;//no cache for debug mode
		}
	}
}


//***************************************************************************************************