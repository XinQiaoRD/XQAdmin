<?php
class mv3c_word{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'word',//关键字
            //bread;
            'bread' => array(
                'm' => '提案管理',
                'word_add' => '提案增加',
                'word_edit' => '提案修改'
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

        $para = $cc->setPara("year,area_id,pid");

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

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m", $para);

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function word_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year");

        $cc->view_inc("{$c->nm}/word_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function word_add_person_ajax(){

        $cc = new ccv;
        $g = $cc->get();

        $cc->where = "area_id=".$g->area_id;
        $cc->field = "id,nm";
        $cc->order = "seat";
        echo $cc->form_select("person");
    }

    public function word_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();

        if(!$g->pid) $cc->go( -1 , "请选择代表");

        $cc->sqli("pid", $g->pid);
        $cc->sqli("area_id", $g->area_id);
        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("desc_info", $g->desc_info);
        $cc->sqli("word_info", $g->word_info);

        $cc->sqli("back_tit", $g->back_tit);
        $cc->sqli("back_info", $g->back_info);

        $cc->sqli("news_tit", $g->news_tit);
        $cc->sqli("news_info", $g->news_info);

        $cc->sqli("news_img1", $g->news_img1);
        $cc->sqli("news_img1_size", $g->news_img1_size);
        $cc->sqli("news_img2", $g->news_img2);
        $cc->sqli("news_img2_size", $g->news_img2_size);
        $cc->sqli("news_img3", $g->news_img3);
        $cc->sqli("news_img3_size", $g->news_img3_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("word", 'add');

        if($g->news_img1) $up->moveFile("/uploads/cache/".$g->news_img1 , "/uploads/word/".$g->news_img1);
        if($g->news_img2) $up->moveFile("/uploads/cache/".$g->news_img2 , "/uploads/word/".$g->news_img2);
        if($g->news_img3) $up->moveFile("/uploads/cache/".$g->news_img3 , "/uploads/word/".$g->news_img3);

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function word_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("word");

        $cc->order = "year@";
        $cc->field = "year,year";
        $cc->Val["year_select"] = $cc->form_select("year", $cc->Val["year"]);

        $cc->order = "seat";
        $cc->where = "area_id=".$cc->Val["area_id"];
        $cc->field = "id,nm";
        $cc->Val["pid_select"] = $cc->form_select("person", $cc->Val["pid"]);

        $cc->view_inc("{$c->nm}/word_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function word_edit_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");
        if(!$g->pid) $cc->go( -1 , "请选择代表");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("word");

        $up->sqliCache($g->news_img1, "news_img1", "word", $rs, $cc, "strs");
        $up->sqliCache($g->news_img2, "news_img2", "word", $rs, $cc, "strs");
        $up->sqliCache($g->news_img3, "news_img3", "word", $rs, $cc, "strs");


        $cc->where = "id=".$id;

        $cc->sqli("pid", $g->pid);
        $cc->sqli("area_id", $g->area_id);
        $cc->sqli("year", $g->year);

        $cc->sqli("title", $g->title);
        $cc->sqli("desc_info", $g->desc_info);
        $cc->sqli("word_info", $g->word_info);

        $cc->sqli("back_tit", $g->back_tit);
        $cc->sqli("back_info", $g->back_info);

        $cc->sqli("news_tit", $g->news_tit);
        $cc->sqli("news_info", $g->news_info);

        $cc->sqli("news_img1", $g->news_img1);
        $cc->sqli("news_img1_size", $g->news_img1_size);
        $cc->sqli("news_img2", $g->news_img2);
        $cc->sqli("news_img2_size", $g->news_img2_size);
        $cc->sqli("news_img3", $g->news_img3);
        $cc->sqli("news_img3_size", $g->news_img3_size);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("word", 'edit');



        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function word_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("word");
        $up->delFile("/uploads/word/", $rs["news_img1"]);
        $up->delFile("/uploads/word/", $rs["news_img2"]);
        $up->delFile("/uploads/word/", $rs["news_img3"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("word", 'del');

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