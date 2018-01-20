<?php
//2018-1-20
//特殊处理
if(get_magic_quotes_gpc()) define('SQL', false);
else define('SQL', true);

class cc{
	//系统参数
	public $db     = '';
	public $sql    = '';
	public $field  = '';
	public $where  = '';
	public $order  = '';
	public $group  = '';
	public $having = '';
	public $limit  = '';
	public $top    = '';
	public $fields = '';
	public $datas  = '';
	//分页函数
	public $p = array();
	
	//数据库连接
	//参数：数据库连接参数
	public function db(){
		$this->db = new mysqli(DbHost, DbRoot, DbPsw, DbNm, DbPost);
		if (mysqli_connect_errno()) die('数据库连接失败,请检查数据库配置变量!');
		$this->db->query("SET NAMES 'utf8'");
	}
	
	//类型判断
	//参数：判断数据,类型
	public function data($da, $mk='str'){
		switch ($mk) {
			case 'strs':
				return true;
			case 'str':
				if ($da) return true;break;
			case 'nums':
				if (is_numeric($da)) return true;break;
			case 'num':
				if ($da!=0 && is_numeric($da)) return true;break;
			case 'date':
				if ($da) {
					$datetime = date('Y-m-d', strtotime($da));
					if($datetime==$da) return true;
				}
				break;
			case 'dates':
				if ($da) {
					$datetime1 = date('Y-m-d', strtotime($da));
					$datetime2 = date('Y-n-j', strtotime($da));
					if($datetime1==$da || $datetime2==$da) return true;
				}
				break;
			case 'time':
				if ($da) {
					$datetime = date('H:i:s', strtotime($da));
					if($datetime==$da) return true;
				}
				break;
			case 'datetime':
				if ($da) {
					$datetime = date('Y-m-d H:i:s', strtotime($da));
					if($datetime==$da) return true;
				}
				break;
			case 'user':
				if (ereg('^[a-zA-Z][a-zA-Z0-9_@.]{1,25}$', $da)) return true;
				break;
		}
		return false;
	}
	
	//数据操作项设定
	//参数：对应字段，数据值，类型
	public function sqli($fi, $da, $mk='str'){
		switch ($mk) {
            case 'strs':
                if (SQL) $da = addslashes($da);
                break;
			case 'str':
				if ($da && SQL) $da = addslashes($da);
				break;
            case 'nums':
                if (!is_numeric($da)) return false;
                break;
            case 'num':
                if ( !$da || !is_numeric($da)) return false;
                break;
			case 'md5':
				if ($da) $da = hash('md5', $da."mv3c");
				break;
			case 'now':
				$da = date('Y-m-d H:i:s');
				break;
			case 'date':
				$da = date('Y-m-d', strtotime($da));
				break;
		}
	
		$this->datas .= "@8k@'$da'";
		$this->fields.= ",$fi";
	
	}
	
	//sql语句组合
	//参数：表名，操作类型
	public function wrtsql($tnm, $mk='select'){
		$tnm = $this->sqlok($tnm);
		switch ($mk) {
			case 'select':
				$sqlt = 'SELECT '.$this->field." FROM $tnm".$this->where.$this->group.$this->having.$this->order.$this->limit;
				break;
			case 'del':
				if (!$this->where) die('wrtsql del where lost');
				$sqlt = "DELETE FROM $tnm".$this->where;
				break;
			case 'add':
				$sqlt        = "INSERT INTO $tnm (".substr($this->fields, 1);
				$sqlt       .= ') VALUES (';
				$this->datas = explode('@8k@', $this->datas);
				$num         = count($this->datas);
				for ($i=1; $i<$num; $i++) $sqlt.= $this->datas[$i].',';
				$sqlt = substr($sqlt, 0, strlen($sqlt)-1);
				$sqlt.= ')';
				break;
			case 'edit':
				if (!$this->where) die('wrtsql edit where lost');
				$sqlt            = "UPDATE $tnm SET ";
				$this->fields = explode(',', $this->fields);
				$this->datas  = explode('@8k@', $this->datas);
				$num          = count($this->fields);
				for ($i=1; $i<$num; $i++) $sqlt.= $this->fields[$i].'='.$this->datas[$i].',';
				$sqlt = substr($sqlt, 0, strlen($sqlt)-1);
				$sqlt.= $this->where.$this->limit;
				break;
			case 'num':
				$sqlt = "SELECT count(*) AS num FROM $tnm".$this->where;
				break;
		}
		$this->sqlcls();
		return $sqlt;
	}
	
