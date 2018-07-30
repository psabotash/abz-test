@extends('layouts.app')
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>
function disable_edit(){
    $("[id^=edit_input]").each(function(){
       $(this).prop("disabled", true);
       $(this).removeClass("is-invalid");
       $(this).removeClass("is-valid");
    });
    $("#edit_datetime_icon").hide();
    $("#div_edit_uploadPhoto").hide();
    $("#btn-edit-edit").show();
    $("#btn-delete-employee").show();
    $("#btn-apply-edit").hide();
}
$(document).ready(function(){
    $(document).click(function() {
        if(event.target.id != "create_inputParentName"){
            $("#ajax_create_search_employee").css("visibility", "hidden"); 
        }
        if(event.target.id != "edit_inputParentName"){
            $("#ajax_edit_search_employee").css("visibility", "hidden"); 
        }
    });
    $("#ajax_create_search_employee").on("click", "td", function() {
        $("#create_inputParentName").val($(this).text());
    });
    $("#ajax_edit_search_employee").on("click", "td", function() {
        $("#edit_inputParentName").val($(this).text());
    });
$(document).on('click', '.view-profile', function(){
    $("#modal_window").show();
    $("#main").css("opacity", "0.5");
    $elem = $(this).parent().parent();
    $img = $elem.children('td:nth-child(2)').children('img:first').attr('src');
    $("#edit_profileImage").attr("src", $img);
    $("#edit_inputID").val($elem.children('td:nth-child(1)').text().trim());
    $("#edit_inputName").val($elem.children('td:nth-child(2)').text().trim());
    $("#edit_inputPosition").val($elem.children('td:nth-child(3)').text().trim());
    $("#edit_inputDate").val($elem.children('td:nth-child(4)').text().trim());
    $("#edit_inputSalary").val($elem.children('td:nth-child(5)').text().trim());
    $("#edit_inputParentName").val($elem.children('td:nth-child(6)').text().trim());
    disable_edit();
    $("#body-edit").show();
});
$("#btn-delete-employee").click(function() {
    $.confirm({
            'title'     : 'Delete Emloyee Profile',
            'content'   : 'You are about to delete this profile. <br/>All subordinates will be transferred to the manager or remain without a manager (in case of his absence).<br/>It cannot be restored at a later time! Continue?',
            'icon': 'fa fa-warning',
            'buttons'   : {
                'Yes'   : {
                    'btnClass': 'btn-blue',
                    'action': function(){
                        ajax_CUD_employee("delete", $("#edit_inputID").val());
                    }
                },
                'Cancel'    : {
                    'action': function(){}
                }
            }
        });
});
$("#btn-edit-edit").click(function() {
    $("#btn-edit-edit").hide();
    $("#btn-apply-edit").show();
    $("[id^=edit_input]").not(":first").each(function(){
       $(this).prop("disabled", false);
    });
    $("#edit_datetime_icon").show();
    $("#div_edit_uploadPhoto").show();
});
$('#create_inputParentName').on('keyup change', function() {
    ajax_search_employee("create");
});
$('#edit_inputParentName').on('keyup change', function() {
    ajax_search_employee("edit");
});
$( "#salary" ).val("0 - " + $("#max_salary").val());
$.ajaxSetup({
headers: 
{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
function ajax_search_employee(mode){
    $("#ajax_"+mode+"_search_employee").css("visibility", "visible");
        $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ajax_search_employee',
        data:{
           value: $("#"+mode+"_inputParentName").val(),
        },
        success: function(data){
            if(data != null){
                var table_body = "";
                    data.forEach(function(item, i, data) {
                    table_body += '<tbody><tr><td><img src="'+item.img_path+'" style = "width: 24px; height: 24px;"/>'+item.name+' (ID: '+item.id+')</td></tr></tbody>';
                    });
                if(table_body.length > 0){
                    $("#ajax_"+mode+"_search_employee").html(table_body);
                }
                else{
                    $("#ajax_"+mode+"_search_employee").html("");
                }
            }
            else{
                $("#ajax_"+mode+"_search_employee").html("");
            }
	    }
        });
    }
function hide_modal(){
    $("#modal_window").hide();
    $("#btn-filters").removeClass("btn-success");
    $("#btn-create").removeClass("btn-success");
    $("#btn-view-setting").removeClass("btn-success");
    $("#btn-filters").addClass("btn-primary");
    $("#btn-create").addClass("btn-primary");
    $("#btn-view-setting").addClass("btn-primary");
    $("#body-filters").hide();
    $("#body-create").hide();
    $("#body-edit").hide();
    $("#body-view-setting").hide();
    $("#main").css("opacity", "1");
    disable_edit();
}
$("#close_modal_window").click(function() {
   hide_modal();
});
$("#btn-filters").click(function() {
    $(this).toggleClass('btn-primary btn-success');
    $("#body-filters").show();
    $("#modal_window").show();
    $("#main").css("opacity", "0.5");
});
$("#btn-create").click(function() {
    $(this).toggleClass('btn-primary btn-success');
    $("#body-create").show();
    $("#modal_window").show();
    $("#main").css("opacity", "0.5");
});
function ajax_CUD_employee(mode, id){
    var data = new FormData();
	data.append('id', id);
	if(mode != "delete"){
        data.append('name', $("#"+mode+"_inputName").val());
        data.append('parent_name', $("#"+mode+"_inputParentName").val());
        data.append('salary', $("#"+mode+"_inputSalary").val());
        data.append('hire_date', $("#"+mode+"_inputDate").val());
        data.append('position', $("#"+mode+"_inputPosition").val());
        data.append('img', $("#"+mode+"_inputFile").prop("files")[0]);
        data.append('img_default', $("#"+mode+"_uploadPhoto").is(":checked") ? 1 : 0);
	}
	$("#load_window").css("visibility", "visible");
	$.ajax({
        url: 'ajax_'+mode+'_employee',
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json',
        success: function(data){
        $("#load_window").css("visibility", "hidden");
            if(data != null){
                data.forEach(function(item, i, data) {
                    if(item.status == "error"){
                       if(item.reason == "File"){
                            $("#"+mode+"-invalid-feedback-file").show();
                       }
                       else if(item.reason == "Unknown"){
                            $.alert({
                                title: 'Error!',
                                type: 'red',
                                closeIcon: true,
                                content: item.msg,
                            });
                       }
                       else{
                            $("#"+mode+"input" + item.reason).removeClass("is-valid");
                            $("#"+mode+"_input" + item.reason).addClass("is-invalid");
                       }
                    }
                    else if(item.status == "success"){
                       if(item.reason == "File"){
                          $("#"+mode+"-invalid-feedback-file").hide();
                       }
                       else if(item.reason == mode){
                          $.alert({
                            title: 'Success!',
                            type: 'green',
                            closeIcon: true,
                            content: item.msg,
                          });
                          $("#"+mode+"-invalid-feedback-file").hide();
                          $("[id^="+mode+"_input]").each(function(){
                            if(mode == "edit"){
                                disable_edit();
                            }
                            else{
                                $(this).val("");
                            }
                            $(this).removeClass("is-invalid");
                            $(this).removeClass("is-valid");
                          });
                          if(mode == "edit"){
                            update(1, false, function(){
                                
                            });
                          }
                          else{
                            update(1, true);
                            $("#main").css("opacity", "1");    
                          }
                       }
                       else{
                          $("#"+mode+"_input" + item.reason).removeClass("is-invalid");
                          $("#"+mode+"_input" + item.reason).addClass("is-valid");   
                       }
                    }
                });
            }
        }
    });
}
$("#btn-apply-create").click(function() {
	ajax_CUD_employee("create", 0);
});
$("#btn-apply-edit").click(function() {
	ajax_CUD_employee("edit", $("#edit_inputID").val());
});
$("#btn-view-setting").click(function() {
    $(this).toggleClass('btn-primary btn-success');
    $("#body-view-setting").show();
    $("#modal_window").show();
    $("#main").css("opacity", "0.5");
});
$("#btn-remove-filters").click(function() {
    $("#inputID").val("");
    $("#inputName").val("");
    $("#inputParentName").val("");
    $("#date1").val("");
    $("#date2").val("");
    $("#inputPosition").val("");
    $("#salary").val("0 - "+$("#max_salary").val());
    $("#slider").slider("option", "values", [0, $("#max_salary").val()]);
});
$("#btn-apply-filters").click(function() {
    $("#f_id").val($("#inputID").val());
    $("#f_name").val($("#inputName").val());
    $("#f_parentName").val($("#inputParentName").val());
    $("#f_date1").val($("#date1").val());
    $("#f_date2").val($("#date2").val());
    $("#f_position").val($("#inputPosition").val());
    $("#f_salary1").val($("#salary").val().split(" ")[0]);
    $("#f_salary2").val($("#salary").val().split(" ")[2]);
    $filter_count = 0;
    $(".filter_item").each(function(){
        if($(this).val().length > 0){
            $filter_count++;
        }
    });
    if($("#f_salary1").val() != 0){
        $filter_count++;
    }
    if($("#f_salary2").val() != $("#max_salary").val()){
        $filter_count++;
    }
    $("#btn-filters").html("Filters ("+$filter_count+")");
    update(1, true);
});
$("#btn-apply-view-setting").click(function() {
    $("#f_sort").val($("#sort").val());
    $("#f_max_on_page").val($("#max_on_page").val());
    $("#f_sort_type").val($('input[name="customRadioInline1"]:checked').val());
    update(1, true);
});
$("#btn-remove-view-setting").click(function() {
   $("#sort").val(1);
   $("#max_on_page").val(5);
   $('input:radio[name="customRadioInline1"][value="0"]').click();
});
function update(page, _hide_modal){
    if(_hide_modal){
        hide_modal();   
    }
     $elem = $("#list_body");
	    $elem.removeAttr("id");
	    $("#load_window").css("visibility", "visible");
	    $("#main").css("opacity", "0.5");
	    $elem.load("/list #list_body", {page: page, id: $("#f_id").val(), name: $("#f_name").val(), parentName: $("#f_parentName").val(), date1: $("#f_date1").val(), date2: $("#f_date2").val(), position: $("#f_position").val(), salary1: $("#f_salary1").val(), salary2: $("#f_salary2").val(), max_on_page: $("#f_max_on_page").val(), sort: $("#f_sort").val(), sort_type: $("#f_sort_type").val()}, function(){
            $elem.replaceWith($("#list_body"));
            $("#load_window").css("visibility", "hidden");
            $("#main").css("opacity", "1");
    });
}
	$(document).on('click', '.btn-page', function(){
	    max_page = $(".btn-page").last().html();
	    next_page = $(this).html();
	    cur_page = $("#this_page").html();
	    if(next_page == "..."){
	        if (parseInt($(this).prevAll().html()) > parseInt(cur_page)){
	            if(parseInt(cur_page) + 10 > max_page){
	                next_page = parseInt(max_page) - 1;
	            }
	            else if (parseInt(cur_page) == 1){
	                next_page = 12;
	            }
	            else{
	                next_page = parseInt(cur_page) + 10;
	            }
	        }
	        else{
	            if(parseInt(cur_page) <= 10){
	                next_page = 2;
	            }
	            else{
	                next_page =  parseInt(cur_page) - 10;
	            }
	        }
	    }
	    update(next_page, true);
	});
});
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('css/load.css') }}">
<style>
#disable_hover:hover {  background-color: #f5f8fa; }
#load_window {
margin-top: -23px;
top: 50%;
left: 0;
visibility: hidden;
text-align: center;
position: fixed;
height: 100%;
width: 100%;
z-index: 100;
}
#modal_window {
top: 0;
left: 0;
/*visibility: hidden;*/
text-align: center;
position: fixed;
height: 100%;
width: 100%;
z-index: 100;
}
</style>
@endsection
@section('content')
<div id = "main">
<input id = "max_salary" type = "hidden" value = "{{ $max_salary }}">
<input class = "filter_item" id = "f_id" type = "hidden">
<input class = "filter_item" id = "f_name" type = "hidden">
<input class = "filter_item" id = "f_parentName" type = "hidden">
<input class = "filter_item" id = "f_date1" type = "hidden">
<input class = "filter_item" id = "f_date2" type = "hidden">
<input class = "filter_item" id = "f_position" type = "hidden">
<input id = "f_max_on_page" type = "hidden" value = "{{ $max_on_page }}">
<input id = "f_sort" type = "hidden" value = "1">
<input id = "f_sort_type" type = "hidden" value = "0">
<input id = "f_salary1" type = "hidden">
<input id = "f_salary2" type = "hidden">
<table class = "table">
<tr>
    <thead>
        <th>
            <button type="button" id = "btn-filters" class="btn btn-primary">Filters (0)</button>
            <button type="button" id = "btn-view-setting" class="btn btn-primary">View settings</button>
            <button type="button" id = "btn-create" class="btn btn-primary" style = "float: right;">Add Employee</button>
        </th>
    </thead>
