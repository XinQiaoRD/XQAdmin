<?php
class mv3c_inspect{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'inspect',//关键字
            //bread;
            'bread' => array(
                'm' => '代表督查资料管理',
                'add' => '代表督查资料增加',
                'edit' => '代表督查资料修改',
                'imgs' => '代表督查图片管理',
                'imgs_add' => '代表督查图片增加',
                'imgs_edit' => '代表督查图片修改'
            ),
            'para' => $para
        );

        ////////
        $cc->Val['C'] = C.'.php';

        return (object)$conf;
    }

    public function m(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $para = $cc->setPara("year");

        //code
        $g = $cc->get();

        $year = [];
        $cc->order = "year@";
        $cc->field = "year";
        $rsc = $cc->opsql("year", "rsc");
        while ($rs = $cc->rs($rsc)){
            $year[] = '{val:"'.$rs["year"].'", text:"'.$rs["year"].'"}';
        }
        $cc->Val["year_sel"] = implode(",", $year);


        $cc->where = "";

        if($g->year){
            if($cc->where) $cc->where.= " and ";
            $cc->where .= "year='".trim($g->year)."'";
        }

        $cc->order = "year@,seat";
        $cc->field = "id,year,title,m_info";
        $cc->For['list'] = $cc->pagei("inspect",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function add_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();


        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("inspect", 'add');

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("inspect");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);


        $cc->view_inc("{$c->nm}/edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function edit_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");


        $cc->where = "id=".$id;

        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("inspect", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function del_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $cc->opsql("inspect", 'del');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }


    /////
    ///
    public function imgs(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $para = $cc->setPara("pid");

        //code
        $g = $cc->get();


        $cc->where = "pid='".trim($g->pid)."'";

        $cc->order = "year@,seat";
        $cc->field = "id,pid,year,img";
        $cc->For['list'] = $cc->pagei("inspect_img",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/imgs.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $para = $cc->setPara("pid");

        $cc->view_inc("{$c->nm}/imgs_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $para = $cc->setPara("pid");

        $g = $cc->get();

        if(!$g->pid) $cc->go( -1 , "PID错误");

        $cc->where = "id=".$g->pid;
        $rs = $cc->opsql("inspect");

        $cc->sqli("year", $rs["year"]);
        $cc->sqli("pid", $g->pid);
        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("inspect_img", 'add');

        if($g->img) $up->moveFile("/uploads/cache/".$g->img , "/uploads/inspect/".$g->img);

        $cc->go('/'.C.'.php/'.M."/imgs?".$para);
    }

    public function imgs_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);
        $para = $cc->setPara("pid");

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        if(!$g->pid) $cc->go( -1 , "PID错误");

        $cc->where = "id = ".$id;
        $cc->val("inspect_img");


        $cc->view_inc("{$c->nm}/imgs_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_edit_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);
        $para = $cc->setPara("pid");

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        if(!$g->pid) $cc->go( -1 , "PID错误");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("inspect_img");

        $up->sqliCache($g->img, "img", "inspect", $rs, $cc, "strs");


        $cc->where = "id=".$id;

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("inspect_img", 'edit');

        $cc->go('/'.C.'.php/'.M."/imgs?".$para);
    }

    public function imgs_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);
        $para = $cc->setPara("pid");

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        if(!$g->pid) $cc->go( -1 , "PID错误");

        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("inspect_img");
        $up->delFile("/uploads/inspect/", $rs["img"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("inspect_img", 'del');

        $cc->go('/'.C.'.php/'.M."/imgs?".$para);
    }

    ///////////
    ///
    public function ImgUpload(){

        $cc = new cc;
        $g = $cc->get();

        $up = new pekeUpload;
        echo $up->upImg($g->filename , '/uploads/cache/');

    }

    public function Mp3Upload(){

        $cc = new cc;
        $g = $cc->get();

        $up = new pekeUpload;
        echo $up->upMp3($g->filename , '/uploads/cache/');

    }

    public function DelCache(){
        $cc = new cc;
        $g = $cc->get();

        $up = new pekeUpload;
        $up->delFile('/uploads/cache/', $g->file);
    }

    private function view_inc(&$cc, $c){
        $cc->Val['Admin_nm'] = $_SESSION['admin_nm'];
        $cc->Val['Admin_pic'] = 'user'.$_SESSION['admin_tp'];

        $bread = $c->bread;
        foreach($bread as $k=>$v){
            $cc->Val['menu_'.$k] = $v;
        }
        $bread = $bread[A];
        $bread_html = '<li class="active"> '.$bread.'</li>';

        $bread_parent = "";
        if($c->parent){
            foreach($c->parent as $k=>$v){
                $bread_parent.= '<li><a href="'.D.'/'.C.'.php/'.$v.'">'.$k.'</a></li>';
            }
        }

        $cc->Val['Bread'] = $bread_parent.$bread_html;

        $cc->view_inc('frame/menu.html');
        $cc->view('frame/main.html');
    }
}
?>