	//sql语句操作函数
	//参数：表名，操作类型
	public function opsql($tnm,$mk='select'){
		$sql_mk = $mk;
		switch ($mk) {
			case 'addid':
				$sql_mk = 'add';
				break;
			case 'rsc':
				$sql_mk = 'select';
				break;
		}
	
		$this->sql = $this->wrtsql($tnm, $sql_mk);
		
		if(!$this->db) $this->db();
		if ($this->db && $this->sql) {
			$query = $this->db->query($this->sql);
			
			$this->sql = '';
			if (!$query) return false;
			switch ($mk) {
				case 'select':
					$rs = $query->fetch_assoc();
					$query->free_result;
					return $rs;
					break;
				case 'addid':
					$num = $this->db->insert_id;
					return $num;
					break;
				case 'rsc':
					return $query;
					break;
			}
			if ($query) return true;
		}
		return false;
	}
	
	public function sqlexe($sql){
		if($sql){
			if(!$this->db) $this->db();
			return $this->db->query($sql);
		}
	}
	
	//数据集操作
	//参数：数据集
	public function rs($query){
		if (!$query) return false;
		return $query->fetch_assoc();
	}
	
	//数据集统计
	//参数： 表名,默认字段,操作类型(query型与table型),数据集 
	public function rsnum($tnm='', $query=''){
		if(!$query) {
			//$this->field = $f;
			if ($tnm){
				if(DbPre) $tab = DbPre.$tnm;
                $where = "";
				if($this->where) $where.= " WHERE ".$this->where;
                if($this->group) {
                    $where.= " GROUP BY ".$this->group;
                    $sql = "SELECT count(*) AS num FROM (SELECT * FROM {$tab} {$where} ) as tmp";
                    //$sql = "SELECT count(DISTINCT id) AS num FROM $tab".$where;
                }else{
                    $sql = "SELECT count(*) AS num FROM $tab".$where;
                }
				$this->where = '';
                $this->group = '';
				$query =  $this->sqlexe($sql);
				if($query) $rs = $query->fetch_assoc();
				else $rs['num']=0;
				$query->free_result;

				return $rs['num'];
			}
		}else{
			$num = $query->num_rows;
			//$query->free_result;
			return $num;
		}
	}
	
	//集体取值
	//参数：对应名字组
	public function get($para=''){
		$get = array();
		if($para){
			$para = str_replace(',',';',$para);
			$para_arr = explode(';', $para);
			if(count($para_arr)==1){
                $data = $this->datatrim($para_arr[0]);
                if (isset($_POST[$data]))
                    return $_POST[$data];
                else if (isset($_GET[$data]))
                    return $_GET[$data];
            }
            foreach ($para_arr as $data) {
                $data = $this->datatrim($data);
                if (isset($_POST[$data]))
                    $get[$data] = $_POST[$data];
                else if (isset($_GET[$data]))
                    $get[$data] = $_GET[$data];
            }

		}else{
		   if(isset($_GET)) $get = array_merge($get, $_GET);
		   if(isset($_POST)) $get = array_merge($get, $_POST);
		}
		return (object)$get;
	}
	
	//跳转函数
	//参数：转向地址, 提示语, 转向对象, 是否关闭
	public function go($go, $word='', $dom='document', $exit=1){
		$go_htm = '<script language="javascript">';
		if ($word) $go_htm.= "alert(\"$word\");";
	
		if (is_numeric($go)) $go_htm.= "history.go($go);";
		else {
            if(strpos($go,"ttp://")) $d = "";
            else $d = D;
            $go = str_replace("\"", "&#34;", $d.$go);
            $go = str_replace("'", "&#39;", $go);
            $go_htm.= "$dom.location.href=\"{$go}\";";
        }
	
		if (!$exit) $go_htm.= 'window.close();';
		$go_htm.= '</script>';
		$this->cls();
		echo $go_htm;
		exit;
	}
	
	//清理函数
	public function cls(){
		if ($this->db) @$this->db->close();
	}
	
	//去掉前后空格及换行符等
	public function datatrim($str){
		$str     = trim($str);
		$str_num = strlen($str);
		$str     = substr($str, 0, $str_num);
		return $str;
	}
		
