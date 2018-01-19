function PekeUP (pa) {

    this.pa = pa;
    if(pa.type == "img") this._imgUp();
    if(pa.type == "mp3") this._mp3Up();
    if(pa.type == "video") this._videoUp();

}

PekeUP.prototype._ini = function(nm){

    var id = this.pa.id;
    this.$box = $('#'+id);
    this.$box.append('<input class="pekeFile" type="file" name="'+id+'PekeFile" /><input class="pekeInput" type="hidden" name="'+id+'" /><input class="pekeInputSize" type="hidden" name="'+id+'_size" />');
    this.$file = this.$box.find('.pekeFile');
    this.$input = this.$box.find(".pekeInput");
    this.$inputSize = this.$box.find(".pekeInputSize");
    this.MB = "MB";


    if(!this.pa.data) this.pa.data = {};
    this.pa.data.filename = id+'PekeFile';

    if(!this.pa.name) this.pa.name = nm+"上传";

    this.options = {
        bootstrap:true,
        btnText:this.pa.name,
        invalidExtError:"文件类型错误",
        errorOnResponse:"文件上传错误！",
        url:this.pa.url,
        delfiletext:"删除"
    };

    if(this.pa.size!==0){

        if(!this.pa.size) this.pa.size = 2;
        this.size = this.pa.size;
        if(this.pa.size<1) {
            this.size = this.pa.size*1080;
            this.MB = "KB"
        }

        this.options.maxSize = this.pa.size*1080*1080;
        this.options.sizeError = nm+"不能大于"+this.size+this.MB;
    }

    if(this.pa.data) this.options.data = this.pa.data;

};

PekeUP.prototype._imgUp = function(){

    this._ini("图片");
    this.options.allowedExtensions = "png|jpg|gif";
    this.options.limit = 1;
    this.options.limitError = "支持1张图片上传，请删除后重新上传";
    this.options.notAjax = false;

    this.options.onFileSuccess = this._success.bind(this);
    this.options.onFileError = function(file,error){
        console.log(file, error);
        if(this.pa.error) this.pa.error(file, error);
    };
    this.options.onDelFile = this._del.bind(this);

    if(this.pa.checkWH) this.options.checkWH = this._checkWH.bind(this);


    this.$file.attr("accept" , "image/png,image/jpeg,image/gif");
    this.$pk = this.$file.pekeUpload(this.options);

    if(this.pa.edit){

        var dom = $('<a href="'+this.pa.editUrl+this.pa.edit+'" target="_blank"><img class="thumbnail" src="'+this.pa.editUrl+this.pa.edit+'" height="64" /></a>');
        this._edit(dom);
    }

};

PekeUP.prototype._mp3Up = function(){

    this._ini("MP3");
    this.options.allowedExtensions = "mp3";
    this.options.limit = 1;
    this.options.limitError = "支持1支MP3上传，请删除后重新上传";
    this.options.showFilename = false;
    this.options.notAjax = false;

    this.options.onFileSuccess = this._success.bind(this);
    this.options.onFileError = function(file,error){
        console.log(file, error);
        if(this.pa.error) this.pa.error(file, error);
    };
    this.options.onDelFile = this._del.bind(this);

    if(this.pa.checkWH) this.options.checkWH = this._checkWH.bind(this);


    this.$file.attr("accept" , "audio/mpeg");
    this.$pk = this.$file.pekeUpload(this.options);

    if(this.pa.edit){

        var dom = $('<audio src="' + this.pa.editUrl+this.pa.edit + '" width="100%" controls></audio>');
        this._edit(dom, "null");

    }

};

