<?php
class mv3c_main{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'main',//关键字
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

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    private function view_inc(&$cc, $c){
        $cc->Val['Admin_nm'] = $_SESSION['admin_nm'];
        $cc->Val['Admin_pic'] = 'user'.$_SESSION['admin_tp'];

        $bread = $c->bread;
        $bread = $bread[A];
        $num = count7($bread);
        if($num){
            $i=1;
            foreach($bread as $k=>$v){
                if($num==$i) $bread_html.= '<li class="active"> '.$v.'</li>';
                else $bread_html.= '<li><a href="/'.C.'.php/'.M.'/'.$k.'">'.$v.'</a></li>';
                $i++;
            }
            $cc->Val['Bread'] = $bread_html;
        }

        $cc->view_inc('frame/menu.html');
        $cc->view('frame/main.html');
    }
}
?>