</table>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">Position</th>
      <th scope="col">Hire Date</th>
      <th scope="col">Salary</th>
      <th scope="col">Manager</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody id = "list_body">
  @foreach ($list as $employee)
		<tr>
			<td>
				{{ $employee->id }}
			</td>
			<td>
			    @if(file_exists(public_path().'/img/employees/employee_'.$employee->id.'.png'))
                    <img src="{{ url('img/employees/employee_'.$employee->id.'.png') }}" style = "width: 24px; height: 24px;"/>
                @else
                    <img src="{{ url('img/employees/employee_default.png') }}" style = "width: 24px; height: 24px;"/>
                @endif
				{{ $employee->name }}
			</td>
			<td>
				{{ $employee->position }}
			</td>
			<td>
				{{\Carbon\Carbon::parse($employee->hire_date)->format('m/d/Y h:i A')}}
			</td>
			<td>
				{{ $employee->salary }}
			</td>
			<td>
			    @if(file_exists(public_path().'/img/employees/employee_'.$employee->parent_id.'.png'))
                    <img src="{{ url('img/employees/employee_'.$employee->parent_id.'.png') }}" style = "width: 24px; height: 24px;"/>
                @elseif ($employee->parent_id != NULL)
                    <img src="{{ url('img/employees/employee_default.png') }}" style = "width: 24px; height: 24px;"/>
                @endif
				{{ $employee->parent_name }}
			</td>
			<td>
			    <button type="button" class="btn btn-primary btn-sm view-profile">View Profile</button>
			</td>
		</tr>
    @endforeach
    <tr>
                <td id = "disable_hover" colspan="7" style = "text-align: center;">
                    @if ($page > 6 && $count > 0)
                        <button type="button" class="btn btn-primary btn-page">1</button>
                        @if ($page > 7)
                        <button type="button" class="btn btn-primary btn-page">...</button>
                        @endif
                    @endif
                    @for ($print_count = 0, $i = $page - 5; $print_count < 11 && $i <= ceil($count/$max_on_page); $i++)
                      @if ($i > 0) 
                           @php
                               $print_count++;
                         @endphp
                               @if($page == $i)
                                   <button type="button" id = "this_page" class="btn btn-success disabled" disabled>{{ $i }}</button>
                               @else
                                  <button type="button" class="btn btn-primary btn-page">{{ $i }}</button>
                               @endif
                       @endif
                 @endfor
                 @if ($page + 5 <= ceil($count/$max_on_page))
                        @if ($page + 6 <= ceil($count/$max_on_page))
                     <button type="button" class="btn btn-primary btn-page">...</button>
                        @endif
                      <button type="button" class="btn btn-primary btn-page">{{ ceil($count/$max_on_page) }}</button>
                  @endif
             </td>
    </tr>
  </tbody>
