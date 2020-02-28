@extends('layout.master')

@section('page-title')
    <div class="page-title">
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
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN TABLE PORTLET-->
            <div class="portlet box">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-envelope"></i>Gelen Mesajlar
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="col-md-6">
                        <h1>{{$ileti->konu}}</h1>
                        @foreach($ileti->mesaj as $mesaj)
                            <div class="media">
                                <a class="pull-left">
                                    <img class="img-circle" style="height: 39px;" src="@if($mesaj->kullanici->avatar!=="") {{URL::to('assets/images/profilresim/'.$mesaj->kullanici->avatar.'') }}  @else {{ URL::to('assets/images/profilresim/test.png') }} @endif" alt="{{$mesaj->kullanici->adi_soyadi}}" class="img-circle">
                                </a>
                                <div class="media-body">
                                    <h5 class="media-heading">{{$mesaj->kullanici->adi_soyadi}}</h5>
                                    <p>{{$mesaj->icerik}}</p>
                                    <div class="text-muted"><small>{{BackendController::time_elapsed($mesaj->created_at)}}</small></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ URL::to('mesaj/guncelle/'.$ileti->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <textarea id="mesaj" name="mesaj" rows="4" cols="50" class="col-md-6" style="margin-left: 20px"></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <button type="submit" class="btn green">Gönder</button>
                                    <a href="{{ URL::to('mesaj')}}" class="btn default">Vazgeç</a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@stop
