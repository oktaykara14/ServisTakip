@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Kalibrasyon Hurda Kayıt <small>Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
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
@stop

@section('page-script')
<script src="{{ URL::to('pages/kalibrasyon/form-validation-3.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationKalibrasyon.init();
});
</script>
<script>
    $(document).ready(function() {

        $('#formsubmit').click(function () {
            $('#durum').val('0');
            $('#form_sample').submit();
        });
        $('#deletesubmit').click(function () {
            $('#durum').val('1');
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
            <i class="fa fa-pencil"></i>Kalibrasyon Hurda Kayıt Düzenleme
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('kalibrasyon/hurdaduzenle/'.$grup->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4"><b>Cari Adı:</b></label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $grup->netsiscari->cariadi}}</label>
                    <input class="hide" id="grupid" name="grupid" value="{{ $grup->id}}"/>
                    <input type="text" id="secilen" name="secilen" value="{{$kalibrasyon->id}}" data-required="1" class="form-control hide">
                    <input type="text" id="durum" name="durum" value="0" class="hide">
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Seri No:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyon->kalibrasyon_seri}} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Adı:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyon->sayacadi->sayacadi}}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Hurda Nedeni:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8"><i class="fa"></i>
                        <select class="form-control select2me hurdaneden" id="hurdaneden" name="hurdaneden" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($hurdanedenleri as $neden)
                                @if($hurdakayit->hurdanedeni_id === $neden->id)
                                <option value="{{ $neden->id }}" selected>{{ $neden->nedeni }}</option>
                                @else
                                <option value="{{ $neden->id }}">{{ $neden->nedeni }}</option>
                                @endif
                            @endforeach
                            </select>
                    </div>
                </div>

                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="button" class="btn red" data-toggle="modal" data-target="#delete">Hurda Kayıdı Sil</button>
                    <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                    <a href="{{ URL::to('kalibrasyon/kalibrasyondetay/'.$grup->id.'')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Kalibrasyon Hurda Kayıt Bilgisi Güncellenecek</h4>
                </div>
                <div class="modal-body">
                   Hurda Sayaç Bilgisi Güncellenecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Hurda Sayaç Kayıt Bilgisi Silinecek</h4>
                </div>
                <div class="modal-body">
                    Kayıtlı Hurda Sayaç Bilgisi Silinecektir?
                </div>
                <div class="modal-footer">
                    <a id="deletesubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop

