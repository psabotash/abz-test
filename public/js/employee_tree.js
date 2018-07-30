$(document).ready(function(){
$(".ul-dropfree").find("li:has(ul)").prepend('<div class="drop"></div>');
    $("#root").removeAttr("id");
	$(document).on('click', '.ul-dropfree div.drop', function(){
        if ($(this).nextAll("ul").html().length == 0 || $(this).hasClass("haschild")) {
            $(this).removeClass("haschild");
            elem = $(this).nextAll("ul");
            $(this).css({'background-position':"-11px 0"});
            $(elem).html("<li>Loading data...</li>");
            $(elem).slideDown(400);
            intID = elem.parent().parent().parent().attr("id").replace(/[^0-9.]/g,'');
            load_child(elem, intID);
        }
        else if ($(this).nextAll("ul").css('display')=='none') {
			$(this).nextAll("ul").slideDown(400);
			$(this).css({'background-position':"-11px 0"});
		} else {
			$(this).nextAll("ul").slideUp(400);
			$(this).css({'background-position':"0 0"});
		}
	});
	$(".ul-dropfree").find("ul").slideUp(0).parents("li").children("div.drop").css({'background-position':"0 0"});
	$("div.drop").each(function(){
        if($(this).parent().children("span.dropTargetSpan").length != 0 && $(this).parent().children("span.root").length == 0){
        $(this).addClass("haschild");
        }
	});
});
function load_child(elem, id){
$(elem).load("tree?id="+intID+" #root", function(){
    $(elem).replaceWith($("#root"));
    $("#root").find("li:has(ul)").prepend('<div class="drop"></div>');
    $("#root").find("ul").slideUp(0);
    $("#root").find("div.drop").css({'background-position':"0 0"});
    $("#root div.drop").each(function(){
        if($(this).parent().children("span.dropTargetSpan").length != 0 && $(this).parent().children("span.root").length == 0){
        $(this).addClass("haschild");
        }
	});
    $("#root").removeAttr("id");
});
}