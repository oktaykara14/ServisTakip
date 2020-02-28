@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Yazılım Destek <small>Düzenli İşlemler Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/edestek/page-info.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core componets
            Layout.init(); // init layout
            Demo.init(); // init demo features
            QuickSidebar.init(); // init quick sidebar
            EdestekPage.init();
        });
    </script>
    <script>
        $(document).on("click", ".delete", function () {
            var Id = $(this).data('id');
            $(".modal-footer #sayacid").attr('href',"{{ URL::to('edestek/islemsil') }}/"+Id );
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
                        <i class="fa fa-tag"></i>Düzenli İşlem Kayıtları
                    </div>
                    <div class="actions">
                        <a href="{{ URL::to('edestek/islemekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Düzenli İşlem Ekle </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th class="hide"></th>
                            <th>Müşteri</th>
                            <th>Konu</th>
                            <th>İşlem</th>
                            <th>Başlangıç Tarihi</th>
                            <th>Personel</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($islemler as $islem)
                            <tr class="odd gradeX">
                                <td class="hide">{{ $islem->id }}</td>
                                <td> {{ $islem->edestekmusteri->musteriadi }}</td>
                                <td> @if($islem->edestekkonu_id ) {{$islem->edestekkonu->adi }} @endif</td>
                                <td> @if($islem->edestekkonuislem_id ) {{$islem->edestekkonuislem->islem }} @endif</td>
                                <td> @if($islem->baslangictarih)<span class="hide">{{$islem->baslangictarih}}</span> {{date("d-m-Y", strtotime($islem->baslangictarih))}} @endif</td>
                                <td> @if($islem->edestekpersonel_id ) {{$islem->edestekpersonel->adisoyadi }} @endif</td>
                                <td> @if($islem->durum==1) {{'Aktif'}}  @else {{'Pasif'}} @endif</td>
                                <td >
                                    <a class="btn btn-sm btn-warning" href="{{ URL::to('edestek/islemduzenle/'.$islem->id.'') }}" > Düzenle </a>
                                    <a href="#portlet-delete" data-toggle="modal" data-id="{{ $islem->id }}" class="btn btn-sm btn-danger delete" data-original-title="" title="">Sil</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END TABLE PORTLET-->
        </div>
    </div>
@stop

@section('modal')
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Düzenli İşlem Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Düzenli İşlemi Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
