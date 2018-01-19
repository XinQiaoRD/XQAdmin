$(function() {
var imgt = $("img");
imgt.each(function(i){
	var img = $(this);
	var src = img.attr('src');
	if (src.indexOf('http://')<0 && src.indexOf('.')<0 && src.indexOf('?')<=0){ img.remove(); }
});
});
