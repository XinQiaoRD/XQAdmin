<?php
class mv3c_meet{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'meet',//关键字
            //bread;
            'bread' => array(
                'm' => '会议资料管理',
                'meet_add' => '会议资料增加',
                'meet_edit' => '会议资料修改'
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


        //code
        $g = $cc->get();

        $cc->where = "";

        if($g->pid){

            $cc->where .= "pid='".trim($g->pid)."'";
        }else{

            if($g->area_id){
                if($cc->where) $cc->where.= " and ";
                $cc->where .= "area_id='".trim($g->area_id)."'";
            }
        }

        if($g->year){
            if($cc->where) $cc->where.= " and ";
            $cc->where .= "year='".trim($g->year)."'";
        }

        $cc->order = "area_id,seat";
        $cc->field = "id,title,pid,year,area_id";
        $cc->For['list'] = $cc->pagei("meet",15);

        if(count7($cc->For['list'])){
            foreach ($cc->For['list'] as &$arr) {
                $cc->where = "id=".$arr["pid"];
                $cc->field = "nm";
                $rs = $cc->opsql("person");
                $arr["nm"] = $rs["nm"];
            }

            $cc->order = "year@";
            $cc->field = "year";
            $rsc = $cc->opsql("year", "rsc");
            while ($rs = $cc->rs($rsc)){
                $year[] = '{val:"'.$rs["year"].'", text:"'.$rs["year"].'"}';
            }
            $cc->Val["year_sel"] = implode(",", $year);
        }

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m");

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function meet_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/meet_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function meet_add_person_ajax(){

        $cc = new ccv;

        $g = $cc->get();

        $cc->where = "area_id=".$g->area_id;
        $cc->field = "id,nm";
        $cc->order = "seat";
        echo $cc->form_select("person");
    }

    public function meet_add_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();

        if(!$g->pid) $cc->go( -1 , "请选择代表");

        $cc->sqli("pid", $g->pid);
        $cc->sqli("area_id", $g->area_id);
        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("desc_info", $g->desc_info);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("meet", 'add');

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function meet_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("meet");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);

        $cc->order = "seat";
        $cc->where = "area_id=".$cc->Val["area_id"];
        $cc->field = "id,nm";
        $cc->Val["pid_select"] = $cc->form_select("person", $cc->Val["pid"]);

        $cc->view_inc("{$c->nm}/meet_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function meet_edit_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        if(!$g->pid) $cc->go( -1 , "请选择代表");


        $cc->where = "id=".$id;

        $cc->sqli("pid", $g->pid);
        $cc->sqli("area_id", $g->area_id);
        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("desc_info", $g->desc_info);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("meet", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function meet_del_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $cc->opsql("meet", 'del');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    ///////////

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