@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Netsis Cari <small> Bilgi Düzenleme</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/subedatabase/form-validation-8.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
    FormValidationSubeDatabase.init();
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
            <i class="fa fa-pencil"></i>Netsis Cari Bilgi Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('subedatabase/cariduzenle/'.$netsiscari->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <input class="hide" id="subekodu" name="subekodu" value="{{$sube ? $sube->subekodu : 1}}">
                        <label class="control-label col-xs-4">Cari Kodu:</label>
                        <label class="col-xs-8 carikod" style="padding-top: 9px">{{$netsiscari->carikod}}</label>
                        <div class="hide">
                            <i class="fa"></i><input type="text" id="carikod" name="carikod" value="{{$netsiscari->carikod}}" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-xs-12">
                        <label class="control-label col-xs-2">Cari Adı: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-6">
                            <i class="fa"></i><input type="text" id="cariadi" name="cariadi" value="{{Input::old('cariadi') ? Input::old('cariadi')  : $netsiscari->cariadi }}"  maxlength="100" data-required="1" class="form-control">
                        </div>
                        <label class="col-xs-4" style="color: red;padding-top: 7px">{{ $netsiscari->aciklama }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Telefon:</label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="telefon" name="telefon" value="{{Input::old('telefon') ? Input::old('telefon') :$netsiscari->tel }}" maxlength="20" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Mail Adresi:</label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="email" name="email" value="{{Input::old('email') ? Input::old('email') :$netsiscari->email }}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Vergi Dairesi: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="vergidairesi" name="vergidairesi" value="{{Input::old('vergidairesi') ? Input::old('vergidairesi') :$netsiscari->vergidairesi }}" maxlength="50" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Vergi No: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="vergino" name="vergino" value="{{Input::old('vergino') ? Input::old('vergino') :$netsiscari->vergino}}" maxlength="10" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Adresi: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="adres" name="adres" value="{{Input::old('adres') ? Input::old('adres') :$netsiscari->adres }}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Tc Kimlik No: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="tckimlikno" name="tckimlikno" value="{{Input::old('tckimlikno') ? Input::old('tckimlikno') : $netsiscari->tckimlikno}}" maxlength="15" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İl: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="il" name="il" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($sehirler as $sehir)
                                    @if((Input::old('il') ?  Input::old('il') : ($netsiscari->cariil ? $netsiscari->cariil->SEHIRKODU : ''))==$sehir->SEHIRKODU )
                                        <option value="{{ $sehir->SEHIRKODU }}" selected>{{ $sehir->SEHIRADI }}</option>
                                    @else
                                        <option value="{{ $sehir->SEHIRKODU }}">{{ $sehir->SEHIRADI }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlçe: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="ilce" name="ilce" value="{{(Input::old('ilce') ? Input::old('ilce') :$netsiscari->ilce) }}" maxlength="50" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Posta Kodu:</label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="postakodu" name="postakodu" value="{{Input::old('postakodu') ? Input::old('postakodu') : $netsiscari->postakodu}}" maxlength="8" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Vade Günü:</label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="vadegunu" name="vadegunu" value="{{Input::old('vadegunu') ? Input::old('vadegunu') : $netsiscari->vadegunu}}" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Yetkili Adı:</label>
                        <div class="col-xs-8">
                            <input type="text" id="yetkili" name="yetkili" value="{{Input::old('yetkili') ? Input::old('yetkili') : $netsiscari->yetkiliadi }}" maxlength="50" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Yetkili Telefonu:</label>
                        <div class="col-xs-8">
                            <input type="text" id="yetkilitel" name="yetkilitel" value="{{Input::old('yetkilitel') ? Input::old('yetkilitel') : $netsiscari->yetkilitel}}" maxlength="50" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Cari Tipi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="caritipi" name="caritipi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="A" {{ (Input::old('caritipi') ? Input::old('caritipi') : $netsiscari->caridurum)=="A" ? "selected" : "" }}>Alıcı</option>
                            <option value="S" {{ (Input::old('caritipi') ? Input::old('caritipi') : $netsiscari->caridurum)=="S" ? "selected" : "" }}>Satıcı</option>
                            <option value="D" {{ (Input::old('caritipi') ? Input::old('caritipi') : $netsiscari->caridurum)=="D" ? "selected" : "" }}>Diğer</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Cari Durumu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="A" {{ (Input::old('durum') ? Input::old('durum') : $netsiscari->caridurum)=="A" ? "selected" : "" }}>Aktif</option>
                            <option value="B" {{ (Input::old('durum') ? Input::old('durum') : $netsiscari->caridurum)=="B" ? "selected" : "" }}>Fatura Kilitli</option>
                            <option value="C" {{ (Input::old('durum') ? Input::old('durum') : $netsiscari->caridurum)=="C" ? "selected" : "" }}>Cari Kilitli</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">E-Fatura:</label>
                    <label class="col-xs-8" style="padding-top:7px">
                        <input type="checkbox" id="efatura" name="efatura" {{(Input::old('efatura') ?  Input::old('efatura') : ($netsiscari->aciklama=='E-FATURA MUKELLEFI' ? true : false)) ? "checked" : ""}}/> E-Fatura Mükellefi
                    </label>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                    <a href="{{ URL::to('subedatabase/netsiscari')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Netsis Cari Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Netsis Cari Bilgisi Kaydedilecektir?
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
