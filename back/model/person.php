<?php
class mv3c_person{

    public function index(){
        $this->m();
    }

///////////////////////////////////
    private function conf(&$cc){

        $para = $cc->setPara();

        $conf = array(
            'nm' => 'person',//关键字
            //bread;
            'bread' => array(
                'm' => '人大代表管理',
                'person_add' => '人大代表增加',
                'person_edit' => '人大代表修改'
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


        //code
        $g = $cc->get();

        $cc->where = "";
        if($g->area_id){
            $cc->where .= "area_id='".trim($g->area_id)."'";
        }

        $cc->order = "seat";
        $cc->For['list'] = $cc->pagei("person",15);

        $cc->Val['bot'] = $cc->pagei_bot('/'.C.'.php/'.M."/m");

        ///code end

        $cc->view_inc("{$c->nm}/index.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function person_add(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $cc->view_inc("{$c->nm}/person_add.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function person_add_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();

        $cc->sqli("nm", $g->nm);
        $cc->sqli("sex", $g->sex);
        $cc->sqli("birth", $g->birth);
        $cc->sqli("tel", $g->tel);

        $cc->sqli("head", $g->head);
        $cc->sqli("head_size", $g->head_size);
        $cc->sqli("photo", $g->photo);
        $cc->sqli("photo_size", $g->photo_size);
        $cc->sqli("addr", $g->addr);
        $cc->sqli("party", $g->party);
        $cc->sqli("nation", $g->nation);
        $cc->sqli("edu", $g->edu);
        $cc->sqli("work_time", $g->work_time);
        $cc->sqli("work", $g->work);

        $cc->sqli("area_id", $g->area_id);

        $cc->sqli("m_tit", $g->m_tit);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("person", 'add');

        if($g->photo) $up->moveFile("/uploads/cache/".$g->photo , "/uploads/person/".$g->photo);
        if($g->head) $up->moveFile("/uploads/cache/".$g->head , "/uploads/person/".$g->head);

        $cc->go('/'.C.'.php/'.M."/m");
    }

    public function person_edit(){
        $cc = new ccv;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id = ".$id;
        $cc->val("person");

        $cc->view_inc("{$c->nm}/person_edit.html", 'main');
        $this->view_inc($cc, $c);
    }

    public function person_edit_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id=".$id;
        $rs = $cc->opsql("person");

        $up->sqliCache($g->head, "head", "person", $rs, $cc, "strs");
        $up->sqliCache($g->photo, "photo", "person", $rs, $cc, "strs");


        $cc->where = "id=".$id;

        $cc->sqli("nm", $g->nm);
        $cc->sqli("sex", $g->sex);
        $cc->sqli("birth", $g->birth);
        $cc->sqli("tel", $g->tel);

        $cc->sqli("head", $g->head);
        $cc->sqli("head_size", $g->head_size);
        $cc->sqli("photo", $g->photo);
        $cc->sqli("photo_size", $g->photo_size);
        $cc->sqli("addr", $g->addr);
        $cc->sqli("party", $g->party);
        $cc->sqli("nation", $g->nation);
        $cc->sqli("edu", $g->edu);
        $cc->sqli("work_time", $g->work_time);
        $cc->sqli("work", $g->work);

        $cc->sqli("area_id", $g->area_id);

        $cc->sqli("m_tit", $g->m_tit);
        $cc->sqli("m_info", $g->m_info);

        $cc->sqli("seat", $g->seat);

        $cc->opsql("person", 'edit');

        $cc->go('/'.C.'.php/'.M."/m?".$c->para);
    }

    public function person_del_op(){
        $cc = new cc;
        $up = new pekeUpload;
        $c = $this->conf($cc);

        $g = $cc->get();
        $id = $g->id;
        if(!$id) $cc->go( -1 , "ID错误");

        $cc->where = "id='{$id}'";
        $rs = $cc->opsql("person");
        $up->delFile("/uploads/person/", $rs["head"]);
        $up->delFile("/uploads/person/", $rs["photo"]);

        $cc->where = "id='{$id}'";
        $cc->opsql("person", 'del');

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