<?php
//2018-1-20
class ccv extends cc
{
	public $Val = array();//变量
	public $Arr = array();//数组
	public $If  = array();//判断
	public $For = array();//循环
	public $Inc = array();//嵌入
	public $Plug= array();//插件
    public $Jcc_Mk = 0;//无jcc直接替换地址
	
	//inc嵌入
	//参数：inc地址
	function view_inc($url, $nm=''){
		if(!$url) die('view_inc的视图未定义');
		if(!$nm) $nm = 0;
		$url = $this-> view_url($url);
		
		if(Cache){
			if (!is_file(Root.$url['cache']))
				$this->cache_set($url, $nm);
			if(!$nm) $nm = $url['cache_nm'];
			$this->Inc[$nm] = $url['cache'];
		}else{
			$code = $this->cache_set($url, $nm, 'code');
			if(!$nm) $nm = $url['cache_nm'];
			$this->Inc[$nm] = $code;
			$this->caches_file_del($url['cache']);
		}
		
	}
	
	//view配置
	//参数：inc地址
	function view($url=''){
		//val
		$this->Val['D'] = D;
		$this->Val['_C'] = C;
		$this->Val['M'] = M;
		$this->Val['_M'] = M;
		$this->Val['_A'] = A;
		
		$url = $this->view_url($url);
		
		if(count7($this->Inc)){
			if(Cache) foreach($this->Inc as $k=>$v) include(Root.$v);
			else foreach($this->Inc as $k=>$v) eval($v);
		}
			
		if(Cache){
			if (!is_file(Root.$url['cache']))
				$url = $this->cache_set($url, $cnm);
			include(Root.$url['cache']);
		}else{
			$code = $this->cache_set($url, $cnm, 'code');
			eval($code);
			$this->caches_file_del($url['cache']);
		}
		
		$nm = $url['cache_nm'];
		
		//js
		if(count7($Js)) {
			$js = array();
			foreach($Js as $v) $js=array_merge($js, $v);
			
			if(count7($js)) {
				$js = array_unique($js);
				
				foreach($js as $v)
					$jsc.='<script type="text/javascript" src="'.$v.'"></script>'."\n";
			}
		}
		
		//css
		if(count7($Css)) {
			if(!$_GET['seo8k']){
				$css = array();
				foreach($Css as $v) $css=array_merge($css, $v);
				
				if(count7($css)) {
					$css = array_unique($css);
					
					foreach($css as $v)
						$cssc.='<link rel="stylesheet" type="text/css" href="'.$v.'" />'."\n";
				}
			}
		}
		
		//Jc
		if(count7($Jc)) foreach($Jc as $v) $jc.=$v;
		
		$view = $Html[$nm]['head'].$jsc.$cssc.$jc.$Body."</html>";
		echo $view;
		
		if($_GET['debug8k']) debug8k('web end');
		
		$this->cls();
		
	}
	//view读取
	//参数：读取地址,头文件读取（inc或组件不读取）
	private function view_read($url, $h=''){
		
		if(!is_file(Root.$url)) {
			echo "{$url} View文件不存在";
			return false;
		}
		
		$htm = file_get_contents(Root.$url);
		
		//jcc内容
		$jcc = explode('<!--jcc-->', $htm);
		
		//jcc code内容
		$jc = explode('<!--jcc code-->', $htm);
		
		//主体内容
		$htm = explode('<!--cc-->', $htm);

		if(!$h){
			//head处理
			$head = explode('</head>',$htm[0]);
			$head = $head[0].'</head>';
            if(strpos($head, "<!--jcc head-->")===false){
                if(strpos($head, "<script")!==false)
                    $head = preg_replace("/<script.*?<\/script>/is", '', $head);
                if(strpos($body, "<link")!==false)
                    $head = preg_replace("/<link.*?\/>/is", '', $head);
            }
		}
	
		return array('head'=>$head, 'body'=>$htm[1], 'jcc'=>$jcc[1], 'jc'=>$jc[1]);
		
	}
	
