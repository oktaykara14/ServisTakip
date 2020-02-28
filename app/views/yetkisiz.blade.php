@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Yetkisiz Sayfa <small></small></h1>
</div>
@stop

@section('page-plugins')
@stop

@section('page-styles')
<link href="{{ URL::to('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
//   Index.init(); // init index page
});
</script>
@stop

@section('content')
Yetkinizin Olmadığı Bir Sayfaya Girmeye Çalıştınız.
@stop
