<?php
class mv3c_year{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'year',//关键字
            //bread;
            'bread' => array(
                'm' => '年份管理',
                'year_add' => '年份增加'
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

        $cc->order = "year@";
        $cc->For['list'] = $cc->page("year");

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function year_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->view_inc("{$c->nm}/year_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function year_add_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();

        $cc->sqli("year", $g->year);

        $cc->opsql("year", 'add');


        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function year_del_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $cc->opsql("year", 'del');

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