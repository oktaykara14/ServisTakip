@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Müşteriler ve Projeler <small>Bilgi Ekranı</small></h1>
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
     $(".modal-footer #sayacid").attr('href',"{{ URL::to('edestek/musterisil') }}/"+Id);
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
                    <i class="fa fa-tag"></i>Proje Bilgileri
                </div>
                <div class="actions">
                    <a href="{{ URL::to('edestek/musteriekle') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-pencil"></i> Yeni Proje Ekle </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                    <thead>
                        <tr>
                            <th class="hide"></th>
                            <th>Proje Adı</th>
                            <th>İli</th>
                            <th>Başlangıç Tarihi</th>
                            <th>Bitiş Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($musteriler as $musteri)
                        <tr class="odd gradeX">
                            <td class="hide">{{ $musteri->id }}</td>
                            <td> {{ $musteri->musteriadi }}</td>
                            <td> @if($musteri->edestekmusteribilgi_id && $musteri->edestekmusteribilgi->iller ) {{$musteri->edestekmusteribilgi->iller->adi }} @endif</td>
                            <td> @if($musteri->baslangictarihi) {{date("d-m-Y", strtotime($musteri->baslangictarihi))}} @endif</td>
                            <td> @if($musteri->bitistarihi) {{date("d-m-Y", strtotime($musteri->bitistarihi))}} @endif</td>
                            <td >
                                <a class="btn btn-sm btn-warning" href="{{ URL::to('edestek/musteriduzenle/'.$musteri->id.'') }}" > Düzenle </a>
                                <a href="#portlet-delete" data-toggle="modal" data-id="{{ $musteri->id }}" class="btn btn-sm btn-danger delete" data-original-title="" title="">Sil</a>
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
                            <h4 class="modal-title">Proje Bilgisi Silinecek</h4>
                    </div>
                    <div class="modal-body">
                             Seçilen proje Bilgisini Silmek İstediğinizden Emin Misiniz?
                    </div>
                    <div class="modal-footer">
                            <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                            <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                    </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@stop