	/* 模块 : sql预处理 */
	public function sqlok($tb){
		$this->field  = $this->field ? ' '.$this->field : ' *';
		$this->where  = $this->where ? ' WHERE '.$this->where : '';
		$this->order  = $this->order ? ' ORDER BY '.str_replace('@',' DESC',$this->order) : '';
		$this->group  = $this->group ? ' GROUP BY '.$this->group : '';
		$this->having = $this->having? ' HAVING '.$this->having : '';
		$this->limit  = $this->limit ? ' LIMIT '.$this->limit : '';
		$this->limit  = $this->top   ? ' LIMIT '.$this->top : $this->limit;//top优先级高于$limit
		
		if($tb){
			if(strpos($tb, '@')!==false) return str_replace('@', '', $tb);
			
			if(DbPre) return DbPre.$tb;
			else return $tb;
		}
	}
	public function sqlcls(){
		$this->field  = '';
		$this->where  = '';
		$this->group  = '';
		$this->having = '';
		$this->order  = '';
		$this->limit  = '';
		$this->top    = '';
		$this->fields = '';
		$this->datas  = '';
	}
	/* 模块 : 转移函数 */
	public function sqlget(){
		$sql[0] = $this->field;
		$sql[1] = $this->where;
		$sql[2] = $this->order;
		$sql[3] = $this->group;
		$sql[4] = $this->having;
		$sql[5] = $this->limit;
		$sql[6] = $this->top;
		$sql[7] = $this->fields;
		$sql[8] = $this->datas;
		
		return $sql;
	}
	public function sqlset($sql){
		$this->field = $sql[0];
		$this->where = $sql[1];
		$this->order = $sql[2];
		$this->group = $sql[3];
		$this->having= $sql[4];
		$this->limit = $sql[5];
		$this->top   = $sql[6];
		$this->fields= $sql[7];
		$this->datas = $sql[8];
	}
	
	//时间函数
	public function now($mk='now'){
		switch ($mk){
			case 'now': return date('Y-m-d H:i:s');
			case 'date': return date('Y-m-d');
			case 'time': return date('H:i:s');
		}	
	}
	
	
	//分页传递处理
    public function setPara($key="", $p=1){
        $cc_vals = $_GET['cc_vals'];
        $cc_vals = str_replace("*p*", "", $cc_vals);
        $px = substr($cc_vals, -2);
        if($px=="*p") $cc_vals = str_replace("*p*", "", $cc_vals."*");

        $key = explode(',',$key);
        $fis = explode('*',$cc_vals);

        foreach($key as $fi){
            $fi = trim($fi);
            if(!in_array($fi , $fis)) $cc_vals.= "*{$fi}";
        }
        if($p) if(!in_array("p" , $fis)) $cc_vals.= "*p";

        $_GET['cc_vals'] = $cc_vals;
	    return $this->getPara();
    }

    public function getPara($mk=0){
        $cc_vals = $_GET['cc_vals'];
        $cc_val = "";
        if($cc_vals) {
            if(!$mk) $cc_val = "&cc_vals={$cc_vals}";
            $fis = explode('*',$cc_vals);
            foreach($fis as $fi){
                $fi = trim($fi);
                if($fi) $cc_val.= "&{$fi}=".$_GET[$fi];
            }
        }
        $this->Val['para'] = $cc_val;
        return $cc_val;
    }


	public function pr(){
		$p = $_GET['p'];
		if($p) $p = "&p={$p}";
		else $p = '';
		return $p;
	}
	
	public function para($w='', $p=1){
        $cc_val = "";
        $cc_vals = "";

		if($w){
		    $p = 0;
			$w = explode(',',$w);
			foreach($w as $fi){
                $fi = trim($fi);
                $cc_val.= "&{$fi}=".$_GET[$fi];
                $cc_vals.= "*{$fi}";
			}
		}else{
            $cc_vals = $_GET['cc_vals'];
			if($cc_vals) {
                $fis = explode('*',$cc_vals);
                foreach($fis as $fi){
                    $fi = trim($fi);
                    if($fi) $cc_val.= "&{$fi}=".$_GET[$fi];
                }
            }
		}

        $cc_val.= "&cc_vals={$cc_vals}";
		if($p) $cc_val.= $this->pr();
		$this->Val['cc_val'] = $cc_val;
		return $cc_val;
	
	}

    public function bug( $txt ){
        echo "<script>console.log('{$txt}');</script>";
    }

    public function curl_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return $output;
    }

    public function curl_post($url, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return $output;
    }

}

//自动加载
function __autoload($className){
	if(strpos($className, 'cc_')!==false) $c = 'mv3c';
	else $c = C;

	if(!is_file(Root.'/'.$c."/class/{$className}.php"))
		die('/'.$c."/class/{$className}.php 类文件不存在");
	include(Root.'/'.$c."/class/{$className}.php");
}

//PHP7替代
function count7($array_or_countable,$mode = COUNT_NORMAL){
    if(is_array($array_or_countable) || is_object($array_or_countable)){
        return count($array_or_countable, $mode);
    }else{
        return 0;
    }
}
?>