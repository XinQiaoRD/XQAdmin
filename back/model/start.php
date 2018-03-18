<?php
class mv3c_start{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'start',//关键字
            //bread;
            'bread' => array(
                'm' => '简介管理'
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

        $cc->where = "id = 1";
        $cc->val("start");

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function edit_op(){
        $cc = new cc;
        $c = $this->conf($cc);

        $g = $cc->get();

        $cc->where = "id=1";
        $cc->sqli("m_info", $g->m_info);
        $cc->opsql("start", 'edit');

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