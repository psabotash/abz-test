@extends('layouts.app')
@section('scripts')
<script src="{{ url('js/employee_tree.js') }}"></script>
<script src="{{ url('js/dragTree.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/lib.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/DragManager.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/DragAvatar.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/DragZone.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/DropTarget.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/TreeDragAvatar.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/TreeDragZone.js') }}"></script>
<script src="{{ url('js/plugins/dragTree/TreeDropTarget.js') }}"></script>
 <script>
$(document).ready(function(){
    var tree = document.getElementById('tree');
    new TreeDragZone(tree);
    new TreeDropTarget(tree);
});
</script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/employee_tree.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/dragTree.css') }}">
@endsection
@section('content')
<ul class="ul-treefree ul-dropfree" id ="tree">
<li class = "dropTarget"><span class = "dropTargetSpan root">Employee tree</span>
<ul id = "root">
@foreach ($tree as $employee)
<li id = "employee_{{ $employee->id }}"><span class = "dragItem">{{ $employee->name }}</span><ul>
<li><span>Position: {{ $employee->position }}</span></li>
<li><span>Hire date: {{ $employee->hire_date }}</span></li>
<li><span>Salary: {{ $employee->salary }}</span></li>
@if ($employee->hasChild > 0)
<li class = "dropTarget"><span class = "dropTargetSpan">Subordinate</span><ul>
</ul>
</li>
@endif
</ul>
</li>
@endforeach
</ul>
</li>
</ul>
@endsection