<?php
class mv3c_book{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'book',//关键字
            //bread;
            'bread' => array(
                'm' => '台账资料'
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
        if($g->year){
            $cc->where .= "year='".trim($g->year)."'";
        }

        $cc->order = "area_id,seat";
        $cc->field = "id,title,pid,year,area_id";
        $cc->For['list'] = $cc->pagei("word",15);

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