PekeUP.prototype._videoUp = function(){

    this.pa.size = 0;
    this._ini("视频");
    this.options.allowedExtensions = "mp4";
    this.options.limit = 1;
    this.options.limitError = "支持1支MP4上传，请删除后重新上传";
    this.options.notAjax = false;
    //this.options.showFilename = false;

    this.options.onFileSuccess = this._success.bind(this);
    this.options.onFileError = function(file,error){
        console.log(file, error);
        if(this.pa.error) this.pa.error(file, error);
    };
    this.options.onDelFile = this._del.bind(this);

    if(this.pa.checkWH) this.options.checkWH = this._checkWH.bind(this);


    this.$file.attr("accept" , "video/mp4");
    this.$pk = this.$file.pekeUpload(this.options);

    if(this.pa.edit){

        var dom = $('<video src="' + this.pa.editUrl+this.pa.edit + '" width="100%" controls>');
        this._edit(dom);

    }

};

PekeUP.prototype._success = function(file, data, index){
    this.$input.val(data.name);
    this.$inputSize.val(data.size);

    var $del = this.$box.find(".pkdelIndex"+index);

    $del.data("name", data.name);
    $del.click(function(){
        this.options.onDelFile($del);
        this.$pk.delAndRearrange(this.$box.find(".pkRow"+index));
    }.bind(this));

    if(this.DEL){
        this.options.onDelFile($del);
        this.$pk.delAndRearrange(this.$box.find(".pkRow"+index));
        this.DEL = 0;
    }

    if(this.pa.edit) this.$box.find(".rowDefault").remove();
    if(this.pa.success) this.pa.success(file, data);

};

PekeUP.prototype._del = function(file, index){
    this.$input.val("");
    this.$inputSize.val("");
    this.$file.val("");
    if(this.pa.delUrl) $.get(this.pa.delUrl,{ file: file.data("name") });
    if(this.pa.del) this.pa.del(file, index);
};

PekeUP.prototype._checkWH = function(img, index){
    this.$box.append('<div id="checkWH" style="width: 0; height: 0; overflow: hidden"></div>');
    $("#checkWH").html(img);
    img.on("load",function(){
        var w = img.width(),
            h = img.height();

        if(this.pa.checkWH.com){
            if(this.pa.checkWH.com=="<"){
                if(this.pa.checkWH.w <= w || this.pa.checkWH.h <= h) {
                    this.$pk.addWarning("图片大小必须要小于等于 ( 宽度："+this.pa.checkWH.w+"px , 高度："+this.pa.checkWH.h+"px ) ", this.$pk.obj);
                    this.DEL = 1;
                }
            }
            if(this.pa.checkWH.com==">"){
                if(this.pa.checkWH.w >= w || this.pa.checkWH.h >= h) {
                    this.$pk.addWarning("图片大小必须要大于等于 ( 宽度："+this.pa.checkWH.w+"px , 高度："+this.pa.checkWH.h+"px ) ", this.$pk.obj);
                    this.DEL = 1;
                }
            }
        }else{
            if(this.pa.checkWH.w != w || this.pa.checkWH.h != h) {
                this.$pk.addWarning("图片大小必须为 ( 宽度："+this.pa.checkWH.w+"px , 高度："+this.pa.checkWH.h+"px ) ", this.$pk.obj);
                this.DEL = 1;
            }
        }

    }.bind(this));

};

PekeUP.prototype._edit = function(dom, nm){
    this.$input.val(this.pa.edit);
    if(this.pa.editSize) this.$inputSize.val(this.pa.editSize);

    var newRow = $('<div class="row pkrw rowDefault"></div>').appendTo(this.$box.find(".pekecontainer"));
    var prev = $('<div class="col-lg-2 col-md-2 col-xs-4"></div>').appendTo(newRow);
    dom.appendTo(prev);


    var finfo = $('<div class="col-lg-8 col-md-8 col-xs-8"></div>').appendTo(newRow);
    if(!nm) finfo.append('<div class="filename">' + this.pa.edit + "</div>");


    dismiss = $('<div class="col-lg-2 col-md-2 col-xs-2"></div>').appendTo(newRow);
    var $del = $('<a href="javascript:void(0);" class="btn btn-danger pkdel">删除</a>');
    $del.appendTo(dismiss);
    $del.click(function(){
        this.$box.find(".rowDefault").remove();
        this.$input.val("");
        this.$inputSize.val("");
    }.bind(this));
};