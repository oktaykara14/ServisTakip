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
                <div class="portlet-body">
                    @if (Session::has('error_message'))
                        <div class="alert alert-danger" role="alert">
                            {{Session::get('error_message')}}
                        </div>
                    @endif
                    @if($iletiler->count() > 0)
                        @foreach($iletiler as $ileti)
                            <div class="media alert {{$ileti->isUnread(Auth::user()->id) ? 'alert-info' : ''}} {{$ileti->alici->kullanici_id}}">
                                <h4 class="media-heading">{{link_to('mesaj/detay/' . $ileti->id, $ileti->konu)}}</h4>
                                <p>{{$ileti->latestMessage!=="" ? $ileti->latestMessage->icerik : ""}}</p>
                                <p><small><strong>Gönderen:</strong> {{ $ileti->olusturan()!=="" ? $ileti->olusturan()->adi_soyadi : Auth::user()->adi_soyadi }}</small></p>
                                <p><small><strong>Alıcılar:</strong> {{ $ileti->participantsString(Auth::id()) }}</small></p>
                            </div>
                        @endforeach
                    @else
                        <p>Gelen Mesaj Yok.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop