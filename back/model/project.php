<?php
class mv3c_project{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'project',//关键字
            //bread;
            'bread' => array(
                'm' => '民生实事管理',
                'project_add' => '民生实事增加',
                'project_edit' => '民生实事修改'
            ),
            'para' => $para
        );

        ////////
        $cc->Val['C'] = C.'.php';

        return (object)$conf;
    }

    //人大代表
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
        $cc->field = "id,year,title, m_info, work";
        $cc->For['list'] = $cc->pagei("project",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function project_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/project_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function project_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();

        $cc->sqli("title", $g->title);
        $cc->sqli("year", $g->year);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("money", $g->money);
        $cc->sqli("m_info", $g->m_info);
        $cc->sqli("time_info", $g->time_info);
        $cc->sqli("pro_info", $g->pro_info);
        $cc->sqli("work", $g->work);
        $cc->sqli("lead", $g->lead);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("project", 'add');

        if($g->photo) $up->moveFile("/uploads/cache/".$g->photo , "/uploads/project/".$g->photo);

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function project_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("project");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);

        $cc->view_inc("{$c->nm}/project_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function project_edit_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("project");

        $up->sqliCache($g->img, "img", "project", $rs, $cc, "strs");


        $cc->where = "id=".$id;

        $cc->sqli("title", $g->title);
        $cc->sqli("year", $g->year);

        $cc->sqli("img", $g->img);
        $cc->sqli("img_size", $g->img_size);

        $cc->sqli("money", $g->money);
        $cc->sqli("m_info", $g->m_info);
        $cc->sqli("time_info", $g->time_info);
        $cc->sqli("pro_info", $g->pro_info);
        $cc->sqli("work", $g->work);
        $cc->sqli("lead", $g->lead);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("project", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function project_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("project");
        $up->delFile("/uploads/project/", $rs["img"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("project", 'del');

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