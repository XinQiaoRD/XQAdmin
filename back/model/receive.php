<?php
class mv3c_receive{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'receive',//关键字
            //bread;
            'bread' => array(
                'm' => '接待选民管理',
                'receive_add' => '接待选民增加',
                'receive_edit' => '接待选民修改'
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
        $cc->field = "id,title,year,img";
        $cc->For['list'] = $cc->pagei("receive",15);



        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);



        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function receive_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/receive_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function receive_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();


        $cc->sqli("title", $g->title);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("year", $g->year);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("receive", 'add');

        if($g->img) $up->moveFile("/uploads/cache/".$g->img , "/uploads/receive/".$g->img);

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function receive_edit(){

        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("receive");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);


        $cc->view_inc("{$c->nm}/receive_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function receive_edit_op(){

        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("receive");

        $cc->where = "id=".$id;

        $up->sqliCache($g->img, "img", "receive", $rs, $cc, "strs");


        $cc->where = "id=".$id;

        $cc->sqli("year", $g->year);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("title", $g->title);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("receive", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);

    }

    public function receive_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("receive");
        $up->delFile("/uploads/receive/", $rs["img"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("receive", 'del');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    ///////////
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