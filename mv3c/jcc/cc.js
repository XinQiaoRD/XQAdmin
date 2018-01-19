$(function() {
	
var cc = $(".CC");

cc.each(function(i){
	var c = $(this);
	var w = c.css("width");
	var h = c.css("height");
	var iframe = c.find("iframe");
	iframe.css("width", w);
	iframe.css("height", h);
	//iframe.css("overflow", "hidden");
});


});
