@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Yazılım Destek Kayıt <small>Takip Ekranı</small></h1>
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
     $(".modal-footer #sayacid").attr('href',"{{ URL::to('edestek/kayitsil') }}/"+Id );
});
if('{{Session::get("problem")}}' !=='')
{
    $(".cozumkonu").html('{{Session::get("konu")}}');
    $(".cozumkonudetay").html('{{Session::get("detay")}}');
    $(".cozumproblem").html('{{Session::get("problem")}}');
    $(".cozumdetay").html('{{Session::get("cozum")}}');

    $(".cozumkonuid").val('{{Session::get("konuid")}}');
    $(".cozumkonudetayid").val('{{Session::get("detayid")}}');
    $(".cozumproblemid").val('{{Session::get("problem")}}');
    $(".cozumdetayid").val('{{Session::get("cozum")}}');

    $('#cozum-kaydet').modal('show');
}
$('#closeButton').click(function(){
    $('#cozum-kaydet').modal('hide');
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
                    <i class="fa fa-tag"></i>Yazılım Destek Kayıtları
                </div>
                <div class="actions">
                    <a href="{{ URL::to('edestek/gorusmeekle') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-comment"></i> Müşteri Destek </a>
                    <a href="{{ URL::to('edestek/kurulumekle') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-cogs"></i> Kurulum </a>
                    <a href="{{ URL::to('edestek/tamirekle') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-wrench"></i> Tamir Bakım </a>
                    <a href="{{ URL::to('edestek/baskiekle') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-image"></i> Kart Baskısı </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                    <thead>
                        <tr>
                            <th class="hide"></th>
                            <th>Müşteri</th>
                            <th>Konu</th>
                            <th>Yapılan İşlem</th>
                            <th>Personel</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kayitlar as $kayit)
                        <tr class="odd gradeX">
                            <td class="hide">{{ $kayit->id }}</td>
                            <td> {{ $kayit->edestekmusteri ? $kayit->edestekmusteri->musteriadi : '' }}</td>
                            <td> @if($kayit->edestekkayitkonu_id ) {{$kayit->edestekkayitkonu->adi }} @endif</td>
                            <td> {{ $kayit->yapilanislem }}</td>
                            <td> @if($kayit->edestekpersonel_id ) {{$kayit->edestekpersonel->adisoyadi }} @endif</td>
                            <td> @if($kayit->tarih)<span class="hide">{{$kayit->tarih}}</span> {{date("d-m-Y", strtotime($kayit->tarih))}} @endif</td>
                            <td> @if($kayit->durum==1) {{'Tamamlandı'}} @elseif($kayit->durum==2) {{'Devredildi'}} @else {{'Bekliyor'}} @endif</td>
                            <td >
                                @if($kayit->durum==1 && (time()-strtotime($kayit->updated_at))>86400 )
                                <a class="btn btn-sm btn-primary" href="{{ URL::to('edestek/kayitgoster/'.$kayit->id.'') }}" > Göster </a>
                                @else
                                <a class="btn btn-sm btn-warning" href="{{ URL::to('edestek/kayitduzenle/'.$kayit->id.'') }}" > Düzenle </a>
                                @endif
                                <a href="#portlet-delete" data-toggle="modal" data-id="{{ $kayit->id }}" class="btn btn-sm btn-danger delete" data-original-title="" title="">Sil</a>
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
                            <h4 class="modal-title">Kayıt Silinecek</h4>
                    </div>
                    <div class="modal-body">
                             Seçilen Kayıdı Silmek İstediğinizden Emin Misiniz?
                    </div>
                    <div class="modal-footer">
                            <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                            <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                    </div>
            </div>
    </div>
</div>
<div class="modal fade" id="cozum-kaydet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Hata Çözümü Kaydetme
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="{{ URL::to('edestek/hatacozumekle') }}" id="form_sample_1" method="POST" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-2 col-xs-12">Konu</label>
                                        <label class="cozumkonu col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                        <input class="cozumkonuid hide" name="options1"/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Detayı</label>
                                        <label class="cozumkonudetay col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                        <input class="cozumkonudetayid hide" name="options2"/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2 col-xs-12">Problem</label>
                                        <label class="cozumproblem col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                        <input class="cozumproblemid hide" name="problem"/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2 col-xs-12">Çözümü</label>
                                        <label class="cozumdetay col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                        <input class="cozumdetayid hide" name="cozumyeniid"/>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="submit" class="btn green">Kaydet</button>
                                                <button type="button" id="closeButton" class="btn default">Vazgeç</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
