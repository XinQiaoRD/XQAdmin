// JavaScript Document
function wtt(obj){
	var nms = new Array();
	var vas = new Array();
	
	var wttbox = document.createElement("div");
	document.body.appendChild(wttbox);
	
	
	for(i in obj) wt_item(i);
	wt_css();
	//var hand = setInterval(wt_load, 10);
	//wt_load
	
	/////////////css函数组
	//属性数据
	function wt_item(nm){
		nms.push(nm);
		vas.push(obj[nm]);
	}

	//展示设置
	function wt_css(){
		//展示框
		css("width","700px");
		css("height","500px");
		css("margin-left","auto");
		css("margin-right","auto");
		
		css("position","absolute");
		css("top","70px");
		
		css("background-color","#efefef");
		css("border","1px solid #999");
		css("padding","13px");
		css("overflow","hidden");
		css("z-index","99999");
		
		//title
		wt_title();
		//输入属性数据
		wt_cotent();
		//cls_btn
		wt_cls_btn();
		
	}
	//标题
	function wt_title(){
		var title = document.createElement("div");
		css("height","23px", title);
		css("border-bottom","1px solid #666", title);
		css("font-size","13px", title);
		css("font-weight","bold", title);
		css("position","relative", title);
		
		cls = document.createElement("div");
		cls.id = "wt_cls";
		css("position","absolute", cls);
		css("top","-3px", cls);
		css("right","-3px", cls);
		css("height","13px", cls);
		css("width","13px", cls);
		
		css("padding","0px", cls);
		css("line-height","13px", cls);
		css("border","1px solid #666", cls);
		css("text-align","center", cls);
		css("font-size","11px", cls);
		css("font-weight","bold", cls);
		css("cursor","pointer", cls);
		
		cls.innerHTML = "X";
		
		title.innerHTML = "WT属性浏览";
		title.appendChild(cls);
		
		wttbox.appendChild(title);
		
	}
	//标题
	function wt_cotent(){
		
		var bo = document.createElement("div");
		css("width","700px", bo);
		css("height","460px", bo);
		css("margin-top","10px", bo);
		css("overflow","auto", bo);
		
		var table = document.createElement("table");
		css("width","680px", table);
		bo.appendChild(table);
		var tBody = document.createElement("tbody");
		table.appendChild(tBody);
		var ti = 0;
		for(var i=0;i<nms.length;i++) {
			if(!ti) {
				var tr = document.createElement("tr");
				tBody.appendChild(tr);
			}
			
			var td = document.createElement("td");
			css("width","180px", td);
			css("height","27px", td);
			tr.appendChild(td);
			
			var a = document.createElement("a");
			css("display","block", a);
			css("width","125px", a);
			css("height","27px", a);
			css("line-height","27px", a);
			css("font-size","13px", a);
			css("border-bottom","1px dotted #999", a);
			css("color","#333", a);
			css("text-decoration","none", a);
			css("overflow","hidden", a);
			
			a.innerHTML = nms[i];
			a.href   = "http://www.baidu.com/s?wd=js+"+nms[i]+"&rsv_bp=0&rsv_spt=3&inputT=625";
			a.target = "_blank";
			if(vas[i]==null) vas[i] = "方法";
			if(!vas[i]) vas[i] = null;
			a.title  = vas[i];
			td.appendChild(a);
			
			ti++;
			if(ti>=4) ti=0;
			
		}
		
		wttbox.appendChild(bo);
	}

	function wt_cls_btn(){
		var cls_btn = document.getElementById("wt_cls");
		if(window.addEventListener){//Mozilla, Netscape, Firefox
			cls_btn.addEventListener('click', wt_cls_btn_run, false);
		}else{//IE   
			cls_btn.attachEvent('onclick', wt_cls_btn_run);
		}
		
		function wt_cls_btn_run(){wttbox.style["display"] = "none";}
	}

	/////////////其他函数组
	function css(css, value, dom){
		if(css=="float") css = "cssFloat";
		css = css.replace(/\-(\w)/g, function(all, letter){
				return letter.toUpperCase();
			});
		if(!dom) dom = wttbox;
		dom.style[css] = value;
	}
//	function wt_load(){
//		try{
//			document.body.appendChild(wttbox);
//			clearInterval(hand);
//			
//			var cls = document.getElementById("wt_cls");
//			if(window.addEventListener){//Mozilla, Netscape, Firefox
//				cls.addEventListener('click', $wt_cls, false);
//			}else{//IE   
//				cls.attachEvent('onclick', $wt_cls);
//			}
//		}catch(e){}
//	}
	
	
}