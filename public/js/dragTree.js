function check_childred(){
$("li.dropTarget").each(function(){
   if($(this).children("div.haschild").length == 0 && !$(this).find("span").hasClass("dragItem")){
   $(this).remove();
   }
   else if ($(this).children("div").length == 0) {
   $(this).prepend('<div class="drop" style="background-position: -11px 0px;"></div>');
   }
   });
}
function append_children(){
     $("[id^=employee]").each(function(){
        var intID = this.id.replace(/[^0-9.]/g,'');
            if(!$(this).find("li").hasClass("dropTarget")){
            $(this).find("ul").append('<li class = "dropTarget"><span class = "dropTargetSpan">Subordinate</span><ul></ul></li>');
            }
    });
}
function apply_changes(employee_id, new_parent_id){
    new_parent_id = new_parent_id.length == 0 ? 0 : new_parent_id.replace(/[^0-9.]/g,'');
    if(employee_id.length > 0){
    $.ajax({
        type: "GET",
        url: "/change_parent_id",
        data: {employee_id: employee_id.replace(/[^0-9.]/g,''), new_parent_id: new_parent_id},
        success: function( msg ) {
        //
        }
    });
    }
}