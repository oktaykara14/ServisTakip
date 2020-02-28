@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Elektrik Arıza Kayıt <small>Detay Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/elkservis/form-validation-4.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
    FormValidationElkServis.init();
});
</script>
<script>
    $("#arizalar").prop("disabled", "disabled");
    $("#degisenler").prop("disabled", "disabled");
    $(document).ready(function() {
        //$("#yapilanlar").prop("disabled", "disabled");
        //$("#uyarilar").prop("disabled", "disabled");

        $('#t1deger').on('change', function () {
            var t1 = parseFloat(($(this).val()).replace(',', '.'));
            var t2 = parseFloat(($('#t2deger').val()).replace(',', '.'));
            var t3 = parseFloat(($('#t3deger').val()).replace(',', '.'));
            var t4 = parseFloat(($('#t4deger').val()).replace(',', '.'));
            var toplam = parseFloat((isNaN(t1) ? 0 : t1) + (isNaN(t2) ? 0 : t2) + (isNaN(t3) ? 0 : t3) + (isNaN(t4) ? 0 : t4)).toFixed(3);
            toplam = toplam.toString();
            toplam = toplam.replace('.', ',');
            $('#ttoplam').val(toplam);
        });
        $('#t2deger').on('change', function () {
            var t2 = parseFloat(($(this).val()).replace(',', '.'));
            var t1 = parseFloat(($('#t1deger').val()).replace(',', '.'));
            var t3 = parseFloat(($('#t3deger').val()).replace(',', '.'));
            var t4 = parseFloat(($('#t4deger').val()).replace(',', '.'));
            var toplam = parseFloat((isNaN(t1) ? 0 : t1) + (isNaN(t2) ? 0 : t2) + (isNaN(t3) ? 0 : t3) + (isNaN(t4) ? 0 : t4)).toFixed(3);
            toplam = toplam.toString();
            toplam = toplam.replace('.', ',');
            $('#ttoplam').val(toplam);
        });
        $('#t3deger').on('change', function () {
            var t3 = parseFloat(($(this).val()).replace(',', '.'));
            var t2 = parseFloat(($('#t2deger').val()).replace(',', '.'));
            var t1 = parseFloat(($('#t1deger').val()).replace(',', '.'));
            var t4 = parseFloat(($('#t4deger').val()).replace(',', '.'));
            var toplam = parseFloat((isNaN(t1) ? 0 : t1) + (isNaN(t2) ? 0 : t2) + (isNaN(t3) ? 0 : t3) + (isNaN(t4) ? 0 : t4)).toFixed(3);
            toplam = toplam.toString();
            toplam = toplam.replace('.', ',');
            $('#ttoplam').val(toplam);
        });
        $('#t4deger').on('change', function () {
            var t4 = parseFloat(($(this).val()).replace(',', '.'));
            var t2 = parseFloat(($('#t2deger').val()).replace(',', '.'));
            var t3 = parseFloat(($('#t3deger').val()).replace(',', '.'));
            var t1 = parseFloat(($('#t1deger').val()).replace(',', '.'));
            var toplam = parseFloat((isNaN(t1) ? 0 : t1) + (isNaN(t2) ? 0 : t2) + (isNaN(t3) ? 0 : t3) + (isNaN(t4) ? 0 : t4)).toFixed(3);
            toplam = toplam.toString();
            toplam = toplam.replace('.', ',');
            $('#ttoplam').val(toplam);
        });
        $('#yapilanlar').on('change', function () {
            $(this).valid();
        });
        $('#uyarilar').on('change', function () {
            $(this).valid();
        });
        var selected_val = $("#yapilanlist").val().split(',');
        $("#yapilanlist").val('');
        for (var q = 0; q < selected_val.length; q++) {
            $('#yapilanlar').multiSelect('select', selected_val[q]);
        }
        selected_val = $("#uyarilist").val().split(',');
        $("#uyarilist").val('');
        for (q = 0; q < selected_val.length; q++) {
            $('#uyarilar').multiSelect('select', selected_val[q]);
        }
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $('#form_sample').valid();
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Arıza Kayıdı Bilgisi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('elkservis/arizakayitgoster/'.$arizakayit->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate" enctype="multipart/form-data">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Seri No:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $arizakayit->sayacgelen->serino }} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Geliş Tarihi:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $arizakayit->sayacgelen->depotarihi ? date("d-m-Y", strtotime($arizakayit->sayacgelen->depotarihi)) : '' }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $arizakayit->netsiscari->cariadi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">İstek:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{  $arizakayit->servisstokkod->stokadi }} </label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Geliş Yeri:</label>
                    <label class="col-sm-4 col-xs-8" style="padding-top: 9px">{{  $arizakayit->uretimyer->yeradi }} </label>
                    <label class="control-label col-sm-2 col-xs-4">Üretim Tarihi:</label>
                    <label class="col-sm-4 col-xs-8" style="padding-top: 9px">{{  $arizakayit->sayac->uretimtarihi ? date("d-m-Y", strtotime($arizakayit->sayac->uretimtarihi)) : '' }} </label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı:</label>
                    <label class="col-sm-4 col-xs-8" style="padding-top: 9px">{{  $arizakayit->sayacadi->sayacadi }} </label>
                    <label class="control-label col-sm-2 col-xs-4">Garanti Durum:</label>
                    <label class="col-sm-4 col-xs-8" style="padding-top: 9px">{{$arizakayit->garanti==0 ? 'Dışında' : 'İçinde' }} </label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">T1 Tüketimi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="t1deger" name="t1deger" value="{{ Input::old('t1deger') ? Input::old('t1deger') : round($arizakayit->ilkkredi,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="7">
                    </div>
                    <label class="control-label col-sm-2 col-xs-4">T2 Tüketimi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="t2deger" name="t2deger" value="{{ Input::old('t2deger') ? Input::old('t2deger') :round($arizakayit->ilkharcanan,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="8">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">T3 Tüketimi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="t3deger" name="t3deger" value="{{ Input::old('t3deger') ? Input::old('t3deger') : round($arizakayit->ilkmekanik,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="9">
                    </div>
                    <label class="control-label col-sm-2 col-xs-4">T4 Tüketimi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="t4deger" name="t4deger" value="{{ Input::old('t4deger') ? Input::old('t4deger') : round($arizakayit->kalankredi,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="9">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Toplam Tüketim:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="ttoplam" name="ttoplam" value="{{ Input::old('ttoplam') ? Input::old('ttoplam') : round($arizakayit->harcanankredi,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="9">
                    </div>
                    <label class="control-label col-sm-2 col-xs-4">Kalan Kredi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><input type="text" id="kalankredi" name="kalankredi" value="{{ Input::old('kalankredi') ? Input::old('kalankredi') : round($arizakayit->mekanik,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="9">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Müşteri Açıklaması:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{$arizakayit->musteriaciklama }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Arıza Açıklaması:</label>
                    <div class="col-xs-8">
                        <input type="text" id="arizaaciklama" name="arizaaciklama" value="{{ Input::old('arizaaciklama') ? Input::old('arizaaciklama') : $arizakayit->arizaaciklama }}"
                               placeholder="Müşterinin göreceği arıza açıklaması" data-required="1" class="form-control" tabindex="13">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Arıza Tespiti:</label>
                    <div class="col-xs-8">
                        <select multiple="multiple" class="multi-select" id="arizalar" name="arizalar[]">
                            @foreach($arizakodlari as $arizakod)
                                {{$flag=0}}
                                @foreach($arizakayit->problemler as $problem)
                                    @if($arizakod->id==$problem)
                                        {{$flag=1}}
                                        <option data-id="{{ $arizakod->garanti }}" value="{{ $arizakod->id }}" selected>{{ $arizakod->tanim }}</option>
                                        @break;
                                    @endif
                                @endforeach
                                @if($flag==0)
                                    <option data-id="{{ $arizakod->garanti }}" value="{{ $arizakod->id }}">{{ $arizakod->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Değişen Parçalar:</label>
                    <div class="col-xs-8">
                        <select multiple="multiple" class="multi-select" id="degisenler" name="degisenler[]">
                            @foreach($degisenler as $degisen)
                                {{$flag=0}}
                                @foreach($arizakayit->degisenler as $degisenparca)
                                    @if($degisen->id==$degisenparca)
                                        {{$flag=1}}
                                        <option value="{{ $degisen->id }}" selected>{{ $degisen->tanim }}</option>
                                        @break;
                                    @endif
                                @endforeach
                                @if($flag==0)
                                    <option value="{{ $degisen->id }}">{{ $degisen->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Yapılan İşlemler:</label>
                    <div class="col-xs-8">
                        <select multiple="multiple" class="multi-select" id="yapilanlar" name="yapilanlar[]">
                            @foreach($yapilanlar as $yapilan)
                                {{$flag=0}}
                                @foreach($arizakayit->yapilanlar as $yapilanariza)
                                    @if($yapilan->id==$yapilanariza)
                                        {{$flag=1}}
                                        <option value="{{ $yapilan->id }}" selected>{{ $yapilan->tanim }}</option>
                                        @break;
                                    @endif
                                @endforeach
                                @if($flag==0)
                                    <option value="{{ $yapilan->id }}">{{ $yapilan->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input id="yapilanlist" name="yapilanlist" class="hide" value="{{ Input::old('yapilanlist') ? Input::old('yapilanlist') : $arizakayit->sayacyapilan ? $arizakayit->sayacyapilan->yapilanlar : ''}}">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sonuç ve Uyarılar:</label>
                    <div class="col-xs-8">
                        <select multiple="multiple" class="multi-select" id="uyarilar" name="uyarilar[]">
                            @foreach($uyarilar as $uyari)
                                {{$flag=0}}
                                @foreach($arizakayit->uyarilar as $uyarii)
                                    @if($uyari->id==$uyarii)
                                        {{$flag=1}}
                                        <option value="{{ $uyari->id }}" selected>{{ $uyari->tanim }}</option>
                                        @break;
                                    @endif
                                @endforeach
                                @if($flag==0)
                                    <option value="{{ $uyari->id }}">{{ $uyari->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input id="uyarilist" name="uyarilist" class="hide" value="{{ Input::old('uyarilist') ? Input::old('uyarilist') : $arizakayit->sayacuyari ? $arizakayit->sayacuyari->uyarilar : ''}}">
                </div>
                <div class="form-group">
                    @if($arizakayit->servistakip->eskiserino)
                        <label class="control-label col-sm-2 col-xs-4">Eski Seri No:</label>
                        <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $arizakayit->servistakip->eskiserino }}</label>
                    @endif
                </div>
                @if($arizakayit->resimler!=="")
                    <h4 class="form-section">Ekli Resimler <span style="font-size: 12px">Kayıt Sırasında Eklenen Resimler</span></h4>
                    @foreach($arizakayit->resimlist as $resim)
                        <div class="form-group">
                            <div class="col-xs-10">
                                <span>- {{$resim}}</span>
                                <a href="{{ URL::to('assets/arizaresim/'.$resim) }}" type="button" class="btn btn-warning" target="_blank">Göster</a>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Not:</label>
                    <div class="col-xs-8">
                        <input type="text" id="arizanot" name="arizanot" value="{{ Input::old('arizanot') ? Input::old('arizanot') : $arizakayit->arizanot }}" data-required="1" class="form-control" tabindex="26">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('elkservis/arizakayit')}}" class="btn default">Vazgeç</a>
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
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Arıza Kayıdı Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Arıza Kayıdı Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

