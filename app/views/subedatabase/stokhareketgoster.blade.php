@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Stok Hareketi <small>Bilgi Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
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
    <div class="portlet box">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-pencil"></i>Stok Hareketi Bilgi Ekranı
            </div>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                <div class="form-body">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Hareket Türü:</label>
                        <label class="col-xs-8 harekettur" style="padding-top: 9px">{{$stokgiriscikis->harekettur.' - '.($stokgiriscikis->harekettur=='A' ? 'Devir' : ($stokgiriscikis->harekettur=='B' ? 'Depolar' : ($stokgiriscikis->harekettur=='C' ? 'Üretim' : ($stokgiriscikis->harekettur=='D' ? 'Muhtelif' : ($stokgiriscikis->harekettur=='F' ? 'Konsinye' : '')))))}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Masraf Merkezi:</label>
                        <label class="col-xs-8 masraf" style="padding-top: 9px">{{$stokgiriscikis->masrafkodu.' - '.$stokgiriscikis->masraf->ACIKLAMA}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Hareket Tarihi:</label>
                        <label class="col-xs-8 tarih" style="padding-top: 9px">{{date("d-m-Y", strtotime($stokgiriscikis->tarih))}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">GC Kodu:</label>
                        <label class="col-xs-8 gckod" style="padding-top: 9px">{{$stokgiriscikis->gckod=='G' ? 'Giriş' : 'Çıkış'}}</label>
                    </div>
                    <div class="form-group col-xs-12">
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-4">Açıklama 1:</label>
                            <label class="col-xs-8 aciklama1" style="padding-top: 9px">{{$stokgiriscikis->aciklama}}</label>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-4">Açıklama 2:</label>
                            <label class="col-xs-8 aciklama1" style="padding-top: 9px">{{$stokgiriscikis->aciklama2}}</label>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-4">Açıklama 3:</label>
                            <label class="col-xs-8 aciklama1" style="padding-top: 9px">{{$stokgiriscikis->aciklama3}}</label>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4"> Eklenen Ürünler </label>
                    </div>
                    <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                        @for($i=0;$i<(count($stokgiriscikis->urunler));$i++)
                            <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{($stokgiriscikis->urunler[$i]->urunadi) .' - '. ($stokgiriscikis->adetler[$i]) . ' ADET' }} </a>
                                    </h4>
                                </div>
                                <div id="collapse_{{$i}}" class="panel-collapse in">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-xs-4 control-label">Ürün Adı:</label>
                                            <label class="col-xs-8" style="padding-top: 9px">{{$stokgiriscikis->urunler[$i]->netsisstokkod->kodu.' - '.$stokgiriscikis->urunler[$i]->urunadi}}</label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Depo Kodu:</label>
                                            <label class="col-xs-8" style="padding-top: 9px">{{$stokgiriscikis->depokodlari[$i]}}</label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 control-label">Miktarı:</label>
                                            <label class="col-xs-8" style="padding-top: 9px">{{$stokgiriscikis->adetler[$i]}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <div class="form-group">{{ Form::token() }}</div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center">
                            <a href="{{ URL::to('subedatabase/stokhareket')}}" class="btn green">Tamam</a>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END FORM-->
        </div>
        <!-- END VALIDATION STATES-->
    </div>
@stop

@section('modal')

@stop
