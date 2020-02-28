@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Sayaç Kayıt <small>Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
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

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-3.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationSube.init();
});
</script>
<script>
    $(document).ready(function() {
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#form_sample').valid();
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Sayac Kayıdı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/sayackayitduzenle/'.$sayacgelen->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-xs-4">Geliş Tarihi:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $sayacgelen->depotarihi ? date("d-m-Y", strtotime($sayacgelen->depotarihi)) : '' }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyer)
                                @if((Input::old('uretimyer') ? Input::old('uretimyer') : $sayacgelen->uretimyer_id )==$uretimyer->id )
                                    <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                @else
                                    <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">İstek:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $servisstokkod->stokadi }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Adı:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $sayacadi->sayacadi }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Çapı:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $sayaccap->capadi }}</label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Seri No:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-3 col-xs-8">
                        <i class="fa"></i><input type="text" id="serino" name="serino" maxlength="10" value="{{ Input::old('serino') ? Input::old('serino') : $sayacgelen->serino }}" class="form-control serino">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sökülme Nedeni:</label>
                    <div class="col-xs-8">
                        <input type="text" id="neden" name="neden" maxlength="200" value="{{ Input::old('neden') ? Input::old('neden') : $sayacgelen->sokulmenedeni }}" class="form-control neden">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Servis Sayacı Takılma Tarihi:</label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><div class="input-group input-medium date date-picker takilmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input type="text" id="takilmatarihi" name="takilmatarihi" class="form-control" value="{{Input::old('takilmatarihi') ? Input::old('takilmatarihi') : ($sayacgelen->takilmatarihi ? date("d-m-Y", strtotime($sayacgelen->takilmatarihi)) : '') }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Servis Sayacı Takılma Endeksi:</label>
                    <div class="col-xs-8">
                        <input type="text" id="endeks" name="endeks" maxlength="15" value="{{ Input::old('endeks') ? Input::old('endeks') : $sayacgelen->endeks }}" class="form-control endeks">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                    <a href="{{ URL::to('sube/sayackayit')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Sayaç Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Sayaç Kayıdı Bilgileri Kaydedilecektir?
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