	//view路由地址
	//参数：地址
	private function view_url($url=''){
		if(!$url){
			$f_view  = A.'.html';
			$f_cache = A.CacheNm.'.php';
			$cache_nm = A;
			
			$url_view  = '/'.C.'/view/'.M."/{$f_view}";
			$url_cache = '/'.C.'/cache/'.M."/{$f_cache}";
			
		}else{
			$f_view = explode('/',$url);
			$f_view = $f_view[count7($f_view)-1];
			
			$f_cache  = explode('.',$f_view);
			$cache_nm = $f_cache[0];
			$f_cache  = $cache_nm.CacheNm.'.php';
			
			$url_view  = '/'.C.'/view/'.$url;
			$url_cache = '/'.C.'/cache/'.$url;
			$url_cache = str_replace($f_view, $f_cache, $url_cache);
		}
		return array(
			'view'=>$url_view, 
			'cache'=>$url_cache, 
			'view_file'=>$f_view, 
			'cache_file'=>$f_cache, 
			'cache_nm'=>$cache_nm
		);
	}

	
	//缓存设置
	//参数：地址组
	private function cache_set($url='', $cnm='', $mk=''){
		$cache_nm = $url['cache_nm'];
		if($cnm===0) $cnm = $cache_nm;
		
		$view = $this->view_read($url['view'], $cnm);
		
		//jcc处理
		$jcc = array('js'=>'', 'css'=>'');
		if($view['jcc']) {
			$jcc = $this->caches_jcc($view['jcc'], $url['view']);
		}else{
            $this->Jcc_Mk = 1;
        }
		
		//body处理
		$body = &$view['body'];
		
		//清除处理
		if(strpos($body, '<!--cct-->')!==false) {
			$body = preg_replace(
			"/<!--cct-->.*?<!--\/cct-->/is",
			'',
			$body);
		}
		
		//伪替换
		$this->caches_cct($body);
		$this->caches_reset($body);
		
		
		//替换变量
		if(strpos($body, '"&="')!==false) $this->caches_v($body);
		if(strpos($body, '<!--{')!==false) $this->caches_vt($body);
		
		//img src
		//if(strpos($body, 'src')!==false) $this->caches_url_re($body, $url['view'], 0);
		//转意处理
		$body = str_replace('"','\"', $body);
	
		//inc处理
		if(strpos($body, "<!--inc")!==false){
			$body = preg_replace(
			"/<!--inc-([\S]+)-->.*?<!--\/inc-[\S]+-->/is",
			'". $Inc[\''."$1".'\'] ."',
			$body);
		}
		if(strpos($body, "<!--inc-")!==false){
			$body = preg_replace(
			"/<!--inc-([\S]+)-->/is",
			'". $Inc[\''."$1".'\'] ."',
			$body);
		}
		if(strpos($body, "[@inc-")!==false){
			$body = preg_replace(
			"/\[@inc-([\S]+)@\]/is",
			'". $Inc[\''."$1".'\'] ."',
			$body);
		}
			
		//Plug
	
		
		//logic
		if(strpos($body, '<!--for-')!==false ||
		   strpos($body, '<!--if-')!==false
		) $this->caches_logic($body, $cnm);
	
		//{$}变量处理
		if(strpos($body, '{$')!==false) $this->caches_val($body);
	
		//Arr
		if(strpos($body, '[@arr-')!==false) $this->caches_arr($body);
		
		//head
		if($view['head'])
			$code.= '$Html["'.$cache_nm.'"]["head"] = "'.str_replace('"','\"', $view['head']).'";'."\n";
	
		//jcc
		if($jcc['js']) $code.= '$Js["'.$cache_nm.'"] = '.var_export($jcc['js'], true).';'."\n";
		if($jcc['css'])  $code.= '$Css["'.$cache_nm.'"] = '.var_export($jcc['css'], true).';'."\n";
		
		//jcc code
		if($view['jc'])  $code.= '$Jc["'.$cache_nm.'"] = "'.str_replace('"','\"', $view['jc']).'";'."\n";
		
		//body
		if(!$cnm) $code.= '$Body = "'.$body.'";'."\n";
		else $code.= '$Inc["'.$cnm.'"] = "'.$body.'";';
		
		//最后容错处理
		if(preg_match("/{\\$[A-Za-z0-9_]+.[A-Za-z0-9_]+}/", $code))
		$code = preg_replace(
		"/\{\\$([\w]+?)\.([\w]+?)\}/is",
		'',
		$code);

        //script与css重新配置
        //不能有jcc标签
        //2014-10-26
        if($this->Jcc_Mk){
            //css
            if(strpos($code, "<!--jcc head-->")!==false){
                $jcc_es = explode('<!--jcc head-->', $code);
                $jcc_es = $jcc_es[1];
                $jcc_est = str_replace('\"','"',$jcc_es);
                $this->caches_url_re($jcc_est, $url['view']);

                $jcc_est = str_replace('"','\"',$jcc_est);
                $code = str_replace($jcc_es, $jcc_est, $code);
            }

            //js
            if(strpos($code, "<!--jcc body-->")!==false){
                $jcc_es = explode('<!--jcc body-->', $code);
                $jcc_es = $jcc_es[1];
                $jcc_est = str_replace('\"','"',$jcc_es);
                $this->caches_url_re($jcc_est, $url['view']);
                $jcc_est = str_replace('"','\"',$jcc_est);

                $code = str_replace($jcc_es, $jcc_est, $code);
            }
        }
		
		if($mk=='code') return $code;
		
		//生成
		$code = '<?php'."\n".$code."\n".'?>';
		$this->caches_file($url['cache'], $code);
		return $url;
		
	}
	
