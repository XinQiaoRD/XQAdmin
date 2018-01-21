<?php
class mv3c_getdata{

    function index(){

        $up = new pekeUpload;
        $cc = new cc;

        $json = '{"date": "'.$cc->now().'",';

        $cc->order = "year@";
        $rsc = $cc->opsql("year", "rsc");

        while($rs = $cc->rs($rsc)){

            $year_arr[] = $rs["year"];
            $word_arr[] = $this->book($rs["year"], $cc);

        }

        $json_year = implode(",", $year_arr);
        $json.= '
            "year":['.$json_year.'],';

        $json_word = implode(",", $word_arr);
        $json.= '
            "word":{'.$json_word.'},';

        $json.= '
        "download_finish": 0}';


        $up->createJson("/uploads/", "xqtz.json", $json);

        //$cc->go(-1,"更新完成");

    }

    private function book($year, &$cc){

        $json = '
        "'.$year.'":[';

        $cc->where = "year=".$year;
        $cc->order = "seat";
        //$cc->field = "id,pid,title,desc_info";
        $rsc = $cc->opsql("word", "rsc");

        while($rs = $cc->rs($rsc)){
            $arr[] = '{"nm":"xx", "photo":"", "title":"'.$rs["title"].'", "desc_info":"'.$rs["desc_info"].'"}';
        }

        $json_t = "";
        if(count7($arr)){
            $json_t = implode(",", $arr);
        }
        $json.= $json_t.']';

        return $json;
    }


}
?>