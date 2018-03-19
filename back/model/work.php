<?php
class mv3c_work{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'work',//关键字
            //bread;
            'bread' => array(
                'm' => '述职资料管理',
                'work_add' => '述职资料增加',
                'work_edit' => '述职资料修改',
                'imgs' => '述职图片管理',
                'imgs_add' => '述职图片增加',
                'imgs_edit' => '述职图片修改'
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
        $cc->field = "id,year,nm,m_info";
        $cc->For['list'] = $cc->pagei("work",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function work_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/work_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function work_add_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();


        $cc->sqli("year", $g->year);

        $cc->sqli("nm", $g->nm);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("work", 'add');

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function work_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("work");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);


        $cc->view_inc("{$c->nm}/work_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function work_edit_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");


        $cc->where = "id=".$id;

        $cc->sqli("year", $g->year);

        $cc->sqli("nm", $g->nm);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("work", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function work_del_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $cc->opsql("work", 'del');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }


    /////
    ///
    public function imgs(){
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
        $cc->field = "id,year,img";
        $cc->For['list'] = $cc->pagei("work_img",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/imgs.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/imgs_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();


        $cc->sqli("year", $g->year);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("work_img", 'add');

        if($g->img) $up->moveFile("/uploads/cache/".$g->img , "/uploads/work/".$g->img);

        $cc->go('/'.C.'.php/'.M."/imgs");
    }

    public function imgs_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("work_img");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);


        $cc->view_inc("{$c->nm}/imgs_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function imgs_edit_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("work_img");

        $up->sqliCache($g->img, "img", "work", $rs, $cc, "strs");

        $cc->where = "id=".$id;

        $cc->sqli("year", $g->year);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("work_img", 'edit');

        $cc->go('/'.C.'.php/'.M."/imgs?".$c->para);
    }

    public function imgs_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("work_img");
        $up->delFile("/uploads/work/", $rs["img"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("work_img", 'del');

        $cc->go('/'.C.'.php/'.M."/imgs?".$c->para);
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