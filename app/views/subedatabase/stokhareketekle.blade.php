@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Stok Hareketi <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('pages/subedatabase/form-validation-9.js') }}"></script>
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
        $(".kaydet").prop('disabled',true);
        var count=parseInt($("#count").val());
        var cnt = count+1;
        $('.ekle').click(function(){
            var newRow="";
            newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
                '<div id="collapse_'+count+'" class="panel-collapse in"><div class="panel-body"><div class="form-group">'+
                '<label class="col-sm-2 col-xs-4 control-label">Ürün Adı:<span class="required" aria-required="true"> * </span></label>'+
                '<div class="input-icon right col-sm-7 col-xs-6"><i class="fa"></i><select class="form-control select2me urunadi urunadi'+count+'" id="urunadi'+count+'" name="urunadi[]" tabindex="-1" title="">'+
                '<option value="">Seçiniz...</option>'+
                    @foreach($urunler as $urun)
                        '<option data-id="{{ $urun->depokodu }}" data-stok="{{intval($urun->stok->BAKIYE)}}" value="{{ $urun->id }}">{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.' ('.intval($urun->stok->BAKIYE).')'}}</option>'+
                    @endforeach
                        '</select></div>' +
                '<label class="col-sm-1 col-xs-2"><a class="btn red satirsil">Sil</a></label></div>'+
                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Depo Kodu:<span class="required" aria-required="true"> * </span></label><div class="col-xs-4">' +
                '<input type="text" id="depokodu'+(count)+'" name="depokodu[]" class="form-control depokodu" readonly></div></div>' +
                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Miktarı:</label><div class="col-xs-4">' +
                '<input type="text" id="miktar'+(count)+'" name="miktar[]" maxlength="3" class="form-control miktar" value="1"></div>'+
                '</div>'+
                '</div></div>'+
                '</div></div></div>';
            cnt++;
            count++;
            $('.count').html(count+' Adet');
            $('.sayaclar').append(newRow);
            $('select.valid'+(count-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('.sayaclar').find('input:checkbox').uniform();
            $.uniform.update();
            $(".miktar").inputmask("mask", {
                mask:"9",repeat:3,greedy:!1
            });
            $("#urunadi"+(count-1)).select2();
            $('.urunadi').on('change', function () {
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                var depokodu = $(this).find("option:selected").data('id');
                $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
                $("#depokodu"+(no)).val(depokodu);
            });
            $('.miktar').on('change', function () {
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
            });
            $('.satirsil').click(function(){
                if($('.sayaclar .sayaclar_ek').size()>0){
                    var sayac=$(this).closest('.sayaclar_ek');
                    var adet = sayac.children('.adet').val();
                    sayac.children('.adet').val(0);
                    sayac.remove();
                    cnt-=adet;
                    count-=adet;
                    $("#count").val(count);
                    if(count===0)
                        $(".kaydet").prop('disabled',true);
                    var j=0;
                    $('.sayaclar .sayaclar_ek').each(function(){
                        var id=$( this ).children('.no').val();
                        $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                        $( this).children('.panel-collapse').attr('id','collapse_'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.urunadi').attr('id','urunadi'+j).attr('name','urunadi[]');
                        $( this).children('div').children('div').children('div').children('div').children('.urunadi').removeClass('urunadi'+id).addClass('urunadi'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.depokodu').attr('id','depokodu'+j).attr('name','depokodu[]');
                        $( this).children('div').children('div').children('div').children('div').children('.depokodu').removeClass('depokodu'+id).addClass('depokodu'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.miktar').attr('id','miktar'+j+'').attr('name','miktar[]');
                        $( this ).children('.no').val(j);
                        j++;
                    });
                }
            });
            if(count>0){
                $(".kaydet").prop('disabled',false);
            }
            else{
                $(".kaydet").prop('disabled',true);
            }
            $("#count").val(count);
            $("select").on("select2-close", function () { $(this).valid(); });
        });
        $(".tumsil").click(function(){
            while($('.sayaclar .sayaclar_ek').size()>0){
                $('.sayaclar .sayaclar_ek:last').remove();
                cnt--;
                count--;
            }
            $("#count").val(0);
            $(".kaydet").prop('disabled',true);
        });
        if(count>0){
            $(".kaydet").prop('disabled',false);
        }
        else{
            $(".kaydet").prop('disabled',true);
        }
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Yeni Stok Hareketi Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('subedatabase/stokhareketekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input class="hide" id="subekodu" name="subekodu" value="{{$sube ? $sube->subekodu : 1}}">
                    <label class="control-label col-xs-4">Hareket Türü:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="harekettur" name="harekettur" tabindex="-1" title="">
                            <option value=""  {{Input::old('harekettur')=="" ? "selected" : ""}}>Seçiniz...</option>
                            <option value="A" {{Input::old('harekettur')=="A" ? "selected" : ""}}>A - Devir</option>
                            <option value="B" {{Input::old('harekettur')=="B" ? "selected" : ""}}>B - Depolar</option>
                            <option value="C" {{Input::old('harekettur')=="C" ? "selected" : ""}}>C - Üretim</option>
                            <option value="D" {{Input::old('harekettur')=="D" ? "selected" : ""}}>D - Muhtelif</option>
                            <option value="F" {{Input::old('harekettur')=="F" ? "selected" : ""}}>F - Konsinye</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Masraf Merkezi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="masraf" name="masraf" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($masrafmerkezi as $masraf)
                                @if(Input::old('masraf')==$masraf->MKOD )
                                    <option value="{{ $masraf->MKOD }}" selected>{{ $masraf->MKOD." - ".$masraf->ACIKLAMA }}</option>
                                @else
                                    <option value="{{ $masraf->MKOD }}">{{ $masraf->MKOD." - ".$masraf->ACIKLAMA }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Hareket Tarihi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input id="tarih" type="text" name="tarih" class="form-control" value="{{Input::old('tarih') ? Input::old('tarih') : date("d-m-Y") }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">GC Kodu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="gckod" name="gckod" tabindex="-1" title="">
                            <option value=""  {{Input::old('gckod')=="" ? "selected" : ""}}>Seçiniz...</option>
                            <option value="G" {{Input::old('gckod')=="G" ? "selected" : ""}}>Giriş</option>
                            <option value="C" {{Input::old('gckod')=="C" ? "selected" : ""}}>Çıkış</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Açıklama 1:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama" value="{{Input::old('aciklama') }}" data-required="1" class="form-control" maxlength="100">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Açıklama 2:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama2" value="{{Input::old('aciklama2') }}" data-required="1" class="form-control" maxlength="100">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Açıklama 3:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama3" value="{{Input::old('aciklama3') }}" data-required="1" class="form-control" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4"> Eklenecek Ürünler </label>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                    @for($i=0;$i<(int)(Input::old('count'));$i++)
                        <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{Input::old('urunadi.'.$i.'').' - '.Input::old('miktar.'.$i.''). ' ADET' }} </a>
                                </h4>
                            </div>
                            <div id="collapse_{{$i}}" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 col-xs-4 control-label">Ürün Adı:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-sm-7 col-xs-6">
                                            <i class="fa"></i><select class="form-control select2me urunadi urunadi{{$i}}" id="urunadi{{$i}}" name="urunadi[]" tabindex="-1" title="">
                                                @foreach($urunler as $urun)
                                                    @if((Input::old('urunadi.'.$i.''))==$urun->id)
                                                        <option data-id="{{$urun->depokodu}}" data-stok="{{intval($urun->stok->BAKIYE)}}" value="{{ $urun->id }}" selected>{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.'('.intval($urun->stok->BAKIYE).')'}}</option>
                                                    @else
                                                        <option data-id="{{$urun->depokodu}}" data-stok="{{intval($urun->stok->BAKIYE)}}" value="{{ $urun->id }}" >{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.'('.intval($urun->stok->BAKIYE).')' }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="col-sm-1 col-xs-2"><a class="btn red satirsil">Sil</a></label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="control-label col-xs-4">Depo Kodu:<span class="required" aria-required="true"> * </span></label>
                                        <div class="col-xs-4">
                                            <input type="text" id="depokodu{{$i}}" name="depokodu[]" class="form-control depokodu" value="{{Input::old('depokodu.'.$i.'')}}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Miktarı:</label>
                                        <div class="col-xs-4">
                                            <input type="text" id="miktar{{$i}}" name="miktar[]" maxlength="3" class="form-control miktar" value="{{Input::old('miktar.'.$i.'')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="form-group">
                    <div class="col-sm-3 col-xs-12 control-label" style="text-align: center;">
                        <a class="btn green ekle">&nbsp Ekle &nbsp </a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                    <a href="{{ URL::to('subedatabase/stokhareket')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Stok Hareketi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Stok Hareketi Bilgisi Kaydedilecektir?
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
