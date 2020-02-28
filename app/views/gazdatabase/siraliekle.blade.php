@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Sayaç <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/gazdatabase/form-validation-13.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationGazDatabase.init();
});
</script>
<script>
    $(document).ready(function() {
        $(".kaydet").prop('disabled',true);
        $('#baslangic').on('change',function(){
            var baslangic=parseInt($(this).val());
            var bitis=parseInt($('#bitis').val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $(".kaydet").prop('disabled',true);
                }else{
                    var artis = $('#spinner').spinner('value');
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".kaydet").prop('disabled',false);
                }
            }
        });

        $('#bitis').on('change',function(){
            var baslangic=parseInt($('#baslangic').val());
            var bitis=parseInt($(this).val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $(".kaydet").prop('disabled',true);
                }else{
                    var artis = $('#spinner').spinner('value');
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".kaydet").prop('disabled',false);
                }
            }
        });

        $('#spinner').on('change',function(){
            var artis = $(this).spinner('value');
            var baslangic=parseInt($('#baslangic').val());
            var bitis=parseInt($('#bitis').val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $(".kaydet").prop('disabled',true);
                }else{
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".kaydet").prop('disabled',false);
                }
            }
        });
        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            $('.sayacadi').val(id);
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#uretimtarihi').on('change', function() { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Sıralı Şekilde Sayaç Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('gazdatabase/siraliekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-xs-4">Seri No Başlangıç: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="baslangic" name="baslangic" value="" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Bitiş: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="bitis" name="bitis" value="" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Artış Miktarı: <span class="required" aria-required="true"> * </span></label>
                    <div id="spinner" class="input-icon right col-xs-6">
                        <i class="fa"></i><div class="input-group input-small">
                            <input type="text" name="artis" value="{{ (Input::old('artis') ? Input::old('artis') : 1) }}" class="spinner-input form-control" maxlength="3" readonly="">
                            <div class="spinner-buttons input-group-btn btn-group-vertical">
                                <button type="button" class="btn spinner-up btn-xs blue">
                                    <i class="fa fa-angle-up"></i>
                                </button>
                                <button type="button" class="btn spinner-down btn-xs blue">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Sayısı:</label>
                    <label class="col-xs-8 sayacsayi" style="margin-top: 9px;color: red">0</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Üretim Tarihi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input id="uretimtarihi" type="text" name="uretimtarihi" class="form-control" value="{{Input::old('uretimtarihi') ? Input::old('uretimtarihi') : '' }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Üretim Yeri:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyer)
                                @if(Input::old('uretimyer')==$uretimyer->id )
                                    <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                @else
                                    <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-4 col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if(Input::old('sayacadi')==$sayacadi->id )
                                    <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                                    <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
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
                        <button type="submit" class="btn green kaydet">Kaydet</button>
                        <a href="{{ URL::to('mekanikdatabase/sayaclar')}}" class="btn default">Vazgeç</a>
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