	//缓存处理组
	
	//view地址重新配置
	//参数：包含地址的代码，样式地址，处理标记
	private function caches_url_re(&$code, $url, $mk=1){
		if($mk==1) preg_match_all("/(src|href)[\s]*=[\s]*[\"'][\s]*([^\"']*)/is", $code, $u);
		if($mk==0) preg_match_all("/(src)[\s]*=[\s]*[\"'][\s]*([^\"']*)/is", $code, $u);
		$u = $u[2];
		if(D) $url = D.$url;
		$url = explode('/',$url);
		array_pop($url);
		$url = implode('/',$url);
		foreach ($u as $v){
			if(strpos($v, '/')!==0 && 
			   strpos($v, 'http://')!==0 && 
			   strpos($v, '{$')!==0  && 
			   strpos($v, 'https://')!==0 
			   ){
				$ui = $url.'/'.$v;
	
				$uit = explode('../',$ui);
				$n = count7($uit);
				if($n>1){
					$ut = explode('/',$uit[0]);
					$nt = count7($ut)-$n;
					
					$us = '';
					if($nt){
						for($i=0;$i<$nt;$i++){
							$us.= $ut[$i].'/';
						}	
					}
					
					$code = str_replace($v, $us.$uit[$n-1], $code);
				}else $code = str_replace($v, $ui, $code);
			}
		}
	}
	
	//头部运作
	//把vs转换为js,css
	//参数：地址
	private function caches_jcc($jcc, $url){
		$this->caches_url_re($jcc, $url);
		
		preg_match_all("/<script.*?src[\s]*=[\s]*[\"\'](.*?)[\"\'].*?<\/script>/is", $jcc, $js);
		preg_match_all("/<link.*?.href[\s]*=[\s]*[\"\'](.*?)[\"\'].*?\/>/is", $jcc, $css);
		
		return array('js'=>$js[1], 'css'=>$css[1]);
		
	}
	
	//临时处理
	private function caches_cct(&$body){
		if(strpos($body, '[@if-')!==false){
			$body = preg_replace(

			"/\[@if-(.+?)@\]/is",
			'<!--if-$1-->',
			$body);
			$body = preg_replace(
			"/\[@\/if-(.+?)@\]/is",
			'<!--/if-$1-->',
			$body);
		}
	}
	//处理替换变量
	private function caches_v(&$body){
		$body = preg_replace(
		"/\"[^\"]+?\"\&=\"([^\"]+?)\"/is",
		'"$1"',
		$body);
	}
	//处理<!--{$v}-->变量
	private function caches_vt(&$body){
		if(strpos($body, '<!--{[')!==false)
		$body = preg_replace(
		"/<!--\{\[(.+?)\]\}-->.+?<!--\/\{\[.+?\]\}-->/is",
		'[$1]',$body);
	
		$body = preg_replace(
		"/<!--\{(.+?)\}-->.+?<!--\/\{.+?\}-->/is",
		'{$1}',$body);
	}
	//处理<!--reset-->变量
	private function caches_reset(&$body){
		if(strpos($body, '<!--/reset-->')!==false){
			preg_match_all("/<!--reset-\{(.+?)\}-->.+?<!--\/reset-->/is", $body, $u);
			
			$val = $u[1][0];
			$body = str_replace('{'.$val.'}','',$body);
			$val = explode(',',$val);
			foreach($val as $v){
				preg_match_all("/\"(.+?)\"=\"(.+?)\"/is", $v, $vs);
				$body = str_replace($vs[1][0],$vs[2][0],$body);
			}
		}
	}
	
	//无嵌套处理
	//处理val变量
	private function caches_val(&$body){
		if(preg_match("/{\\$[A-Za-z0-9_]+.[A-Za-z0-9_]+}/", $body)){
			$r = 1;
			$body = preg_replace(
			"/\{\\$([\w]+?)\.([\w]+?)\}/is",
			'{@8k-{@}$$1[\'$2\']}',
			$body);
		}
		
		$body = preg_replace(
		"/\{\\$(.+?)\}/is",
		'{$this->Val[\'$1\']}',
		$body);
		
		if($r) $body = str_replace('{@8k-{@}','{',$body);
	}
	//处理val变量(if等处理)
	private function caches_valt(&$body){
		if(preg_match("/{\\$[A-Za-z0-9_]+.[A-Za-z0-9_]+}/", $body)){
			$r = 1;
			$body = preg_replace(
			"/\{\\$([\w]+?)\.([\w]+?)\}/is",
			'$$1[\'$2\']',
			$body);
		}
		
		$body = preg_replace(
		"/\{\\$(.+?)\}/is",
		'$this->Val[\'$1\']',
		$body);
	}
	//处理arr变量
	private function caches_arr(&$body){
		$body = preg_replace(
		"/\[@arr-(.+?)-\{\\$(.+?)\}@\]/is",
		'".$this->Arr[\'$1\'][$$2]."',
		$body);
		if(strpos($body, '[@arr-')!==false)
		$body = preg_replace(
		"/\[@arr-(.+?)-\{(.+?)\}@\]/is",
		'".$this->Arr[\'$1\'][$2]."',
		$body);
		if(strpos($body, '[@arr-')!==false)
		$body = preg_replace(
		"/\[@arr-(.+?)-(.+?)@\]/is",
		'".$this->Arr[\'$1\'][\'$2\']."',
		$body);
	}
	
	//嵌套处理
	//处理for变量
	private function caches_logic(&$body, $cnm=''){
		if(!$cnm) $cnm = '$Body';
		else $cnm = '$Inc["'.$cnm.'"]';
		preg_match_all("/<!--(for|if)-(.+?)-->/is", $body, $u);
		$nm = $u[1];
		$va = $u[2];
		foreach($nm as $k=>$n){
			$n = 'caches_'.$n;
			$this->$n($body, $va[$k], $cnm);
		}
	}
	
	private function caches_for(&$body, $nm, $cnm){
        $full_nm = $nm;
		$nm = explode('-',$nm);
		if(count7($nm)>1){
			$rs = $nm[1];
            $key = $nm[2];
            $nm = $nm[0];
		}else{
			$rs = 'rs';
            $key = $nm[2];
			$nm = $nm[0];
		}
		
		$for1 = "<!--for-{$full_nm}-->";
		$for2 = "<!--/for-{$full_nm}-->";
		$for = explode($for1, $body, 2);
		$for = explode($for2, $for[1], 2);
		$for = $for[0];

		$body = preg_replace(
		"/<!--for-{$full_nm}-->(.+?)<!--\/for-{$full_nm}-->/is",
		"<!--for-{$full_nm}-->".$cnm.'.= "$1";'."\n"."<!--/for-{$full_nm}-->",
		$body);


        $f1 = '";'."\n";
        if($key) {
            $keys = explode('.', $key, 2);
            $nm = '"'.$nm.'".$'.$keys[0].'["'.$keys[1].'"]';
        }else $nm = '"'.$nm.'"';
        $f1.= 'if($this->For['.$nm.']){'."\n";
        $f1.= 'foreach($this->For['.$nm.'] as $'.$rs.'){'."\n";
        $body = str_replace($for1, $f1, $body);

        $f2 = '};'."\n";
        $f2.= '};'."\n";
        $f2.= $cnm.'.= "';
        $body = str_replace($for2, $f2, $body);
	
	}
	//处理if变量
	private function caches_if(&$body, $if, $cnm){
		$if = explode('-',$if);
		if(count7($if)>1){
			$nm = $if[0];
			$if_mk = $if[1];
		}else{
			$nm = $if[0];
			$if_mk = '';
		}
		
		if($if_mk) $if_1 = "<!--if-{$nm}-{$if_mk}-->";
		else $if_1 = "<!--if-{$nm}-->";
		$if_2 = "<!--/if-{$nm}-->";
		
		$ifc = explode($if_1, $body, 2);
		$ifc = explode($if_2, $ifc[1], 2);
		$ifc = $ifc[0];
		
		preg_match_all("/<!--else-{$nm}(.*?)-->/is", $ifc, $e);
		$et = array();
		foreach($e[1] as $k=>$v) $et[($k+1)] = str_replace('-','',$v);
		$e[1] = $et;
		$e[1][0] = $if_mk;
		$et = array();
		foreach($e[0] as $k=>$v) $et[($k+1)] = $v;
		$e[0] = $et;
		$e[0][0] = $if_1;
	
		foreach($e[0] as $k=>$v){
			if($e[1][$k]){
				$this->caches_valt($e[1][$k]);
				
				$i = '";'."\n";
				if($k) $i.= '}elseif'.$e[1][$k].'{'."\n";
				else $i.= 'if'.$e[1][$k].'{'."\n";
				$i.= $cnm.'.= "';
				$body = str_replace($v, $i, $body);
				
			}else{
				
				$i = '";'."\n";
				$i.= '}else{'."\n";
				$i.= $cnm.'.= "';
				$body = str_replace($v, $i, $body);	
			}
		}
		
		$i = '";'."\n";
		$i.= '}'."\n";
		$i.= $cnm.'.= "';
		$body = str_replace($if_2, $i, $body);
		
	}
	
	
	//缓存文件操作
	//参数：地址，内容
	private function caches_file($url, $content){
		$url_dir = explode('/', $url);
		array_pop($url_dir);
		
		$urlt = '';
		foreach ($url_dir as $u) {
			if ($u && $u!='..' && $u!='.' && !is_dir(Root.'/'.$urlt.$u))
				mkdir(Root.'/'.$urlt.$u, 0777);
			$urlt.= "$u/";
		}
		
		$fi = fopen(Root.$url,'w');
		if($fi) fputs($fi,$content);
		fclose($fi);
	}
	
