$.fn.rendor2010 = function(json,name,_html,_tpl){
	if(_tpl){
		var name_tpl = _tpl;
		if(!window["_micro_templates_"]){
			window["_micro_templates_"]={};
		}
		var _micro_templates_=window['_micro_templates_'];

		if(!window["_micro_templates_s_"]){
			window["_micro_templates_s_"]={};
		}
		var _micro_templates_s_=window['_micro_templates_s_'];
		if (!_micro_templates_[name_tpl]){
			//var tpl=unescape(this.html());
			var tpl=_html;
			//tpl=tpl.replace(/<!--|&lt;!|&lt;%|\/\*/g, "<%");
			//tpl=tpl.replace(/-->|!&gt;|%&gt;|\*\//g, "%>");
			//safari and opera,both will make <! into <!-- automatically,so do not use <! but use <% 
			//just allow <% in order to  let <!-- --> be the comment of html, let // and /* be the comment of js.but attention: do not use /*=
			tpl=tpl.replace(/&lt;%/g, "<%");
			tpl=tpl.replace(/%&gt;/g, "%>");
			tpl=tpl.replace(/\r|\*+="/g, ' ');
			tpl=tpl.split('<%').join("\r");
			tpl=tpl.replace(/(?:^|%>)[^\r]*/g, function(){return arguments[0].replace(/'|\\/g, "\\$&").replace(/\n/g, "\\n");}).replace(/\r=(.*?)%>/g, "',$1,'").split("\r").join("');");
			tpl=tpl.split('%>').join("write.push('");
			try{
				_micro_templates_s_[name_tpl]=tpl;
				var _s="try{var write=[];with("+name+"){write.push('"+ tpl +"');};return write.join('');}catch(e){alert('template runtime error: ' + e);}";
				var nf= new Function(name, _s);
			}catch(e){
				alert("template compile error: "+e);				

				var _param = {tpl:_tpl,s:_s};
				var _data=jQuery.toJSON({ param: _param, method: "JsTplCompileError", "class": "apiSystem"});
				jQuery.ajax({
					data:_data
					,error: function(XMLHttpRequest, textStatus, errorThrown){
						var _msg=XMLHttpRequest.responseText;
						_d_("myOnError.error="+_msg);
					}
				,success:function(obj, textStatus, XMLHttpRequest){
				}
				,type:"POST"
					,processData:false//post raw data
					,dataType:"json"
					,url:"../_api/?_s="+window['SID']+"&lang="+window['lang']+"&debug="+window['debug']
				});
			}
			_micro_templates_[name_tpl] = nf;
		}
		var _nf=_micro_templates_[name_tpl];
		if (!json) {
			return;
		};
		if(typeof(_nf)=="function"){
			var _html=_nf(json);
			this.html(_html);
		}else{
			_d_("name_tpl is not a function="+_nf);
		}
		if(window['debug'] && window['debug']>0){
			_micro_templates_[name_tpl]=null;//no cache for debug mode
		}
		arguments[1] || this.show();
	}else{
		this.html("<b>Template not found</b>");
	}
};
$.extend({formatTPL:function(json,name,_html,_tpl){
 	if(_tpl){
		var name_tpl = _tpl;
		if(!window["_micro_templates_"]){
			window["_micro_templates_"]={};
		}
		var _micro_templates_=window['_micro_templates_'];

		if(!window["_micro_templates_s_"]){
			window["_micro_templates_s_"]={};
		}
		var _micro_templates_s_=window['_micro_templates_s_'];
		if (!_micro_templates_[name_tpl]){
			//var tpl=unescape(this.html());
			var tpl=_html;
			//tpl=tpl.replace(/<!--|&lt;!|&lt;%|\/\*/g, "<%");
			//tpl=tpl.replace(/-->|!&gt;|%&gt;|\*\//g, "%>");
			//safari and opera,both will make <! into <!-- automatically,so do not use <! but use <% 
			//just allow <% in order to  let <!-- --> be the comment of html, let // and /* be the comment of js.but attention: do not use /*=
			tpl=tpl.replace(/&lt;%/g, "<%");
			tpl=tpl.replace(/%&gt;/g, "%>");
			tpl=tpl.replace(/\r|\*+="/g, ' ');
			tpl=tpl.split('<%').join("\r");
			tpl=tpl.replace(/(?:^|%>)[^\r]*/g, function(){return arguments[0].replace(/'|\\/g, "\\$&").replace(/\n/g, "\\n");}).replace(/\r=(.*?)%>/g, "',$1,'").split("\r").join("');");
			tpl=tpl.split('%>').join("write.push('");
			try{
				_micro_templates_s_[name_tpl]=tpl;
				var _s="try{var write=[];with("+name+"){write.push('"+ tpl +"');};return write.join('');}catch(e){alert('template runtime error: ' + e);}";
				var nf= new Function(name, _s);
			}catch(e){
				alert("template compile error: "+e);				

				var _param = {tpl:_tpl,s:_s};
				var _data=jQuery.toJSON({ param: _param, method: "JsTplCompileError", "class": "apiSystem"});
				jQuery.ajax({
					data:_data
					,error: function(XMLHttpRequest, textStatus, errorThrown){
						var _msg=XMLHttpRequest.responseText;
						_d_("myOnError.error="+_msg);
					}
				,success:function(obj, textStatus, XMLHttpRequest){
				}
				,type:"POST"
					,processData:false//post raw data
					,dataType:"json"
					,url:"../_api/?_s="+window['SID']+"&lang="+window['lang']+"&debug="+window['debug']
				});
			}
			_micro_templates_[name_tpl] = nf;
		}
		var _nf=_micro_templates_[name_tpl];
		if (!json) {
			return;
		};
		if(typeof(_nf)=="function"){
			var _html=_nf(json);
			return _html;
		}else{
			_d_("name_tpl is not a function="+_nf);
		}
		if(window['debug'] && window['debug']>0){
			_micro_templates_[name_tpl]=null;//no cache for debug mode
		}
		 
	}else{
		return "<b>Template not found</b>";
	}
 }
});


