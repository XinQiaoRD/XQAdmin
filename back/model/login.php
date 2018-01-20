<?php
class mv3c_login{

    function index(){
        $cc = new ccv;
        $cc->view('login.html');
    }

    function login_op(){
        $cc = new cc;
        $g = $cc->get();


        if (!$g->username) $cc->go(-1,'帐号填写错误');
        if (!$g->password) $cc->go(-1,'密码填写错误');
    	if (!$g->code) $cc->go('/back.php/login','验证码填写错误');

    	//验证码
    	@session_start();
    	if(strtolower($g->code)!=$_SESSION['mv3c_code']) $cc->go('/back.php/login','验证码填写错误');

        //数据操作
        $cc->where = "username='".$g->username."'";
        $rs = $cc->opsql('admin');
        if ($rs['password']!=hash('md5', $g->password."mv3c") || !$rs['password']) $cc->go(-1,'帐号或密码填写错误');



    	$_SESSION['mv3c_admin']=1;
        $_SESSION['username']=$g->username;
    	$_SESSION['admin_tp']=$rs['tp'];
        $cc->go('/back.php/main');
    }
}
?>