</table>
</div>
<div id="modal_window" style = "display: none;">
    <div id="modal_content" style="position: absolute; left: 50%; margin-top: 10%; margin-left: -45%; width: 90%; background-color: #fff; border-radius: 10px;">
        <table class = "table">
    <tbody id = "body-edit" style = "display: none;">
        <tr>
        <td style = "text-align: left;">
            <div style="font-size: 17pt; position: absolute; left:50%; margin-top: 10px; margin-left: -100px; height: 20px; width: 200px;"><strong>Employee Profile</strong></div>
            <div class="form-row" style = "margin-top: 50px;">
                <div class="col-md-4 mb-3">
                    <img id = "edit_profileImage" src="{{ url('img/employees/employee_default.png') }}" style = "width: 200px; height: 200px;"/>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="edit_inputID">ID</label>
                    <input type="text" class="form-control" id="edit_inputID" placeholder="ID">
                    <label for="edit_inputName">Name</label>
                    <input type="text" class="form-control" id="edit_inputName" placeholder="Name">
                    <div class="invalid-feedback">
                        Please enter a valid name.
                    </div>
                    <div>
                        <label for="edit_inputSalary">Salary</label>
                        <input type="number" class="form-control" placeholder="Salary" id="edit_inputSalary">
                        <div class="invalid-feedback">
                           Please enter a valid salary.
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                        <label for="edit_inputParentName">Manager</label>
                      <div>
                        <input type="text" class="form-control" placeholder="Manager" id="edit_inputParentName">
                        <table class = "table table-hover" id = "ajax_edit_search_employee" style="margin-top: 3px; width: calc(100% - 9px); position: absolute; background-color: #fff; z-index: 2;">
                        </table>
                       <div class="invalid-feedback">
                           Please enter a valid name.
                       </div>
                      </div>
                        <label for="datetimepicker3">Hire Date</label>
                        <div class="input-group date" id="datetimepicker4" data-target-input="nearest">
                        <input id = "edit_inputDate" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker4"/>
                        <div id = "edit_datetime_icon" class="input-group-append" data-target="#datetimepicker4" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                            <div class="invalid-feedback">
                                Please enter a valid date.
                            </div>
                        </div>
                    <div>
                        <label for="edit_inputPosition">Position</label>
                        <input type="text" class="form-control" placeholder="Position" id="edit_inputPosition">
                        <div class="invalid-feedback">
                            Please enter a valid position.
                        </div>
                    </div>
                    <div id = "div_edit_uploadPhoto">
                    <label for="edit_inputFile">Photo (*.png, min: 24 x 24px, max: 400x400 px)</label>
                    <input type="file" class="form-control-file" id="edit_inputFile">
                    <input type="checkbox" id="edit_uploadPhoto">
                    <label for="edit_uploadPhoto">
                        Use default picture for this employee.
                    </label>
                    <div id = "edit-invalid-feedback-file" class="invalid-feedback">
                        Please upload a valid image file.
                    </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <button type="button" id = "btn-apply-edit" class="btn btn-primary">Apply</button>
                    <button type="button" id = "btn-edit-edit" class="btn btn-warning">Edit</button>
                    <button type="button" id = "btn-delete-employee" class="btn btn-danger">Delete Profile</button>
                </div> 
            </div>
            </td>
            </tr>
    </tbody>
    <tbody id = "body-create" style = "display: none;">
        <tr>
        <td style = "text-align: left;">
        <div style="font-size: 17pt; position: absolute; left:50%; margin-top: 10px; margin-left: -100px; height: 20px; width: 200px;"><strong>Add Employee</strong></div>
            <div class="form-row" style = "margin-top: 50px;">
                <div class="col-md-4 mb-3">
                    <label for="create_inputName">Name</label>
                    <input type="text" class="form-control" id="create_inputName" placeholder="Name">
                    <div class="invalid-feedback">
                        Please enter a valid name.
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="create_inputParentName">Manager</label>
                    <input type="text" class="form-control" placeholder="Manager" id="create_inputParentName">
                    <div class="invalid-feedback">
                        Please enter a valid name.
                    </div>
                    <table class = "table table-hover" id = "ajax_create_search_employee" style="margin-top: 3px; width: calc(100% - 9px); position: absolute; background-color: #fff; z-index: 2;">
                    </table>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="create_inputSalary">Salary</label>
                    <input type="number" class="form-control" placeholder="Salary" id="create_inputSalary">
                    <div class="invalid-feedback">
                        Please enter a valid salary.
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="datetimepicker3">Hire Date</label>
                    <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                    <input id = "create_inputDate" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker3"/>
                    <div class="input-group-append" data-target="#datetimepicker3" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    <div class="invalid-feedback">
                        Please enter a valid date.
                    </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="create_inputPosition">Position</label>
                    <input type="text" class="form-control" placeholder="Position" id="create_inputPosition">
                    <div class="invalid-feedback">
                        Please enter a valid position.
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="create_inputFile">Photo (*.png, min: 24 x 24px, max: 400x400 px)</label>
                    <input type="file" class="form-control-file" id="create_inputFile">
                    <input type="checkbox" id="create_uploadPhoto">
                    <label for="create_uploadPhoto">
                        Use default picture for this employee.
                    </label>
                    <div id = "create-invalid-feedback-file" class="invalid-feedback">
                        Please upload a valid image file.
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <button type="button" id = "btn-apply-create" class="btn btn-primary">Apply</button>
                </div> 
            </div>
            </td>
            </tr>
    </tbody>
    <tbody id = "body-view-setting" style = "display: none;">
        <tr>
        <td style = "text-align: left;">
        <div style="font-size: 17pt; position: absolute; left:50%; margin-top: 10px; margin-left: -100px; height: 20px; width: 200px;"><strong>View Settings</strong></div>
            <div class="form-row" style = "margin-top: 50px;">
                <div class="col-md-4 mb-3">
                 <label for="max_on_page">Employees per page:</label>
                    <select id="max_on_page" class="form-control" style = "padding: 2px;">
                        <option>5</option>
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                 </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                 <label for="sort">Sort by: </label>
                    <select id="sort" class="form-control" style = "padding: 2px;">
                        <option value = "0">ID</option>
                        <option value = "1" selected>Name</option>
                        <option value = "2">Position</option>
                        <option value = "3">Hire Date</option>
                        <option value = "4">Salary</option>
                        <option value = "5">Manager</option>
                    </select>
                 </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                 <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" value = "0" checked>
                    <label class="custom-control-label" for="customRadioInline1">Ascending</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input" value = "1">
                    <label class="custom-control-label" for="customRadioInline2">Descending</label>
                </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <button type="button" id = "btn-apply-view-setting" class="btn btn-primary">Apply</button>
                    <button type="button" id = "btn-remove-view-setting" class="btn btn-danger">Reset settings</button>
                </div> 
            </div>
        </td>
        </tr>
    </tbody>
    <tbody id = "body-filters" style = "display: none;">
        <tr>
        <td style = "text-align: left;">
            <div style="font-size: 17pt; position: absolute; left:50%; margin-top: 10px; margin-left: -100px; height: 20px; width: 200px;"><strong>Filters</strong></div>
            <div class="form-row" style = "margin-top: 50px;">
                <div class="form-group col-md-2">
                    <label for="inputID">ID</label>
                    <input type="text" class="form-control" placeholder="ID" id="inputID">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="inputName">Name</label>
                    <input type="text" class="form-control" id="inputName" placeholder="Name">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="inputParentName">Manager</label>
                    <input type="text" class="form-control" placeholder="Manager" id="inputParentName">
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-3">
                    <label for="datetimepicker1">Hire Date (min)</label>
                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                    <input id = "date1" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1"/>
                    <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="datetimepicker2">Hire Date (max)</label>
                    <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                    <input id = "date2" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                    <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="inputPosition">Position</label>
                    <input type="text" class="form-control" placeholder="Position" id="inputPosition">
                </div>
            </div>
            <div class="form-row">
               <div class="col-md-4 mb-3">
                    <label for="salary">Salary range:</label>
                    <input type="text" id="salary" readonly style="background-color: transparent; border:0; color:#067aff; font-weight:bold;">
                    <!--<div id="slider-range"></div>-->
                    <div id="slider"></div>
                    <script>
                        $( "#slider" ).slider({
                        min:0,
                        max: $("#max_salary").val(),
                        range: true,
                        values: [ 0, $("#max_salary").val() ],
                        slide: function( event, ui ) {
                           $( "#salary" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
                        }
                    });
                    </script>
                </div> 
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <button type="button" id = "btn-apply-filters" class="btn btn-primary">Apply</button>
                    <button type="button" id = "btn-remove-filters" class="btn btn-danger">Delete all filters</button>
                </div> 
            </div>
        </td>
        </tr>
    </tbody>
    </table>
    <div id="close_modal_window" style = "position: absolute; right: 0; top: 0; cursor: pointer;"><img style = "width: 24px; height: 24px; margin-top: 5px; margin-right: 5px;" src="{{ url('img/icons/close.png') }}"></div>
    </div>
</div>
<div id="load_window">
    <div id="spinningSquaresG">
	    <div id="spinningSquaresG_1" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_2" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_3" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_4" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_5" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_6" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_7" class="spinningSquaresG"></div>
	    <div id="spinningSquaresG_8" class="spinningSquaresG"></div>
    </div>
</div>
@endsection