	//删除缓存文件操作
	//参数：地址，内容
	private function caches_file_del($url){
		if (file_exists(Root.$url)) unlink(Root.$url);
	}
	
	
	/////////////数据库操作
	
	//页面变量
	//参数：表名
	function val($tnm, $txt1="", $txt2="", $txt3=""){

		if($tnm) {
			if(!$this->db) $this->db();
			if(!$this->field) {
				if(DbPre) $tab = DbPre.$tnm;
				$sql   = "show columns from $tab";
				$files = $this->db->query($sql);
				while ($rs = $files->fetch_assoc()) $fs[] = $rs['Field'];
				$files->free_result();
				$this->field = implode(',',$fs);
			}else $fs = explode(',',$this->field);

			$rs = $this->opsql($tnm);
			if ($rs) foreach ($fs as $d) {
				if( strpos($d, '_info')===false ){//+_info为html格式，不放在标签中
                    $rs[$d] = str_replace("\"", "&#34;", $rs[$d]);
					$rs[$d] = str_replace("'", "&#39;", $rs[$d]); 
				}
				$this->Val[$d] = $rs[$d];
			}
		}
	}
	
	//查询操作返回数组
	//参数：表名
	function opsqli($tnm){
		$this->sql = $this->wrtsql($tnm);

		if(!$this->db) $this->db();
		if ($this->sql) {
			$query = $this->db->query($this->sql);
			$this->sql = '';
			$i = 1;
			if($query){
				while($rs = $query->fetch_assoc()) {
					$rs['i'] = $i;
					$arr[] = $rs;
					$i++;
				}
				$query->free_result();
			}
			return $arr;
		}
		return false;
	}
	
	//查询操作返回数组
	//参数：表名
	function opsql_arr($tnm){
		if(!$this->db) $this->db();
		if(!$this->field) return false;
		else $fs = explode(',',$this->field);
		
		$this->sql = $this->wrtsql($tnm);
		
		if ($this->sql) {
			$query = $this->db->query($this->sql);
			$this->sql = '';
	
			while($rs = $query->fetch_assoc()) {
				$arr[$rs[$fs[0]]] = $rs[$fs[1]];
			}
			$query->free_result();
			
			return $arr;
		}
		return false;
	}

    public function page($tnm){
        if(!$this->db) $this->db();

        $tnm = $this->sqlok($tnm);
        $sql = 'SELECT '.$this->field." FROM $tnm".$this->where.$this->group.$this->having.$this->order.$this->limit;

        $query = $this->db->query($sql);
        $this->sqlcls();

        $base = array();
        if ($query) {

            $i = 1;
            while ($rs = $query->fetch_assoc()) {
                $rs['i'] = $i;
                $base[$i] = $rs;
                $i++;
            }
            $query->free_result();
        }

        return $base;
    }
	
