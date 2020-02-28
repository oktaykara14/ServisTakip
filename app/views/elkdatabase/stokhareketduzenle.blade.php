@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Elektrik Stok Hareketi <small>Düzenleme Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/elkdatabase/form-validation-11.js') }}"></script>
@stop

@section('scripts')
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        Demo.init(); // init demo features
        QuickSidebar.init(); // init quick sidebar
        FormValidationElkDatabase.init();
    });
</script>
<script>
    $(document).ready(function() {
        var parca = $('#parca').val();
        if (parca !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('elkdatabase/stokadi') }}",{degisenid:parca}, function (event) {
                if (event.durum) {
                    var netsisstokkod = event.netsisstokkod;
                    $(".stokadi").html(netsisstokkod.adi);
                } else {
                    $(".stokadi").html('');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }
        $('#parca').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('elkdatabase/stokadi') }}",{degisenid:id}, function (event) {
                    if (event.durum) {
                        var netsisstokkod = event.netsisstokkod;
                        $(".stokadi").html(netsisstokkod.adi);
                    } else {
                        $(".stokadi").html('');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $(".stokadi").html('');
            }
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
                <i class="fa fa-pencil"></i>Stok Hareketi Düzenle
            </div>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form action="{{ URL::to('elkdatabase/stokhareketduzenle/'.$stokgiriscikis->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        Girilen Bilgilerde Hata Var.
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        Bilgiler Doğru!
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Parça Adı:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parca" name="parca" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($parcalar as $parca)
                                    @if(( Input::old('parca') ? Input::old('parca') : $stokgiriscikis->degisenler_id)==$parca->id )
                                        <option value="{{ $parca->id }}" selected>{{ $parca->tanim }}</option>
                                    @else
                                        <option value="{{ $parca->id }}">{{ $parca->tanim }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Stok Adı:</label>
                        <label class="col-xs-8 stokadi" style="padding-top: 9px"></label>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">GC Kodu:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="gckod" name="gckod" tabindex="-1" title="">
                                <option value=""  {{ ( Input::old('gckod') ? Input::old('gckod') : $stokgiriscikis->gckod)=="" ? "selected" : ""}}>Seçiniz...</option>
                                <option value="G" {{ ( Input::old('gckod') ? Input::old('gckod') : $stokgiriscikis->gckod)=="G" ? "selected" : ""}}>Giriş</option>
                                <option value="C" {{ ( Input::old('gckod') ? Input::old('gckod') : $stokgiriscikis->gckod)=="C" ? "selected" : ""}}>Çıkış</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Miktar:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="miktar" name="miktar" value="{{( Input::old('miktar') ? Input::old('miktar') : $stokgiriscikis->miktar) }}" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Açıklama:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama" value="{{( Input::old('aciklama') ? Input::old('aciklama') : $stokgiriscikis->aciklama) }}" data-required="1" class="form-control" maxlength="250">
                        </div>
                    </div>
                    <div class="form-group">{{ Form::token() }}</div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center">
                            <button type="submit" class="btn green">Kaydet</button>
                            <a href="{{ URL::to('elkdatabase/stokgirisi')}}" class="btn default">Vazgeç</a>
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
