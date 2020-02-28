@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Kart Baskı Bilgisi <small></small></h1>
</div>
@stop

@section('page-plugins')
@stop

@section('page-js')
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

$(document).ready(function() {
    var options1 = "{{ $kayit->edestekmusteri_id }}";
    var options3 = "{{ $baski->edestekkartbaski_id }}";
    if (options1 !== "") {
        $.getJSON(" {{ URL::to('edestek/musteribaski') }}/" + options1, function (event) {
            $("#options3").empty();
            var baski = event.baski;
            if (baski.length > 0) {
                $("#options3").append('<option value="">Seçiniz...</option>');
                $.each(baski, function (index) {
                    switch (baski[index].edestekbaskitur_id) {
                        case '1':
                            document.getElementById("baskitur").innerHTML = 'Su Kart Baskısı';
                            break;
                        case '2':
                            document.getElementById("baskitur").innerHTML = 'Kalorimetre Kart Baskısı';
                            break;
                        case '3':
                            document.getElementById("baskitur").innerHTML = 'Manas Kart Baskısı';
                            break;
                        case '4':
                            document.getElementById("baskitur").innerHTML = 'Trifaze Elektrik Kart Baskısı';
                            break;
                        case '5':
                            document.getElementById("baskitur").innerHTML = 'Monofaze Elektrik Kart Baskısı';
                            break;
                        case '6':
                            document.getElementById("baskitur").innerHTML = 'Baskısız';
                            break;
                        case '7':
                            document.getElementById("baskitur").innerHTML = 'Klimatik Kart Baskısı';
                            break;
                        case '8':
                            document.getElementById("baskitur").innerHTML = 'Gaz Kart Baskısı';
                            break;
                        case '9':
                            document.getElementById("baskitur").innerHTML = 'Mifare Kart Baskısı';
                            break;
                        case '10':
                            document.getElementById("baskitur").innerHTML = 'Mifare Manas Kart Baskısı';
                            break;
                    }
                });
            }
        });
    }
    $('#closeButton3').click(function () {

        $('#baski-goster').modal('hide');
    });

    $('#baskigoster').click(function () {
        if (options3 !== "") {
            $.getJSON(" {{ URL::to('edestek/kartbaski') }}/" + options3, function (event) {
                switch (event.kartbaski.edestekbaskitur_id) {
                    case '1':
                    case '2':
                    case '4':
                    case '5':
                    case '7':
                    case '8':
					case '9':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/baski')}}/" + event.kartbaski.onresim;
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/baski')}}/" + event.kartbaski.arkaresim;
                        break;
                    case '3':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/manasbaskion.png')}}";
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/manasbaskiarka.png')}}";
                        break;
                    case '6':
                        document.getElementById("baskion").src = "";
                        document.getElementById("baskiarka").src = "";
                        break;
                    case '10':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/mifaremanason.png')}}";
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/mifaremanasarka.png')}}";
                        break;
                }
            });
            $('#baski-goster').modal('show');
        } else {

            toastr.options = {
                closeButton: true, debug: false, positionClass: "toast-top-right", onclick: null,
                showDuration: "1000", hideDuration: "1000", timeOut: "5000", extendedTimeOut: "1000",
                showEasing: "swing", hideEasing: "linear", showMethod: "fadeIn", hideMethod: "fadeOut"
            };
            toastr['warning']('Baskı Adı Seçilmedi', 'Kart Baskısı Gösterme Hatası');
        }
    });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-eye"></i>Kart Baskı Bilgisi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Firma Adı</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ $kayit->edestekmusteri->musteriadi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Siparişi Oluşturan</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ $baski->plasiyer->plasiyeradi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Sipariş Tarihi</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ date("d-m-Y", strtotime($baski->siparistarihi))  }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Baskı Adı</label>
                    <label id="baskitur" class="col-md-5 col-xs-12" style="padding-top: 7px">{{ $baski->edestekkartbaski->edestekbaskitur->adi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Baskı Örneği</label>
                    <div class="col-md-7">
                        <a href="" id="baskigoster" data-toggle="modal" type="button" class="btn btn-info "><i class="fa fa-eye" style="padding-right: 2px"></i>Göster</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Miktar</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ $baski->miktar }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Teslim Tarihi</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ date("d-m-Y", strtotime($baski->teslimtarihi))  }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Personel</label>
                    <label class="col-md-5 col-xs-12" style="padding-top: 7px">{{ $baski->edestekpersonel->adisoyadi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Durum</label>
                    <label class="col-md-4 col-xs-12" style="padding-top: 7px">@if($baski->durum=='0'){{ 'Bekliyor' }} @elseif($baski->durum=='1'){{ 'Tamamlandı' }} @endif </label>
                    <label class="control-label col-md-2">Harcanan Süre</label>
                    <label class="col-md-4 col-xs-12" style="padding-top: 7px"> {{ $kayit->sure.' Dakika' }} </label>
                </div>
                <div class="form-group {{ $kayit->durum=="1" ? "hide" : "" }}">
                    <label class="control-label col-md-2">Durum Açıklaması</label>
                    <label class="col-md-9 col-xs-12" style="padding-top: 7px"> {{ $kayit->durum_aciklama }} </label>
                </div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('edestek/edestekkayit')}}" class="btn btn-success">Tamam</a>
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

<div class="modal fade" id="baski-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-image"></i> Kart Baskı Örneği
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-5 col-md-offset-1">
                                            <div class="thumbnail" style="width: 400px; height: 256px;">
                                                <img id="baskion" src="" alt=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class=" thumbnail" style="width: 400px; height: 256px;">
                                                <img id="baskiarka" src="" alt=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="button" id="closeButton3" class="btn default">Kapat</button>
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
