<?php
class mv3c_message{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'message',//关键字
            //bread;
            'bread' => array(
                'm' => '议政会资料管理',
                'message_add' => '议政会资料增加',
                'message_edit' => '议政会资料修改'
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
        $cc->field = "id,year,title,m_info,work";
        $cc->For['list'] = $cc->pagei("message",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function message_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/message_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function message_add_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();


        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("back_info", $g->back_info);
        $cc->sqli("m_info", $g->m_info);
        $cc->sqli("work", $g->work);
        $cc->sqli("lead", $g->lead);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("message", 'add');

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function message_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("message");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);


        $cc->view_inc("{$c->nm}/message_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function message_edit_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");


        $cc->where = "id=".$id;

        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("back_info", $g->back_info);
        $cc->sqli("m_info", $g->m_info);
        $cc->sqli("work", $g->work);
        $cc->sqli("lead", $g->lead);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("message", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function message_del_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $cc->opsql("message", 'del');

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