	public function pagei($tnm, $page_num){
		if(!$this->db) $this->db();
		
		$sql = $this->sqlget();
	
		$all_num = $this->rsnum($tnm);
		if(!$all_num) {
		   $this->cls();
		   return array();
		}
		
		$page_max = ceil($all_num/$page_num);
		$page = $_GET['p']?$_GET['p']:$_POST['p'];
		if (!$page || !is_numeric($page)) $page = 1;
		if ($page>$page_max) $page = $page_max;
		
		$this->p['page_max'] = $page_max;
		$this->p['page_num'] = $page_num;
		$this->p['page_now'] = $page;
		$this->p['page_line_num'] = $all_num;
	
		$this->sqlset($sql);
		$this->limit = (($page-1)*$page_num).','.$page_num;
	
		$tnm = $this->sqlok($tnm);
		$sql = 'SELECT '.$this->field." FROM $tnm".$this->where.$this->group.$this->having.$this->order.$this->limit;

		$query = $this->db->query($sql);
		$this->sqlcls();

		$base = array();
		if ($query) {

			$i = 1;
			while ($rs = $query->fetch_assoc()) {
				$rs['i'] = $i+(($page-1)*$page_num);
				$base[$i] = $rs;
				$i++;
			}
			$query->free_result();
		}
		
		return $base;
	}
	
	public function pagei_bot($url, $para='', $md=''){
		if(!$url || !$this->p) return false;
		$url = D.$url;
		$page = $this->p['page_now'];
		$num = $this->p['page_line_num'];
		$page_max = $this->p['page_max'];
		$page_num = 10;
		
		$ul = '<ul class="pagination">';
		
		if($num==0) return $ul.'<li class="disabled"><a href="#">未找到您要的数据！</a></li></ul>';
		if($page_max<=1) return $ul.'<li class="disabled"><a href="#">记录数：'.$num.'</a></li></ul>';
	
		if(!strpos($url,'?')) $url.='?';
		$url.= $para;
		
		$page_n = ceil($page_num/2);
		$page_p = $page_max-$page_n;
	
		if ($page!=1) $pre = '<li class="disabled2"><a href="'.$url.'&p='.($page-1).$md.'">上一页</a></li>';
		else $pre = '<li class="disabled"><a href="javascript:void(0)">上一页</a></li>';
		
		if ($page!=$page_max)  $next = '<li class="disabled2"><a href="'.$url.'&p='.($page+1).$md.'">下一页</a></li>';
		else $next = '<li class="disabled"><a href="javascript:void(0)">下一页</a></li>';
		
		if($page_num>=$page_max) {
			$rt = 1;
			$re = $page_max;
		}elseif($page<=$page_n){
			$rt = 1;
			$re = $page_num;
		}elseif($page>$page_p){
			$rt = $page_max - $page_num + 1;
			$re = $page_max;
		}else{
			$rt = $page-$page_n+1;
			$re = $rt+$page_num-1;	
		}
		
		$list = '';	
		for ($i=$rt; $i<=$re; $i++) {
			if($page==$i) $list.= '<li class="active"><a href="javascript:void(0)">'.$i.'</a></li>';
			else $list.= '<li><a href="'.$url.'&p='.$i.$md.'">'.$i.'</a></li>';
		}
		
		$info = '<li class="disabled"><a href="javascript:void(0)">记录'.$num.'条，页面'.$page.'/'.$page_max.'</a></li>';
		
		return $ul.$pre.$list.$next.$info.'</ul>';
	}
	
	//base型转化为arr型
	//参数：数据元，字段名
	public function base2arr($base, $f){
		if(!count7($base)) return array();
		$f = explode(',',$f);
		foreach($base as $rs){
			$arr[$rs[$f[0]]] = $rs[$f[1]];
		}
		return $arr;
	}
	
	//form_select
	//参数：表
	public function form_select($db, $v=''){
		$sql = $this->wrtsql($db);
		if(!$this->db) $this->db();
		$query = $this->db->query($sql);
		$this->sqlcls();
	
		if ($query) {
			while ($rs = $query->fetch_row()) {
				if($v && $v==$rs[0]) $html.= '<option value="'.$rs[0].'" selected="selected">'.$rs[1].'</option>';
				else $html.= '<option value="'.$rs[0].'">'.$rs[1].'</option>';
			}
			$query->free_result();
		}
		
		return $html;
		
	}

}
?>