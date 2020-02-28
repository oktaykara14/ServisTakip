@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Hatalı İşlem<small></small></h1>
</div>
@stop

@section('page-plugins')
@stop

@section('page-styles')
@stop

@section('page-js')
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   QuickSidebar.init(); // init quick sidebar
});
</script>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            Sorgu Sayfası :@if(isset($url)) {{$url}} @endif
        </div>
        <div class="col-xs-12">
            Alınan Hata :@if(isset($exception)) {{$exception->getMessage()}} @endif
        </div>
        <div class="col-xs-12">
            Hata Alınan Dizin :@if(isset($exception)) {{$exception->getFile()}} @endif
        </div>
        <div class="col-xs-12">
            Hata Alınan Satır :@if(isset($exception)) {{$exception->getLine()}} @endif
        </div>
    </div>

@stop
