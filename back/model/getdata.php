<?php
class mv3c_getdata{

    function tz(){

        $up = new pekeUpload;
        $cc = new cc;


        //台账
        $json = '{"date": "'.$cc->now().'",';

        //year
        $arr_year = [];
        $arr_year_word = [];

        $cc->order = "year@";
        $rsc = $cc->opsql("year", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_year[] = $rs["year"];
            $arr_year_word[] = $this->arr_year_word($rs["year"], $cc);

        }
        $json_year = implode(",", $arr_year);
        $json.= '
            "year":['.$json_year.'],';

        $json_year_word = implode(",", $arr_year_word);
        $json.= '
            "year_word":{'.$json_year_word.'},';


        //word提案
        $arr_word = [];
        $cc->order = "id";
        $rsc = $cc->opsql("word", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_word[] = $this->arr_word($cc, $rs);
        }
        $json_word = implode(",", $arr_word);
        $json.= '
            "word":{'.$json_word.'},';


        //end
        $json.= '
        "download_finish": 0}';

        $up->createJson("/uploads/", "tz_data.json", $json);


        $cc->go(-1,"更新完成");

    }

    function rd(){

        $up = new pekeUpload;
        $cc = new cc;


        //人大代表
        $json = '{"date": "'.$cc->now().'",';

        //area
        $arr_area_person = [];
        for($i=1; $i<=8; $i++){

            $arr_area_person[$i] = '
            "'.$i.'":{';

            //区域人数
            $cc->where = "id=".$i;
            $ars = $cc->opsql("area");
            if(!$ars["num"]) $ars["num"]=0;
            $arr_area_person[$i].= '"num":'.$ars["num"].',';

            //代表
            $cc->where = "area_id=".$i;
            $cc->order = "seat";
            $cc->field = "id";
            $prsc = $cc->opsql("person", "rsc");
            $arr_area_person[$i].= '"person":[';
            $arr_t = [];
            while($prs = $cc->rs($prsc)){
                $arr_t[] = $prs["id"];
            }
            $arr_t_w = implode(",", $arr_t);
            $arr_area_person[$i].= $arr_t_w.']';

            $arr_area_person[$i].= '}';

        }
        $json_area_person = implode(",", $arr_area_person);
        $json.= '
            "area_person":{'.$json_area_person.'},';

        //代表
        $arr_person = [];
        $cc->order = "id";
        $rsc = $cc->opsql("person", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_person[] = $this->arr_person($cc, $rs);
        }
        $json_person = implode(",", $arr_person);
        $json.= '
            "person":{'.$json_person.'},';

        //word提案
        $arr_word = [];
        $cc->order = "id";
        $rsc = $cc->opsql("word", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_word[] = $this->arr_word($cc, $rs);
        }
        $json_word = implode(",", $arr_word);
        $json.= '
            "word":{'.$json_word.'},';

        //act社会活动
        $arr_act = [];
        $cc->order = "id";
        $rsc = $cc->opsql("act", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_act[] = $this->arr_word($cc, $rs);
        }
        $json_act = implode(",", $arr_act);
        $json.= '
            "act":{'.$json_act.'},';

        //meet会议资料
        $arr_meet = [];
        $cc->order = "id";
        $rsc = $cc->opsql("meet", "rsc");
        while($rs = $cc->rs($rsc)){
            $arr_meet[] = $this->arr_word($cc, $rs);
        }
        $json_meet = implode(",", $arr_meet);
        $json.= '
            "meet":{'.$json_meet.'},';


        //end
        $json.= '
        "download_finish": 0}';

        $up->createJson("/uploads/", "db_data.json", $json);

        $cc->go(-1,"更新完成");

    }

    private function arr_year_word($year, &$cc){

        $json = '
        "'.$year.'":[';

        $cc->where = "year=".$year;
        $cc->order = "seat";
        $cc->field = "id";
        $rsc = $cc->opsql("word", "rsc");

        $arr = [];
        while($rs = $cc->rs($rsc)){
            $arr[] = $rs["id"];
        }

        $json_t = implode(",", $arr);
        $json.= $json_t.']';

        return $json;
    }

    private function arr_word(&$cc, &$rs){

        $json = '
        "'.$rs["id"].'":';

        $json.= $this->word_rsToJson($cc, $rs);

        return $json;
    }

    private function word_rsToJson(&$cc, &$rs){

        //add
        $cc->where = "id=".$rs["pid"];
        $cc->field = "nm,photo";
        $prs = $cc->opsql("person");
        $rs["nm"] = $prs["nm"];
        $rs["photo"] = $prs["photo"];
        //add

        $row = '{';
        $row_arr = [];
        foreach($rs as $key => $value){
            $row_arr[] = '
                "'.$key.'":"'.addslashes($value).'"';
        }
        $row_arr_w = implode(",", $row_arr);

        $row.= $row_arr_w;
        $row.= '}';

        return $row;
    }

    private function arr_person(&$cc, &$rs){

        $json = '
        "'.$rs["id"].'":{"person":';

        $json.= $this->rsToJson($rs);
        $json.= ',
        "word":[';
        $cc->where = "pid=".$rs["id"];
        $cc->order = "seat";
        $cc->field = "id";
        $rsc = $cc->opsql("word", "rsc");
        $arr = [];
        while($rsi = $cc->rs($rsc)){
            $arr[] = $rsi["id"];
        }
        $arr_t = implode(",", $arr);
        $json.= $arr_t;

        $json.= '],
        "act":[';
        $cc->where = "pid=".$rs["id"];
        $cc->order = "seat";
        $cc->field = "id";
        $rsc = $cc->opsql("act", "rsc");
        $arr = [];
        while($rsi = $cc->rs($rsc)){
            $arr[] = $rs["id"];
        }
        $arr_t = implode(",", $arr);
        $json.= $arr_t;


        $json.= '],
        "meet":[';
        $cc->where = "pid=".$rs["id"];
        $cc->order = "seat";
        $cc->field = "id";
        $rsc = $cc->opsql("meet", "rsc");
        $arr = [];
        while($rsi = $cc->rs($rsc)){
            $arr[] = $rs["id"];
        }
        $arr_t = implode(",", $arr);
        $json.= $arr_t;

        $json.= ']}';
        return $json;
    }

    private function rsToJson(&$rs){


        $row = '{';
        $row_arr = [];
        foreach($rs as $key => $value){
            $row_arr[] = '
                "'.$key.'":"'.addslashes($value).'"';
        }
        $row_arr_w = implode(",", $row_arr);

        $row.= $row_arr_w;
        $row.= '}';

        return $row;
    }

    private function rscToJson(&$cc, &$rsc){
        $arr = [];
        while($rs = $cc->rs($rsc)){

            $row = '{';
            $row_arr = [];
            foreach($rs as $key => $value){
                $row_arr[] = '
                "'.$key.'":"'.addslashes($value).'"';
            }
            $row_arr_w = implode(",", $row_arr);

            $row.= $row_arr_w;
            $row.= '}';

            $arr[] = $row;
        }

        $json = implode(",", $arr);

        return $json;
    }


}
?>