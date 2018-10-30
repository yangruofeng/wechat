//deprends: _d_

function mgrender_quick(tpl_id,target_id,dataobj,tpl_s){
	if (!tpl_s){
		var o=$("#"+tpl_id)[0];
		if(o){
			tpl_s=$("#"+tpl_id).html();
			//tpl_s=o.innerHTML;
		}else{
			//_d_("found no tpl_id");
			return "";
		}
	}
	var h=mgrender(dataobj,tpl_s,tpl_id);
	$("#"+target_id).html(h);
}

function mgrender(json_obj,tpl_s,name_tpl)
{
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
				_s+="}catch(ex){try{_d_('err in tpl "+name_tpl+"');_d_(window['_micro_templates_s_']['"+name_tpl+"']);_d_(''+ex);}catch(e){alert(e);}}";
				var nf= new Function(obj_name, _s);
		}catch(ex){
			try{
				_d_("tpl error");
				_d_(_micro_templates_s_[name_tpl]);
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

