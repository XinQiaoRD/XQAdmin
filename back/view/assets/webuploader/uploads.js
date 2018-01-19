function webup(pa){

    var type = $('div.type'+pa.id);

    if(!pa.id) type.removeClass('hide').text('缺少id参数！');
    if(!pa.url) type.removeClass('hide').text('缺少url参数！');
    if(!pa.tp) pa.tp = 'img';
    if(pa.tp == 'img'){
        pa.nm = '上传图片'
    }
    else if(pa.tp == 'video'){
        pa.nm = '上传视频'
    }
    else if(pa.tp == 'zip'){
        pa.nm = '上传压缩包'
    }

    if(!pa.width) pa.width = 100;
    if(!pa.height) pa.height = 100;

    var option = {

        // 文件接收服务端。
        server: pa.url,

        // 选择文件的按钮。可选。
        pick: {
            id: '#picker'+ pa.id,
            innerHTML: pa.nm,
            multiple: false
        },

        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
        resize: false ,
        auto : true ,
        threads: 1,//上传并发数，默认为3。
        fileNumLimit : 1,//验证文件总数量，超出则不允许加入队列。
        compress: null
    };
    if(pa.tp=='img'){
        option.fileSingleSizeLimit = 500 * 1024 * 1024;
        option.accept = {
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        };
    }
    else if(pa.tp=='video'){
        option.fileSingleSizeLimit = 5000 * 1024 * 1024;
        option.accept = {
            extensions: 'ogv,mp3,webm,mov,mp4',
            mimeTypes: '.ogv,.mp3,.webm,.mov,.mp4'
        };
    }
    else if(pa.tp=='zip'){
        option.fileSingleSizeLimit = 5000 * 1024 * 1024;
        option.accept = {
            extensions: 'zip',
            mimeTypes: '.zip'
        };
    }


    var uploader = new WebUploader.create(option);



    uploader.on( 'fileQueued', function( file ) {
        $('#thelist' + pa.id + ' div').remove();
        type.addClass('hide');

        var li = $(
                '<div id="' + file.id + '">' +
                    '<img id="show_img' + pa.id + '">' +
                    '<div class="info">' + file.name + '</div>' +
                    '<a href="javascript:void(0)" class="delete" data-id="' + pa.id + '">删除</a>' +
                    '<input type="hidden" value="" id="file_id'+ pa.id +'" name="'+ pa.name +'">' +
                '</div>'
            ),
            img = li.find('img');

        $('#thelist' + pa.id).append( li );

//        删除
        var delete_bt = $('#thelist' + pa.id + ' .delete');
        delete_bt.click(function(){
            $('#thelist' + pa.id + ' div').remove();
            type.addClass('hide');
            uploader.reset();
        });

        uploader.makeThumb( file, function( error, src ) {
            if ( error ) {
                img.replaceWith('<span>不能预览</span>');
                return;
            }
            img.attr( 'src', src );
        }, pa.width, pa.height );
    });

    uploader.on( 'uploadProgress', function( file, percentage ) {
        var li = $( '#'+file.id ),
            percent = li.find('.progress .progress-bar');

        // 避免重复创建
        if ( !percent.length ) {
            percent = $('<div class="progress progress-striped active">' +
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                '</div>' +
                '</div>').appendTo( li ).find('.progress-bar');
            type.removeClass('hide').text('上传中~~~');
        }

        percent.css( 'width', percentage * 100 + '%' );
    });

    uploader.on( 'uploadSuccess', function( file, response ) {
        console.log(response);
        var json = response._raw;
        json = eval("("+json+")");
        $("#file_id"+ pa.id).val(json.nm);
        type.removeClass('hide').text('上传成功！');
        uploader.reset();
    });

    uploader.on( 'uploadError', function( file ) {
        type.removeClass('hide').text('上传失败');
    });

    // 完成上传删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').remove();
    });
}