Blib = {};

Blib.searchHtml = "";
Blib.searchId = "";
Blib.searchHtmls = {};
Blib.searchIds = {};
Blib.searchRow = function(pa){

    if(!pa.placeholder) pa.placeholder = "";

    var html = '<div class="row"><p><label class="col-sm-2 control-label no-padding-right" >' + pa.tit + ': </label>';
    if(pa.type){

        if(pa.type=="select"){
            html+= '<select id="' + pa.id + '">';
            for(var i in pa.val){
                html+= '<option value="' + pa.val[i].val + '">' + pa.val[i].text + '</option>';
            }
            html+= '</select>';
        }

    }else
        html+= '<input id="' + pa.id + '" type="text" class="input-xlarge" placeholder="' + pa.placeholder + '">';

    html+= '</p></div>';
    Blib.searchHtml += html;
    Blib.searchId += pa.id+",";
};


Blib.createSearch = function(id , url){
    Blib.searchHtmls[id] = '<div class="row"><blockquote class="pull-left"><p>搜索</p></blockquote></div>'+Blib.searchHtml;
    Blib.searchHtml = "";

    Blib.searchId = Blib.searchId.substring(0,Blib.searchId.length-1);
    Blib.searchIds[id] = Blib.searchId.split(",");

    $(id).click(function(){
        bootbox.alert(Blib.searchHtmls[id], function(){
            var key = "";
            for(var i in Blib.searchIds[id]){
                key+= "&"+Blib.searchIds[id][i]+"=" + $("#"+Blib.searchIds[id][i]).val();
            }
            if(key) {
                window.location.href=url+key;
            }
        });
    });
};


