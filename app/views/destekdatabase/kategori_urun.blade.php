@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Yazılım Destek <small>Kategoriye Ait Ürünler Bilgi Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/jstree/dist/themes/default/style.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jstree/dist/jstree.min.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/destekdatabase/form-validation-3.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
    Demo.init(); // init demo features
    QuickSidebar.init(); // init quick sidebar
    FormValidationDestekDatabase.init();
});
</script>
<script>
    $(document).ready(function() {
        $(document).on("click", ".ekle", function () {
            var id = $(this).data('id');
            var kategori = $(this).data('adi');
            var status = $(this).data('status');
            // kategori 0 : urunleri olan 1 : alt kategorisi olan 2 : hiçbir şeyi yok
            // urun 3 : parca varsa 4: yoksa
            // parca 5 : parca varsa  6: yoksa
            $('#kategori_adi').html(kategori);
            $('#kategori').val(id);
            if(status===0){
                $('.kategorisec').addClass('hide');
                $('.urunsec').removeClass('hide');
            }else if(status===1){
                $('.kategorisec').removeClass('hide');
                $('.urunsec').addClass('hide');
            }else{
                $('.kategorisec').removeClass('hide');
                $('.urunsec').removeClass('hide');
            }
        });

        $('#eklenecek').on('change', function () {
            var eklenecek = $(this).val();
            if(eklenecek==="0"){
                $('.kategorisec2').addClass('hide');
                $('.urunsec2').removeClass('hide');
            }else if(eklenecek==="1"){
                $('.kategorisec2').removeClass('hide');
                $('.urunsec2').addClass('hide');
            }else{
                $('.kategorisec2').addClass('hide');
                $('.urunsec2').addClass('hide');
            }
        });

        $(document).on("click", ".urun-sil", function () {
            var id = $(this).data('id');
            $(".modal-footer #urunsil").attr('href',"{{ URL::to('destekdatabase/kategoriurunsil') }}/"+id );
        });
        $(document).on("click", ".kategori-sil", function () {
            var id = $(this).data('id');
            $(".modal-footer #kategorisil").attr('href',"{{ URL::to('destekdatabase/kategorisil') }}/"+id );
        });
        $(document).on("click", ".kategoriguncelle", function (){
            var id = $(this).data('id');
            var kategori = $(this).data('value');
            $('#kategoriduzenle').val(kategori);
            $('#kategoriduzenleid').val(id);
        });
        $(document).on("click", ".urunguncelle", function (){
            var id = $(this).data('id');
            var urun = $(this).data('value');
            $('#urunduzenle').val(urun);
            $('#urunduzenleid').val(id);
        });

        $(document).on("click", ".kategoriurunekle", function () {
            $.blockUI();
        });
        $(document).on("click", ".urunsil", function () {
            $.blockUI();
        });
        $(document).on("click", ".kategorisil", function () {
            $.blockUI();
        });
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
                    <i class="fa fa-tag"></i>Kategoriler ve Ürünleri
                </div>
            </div>
            <div class="portlet-body">
                <div id="tree_1" class="tree-demo" style="font-size: 18px;">
                    <ul>
                        <li data-jstree='{ "opened" : true }'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="-1" data-adi="Ürünler" data-status="1" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button> Ürünler
                            <ul id="tree">
                                {{ $tree }}
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END TABLE PORTLET-->
    </div>
</div>
@stop

@section('modal')
<div class="modal fade" id="portlet-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Kategoriye Ürün - Alt Kategori Ekle
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="{{ URL::to('destekdatabase/kategoriurunekle') }}" id="form_sample" method="post" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <h3 class="form-section">Kategoriye Yeni Ürün ya da Alt Kategori Ekle</h3>
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Kategori:</label>
                                        <label id="kategori_adi" class="col-md-6" style="padding-top: 9px"></label>
                                        <div class=" col-md-6 hide">
                                            <input type="text" id="kategori" name="kategori" value="{{Input::old('kategori')}}" data-required="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Ne Eklenecek?:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-md-6">
                                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="eklenecek" name="eklenecek" tabindex="4" title="">
                                                <option value="">Seçiniz...</option>
                                                <option class="kategorisec" value="1">Alt Kategori</option>
                                                <option class="urunsec" value="0" >Ürün</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group kategorisec2 hide">
                                        <label class="control-label col-md-3">Alt Kategori:</label>
                                        <div class=" col-md-6">
                                            <input type="text" id="altkategori" name="altkategori" value="{{Input::old('altkategori')}}" data-required="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group urunsec2 hide">
                                        <label class="control-label col-md-3">Ürün:</label>
                                        <div class=" col-md-6">
                                            <input type="text" id="urun" name="urun" value="{{Input::old('urun')}}" data-required="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="submit" class="btn green kategoriurunekle">Kaydet</button>
                                                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
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
<div class="modal fade" id="kategoriguncelle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-pencil"></i>Kategori Düzenle
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <h3 class="form-section">Kategori Güncelle</h3>
                                    <input class="hide" id="kategoriduzenleid" name="kategoriduzenleid"/>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2 col-xs-4">Kategori Adı:</label>
                                        <div class="col-xs-8">
                                            <input type="text" id="kategoriduzenle"  name="kategoriduzenle" value="{{Input::old('kategoriduzenle')}}" data-required="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="button" class="btn green kategoriguncelle" data-dismiss="modal">Değiştir</button>
                                                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
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
<div class="modal fade" id="urunguncelle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-pencil"></i>Ürün Düzenle
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <h3 class="form-section">Ürün Güncelle</h3>
                                    <input class="hide" id="urunduzenleid" name="urunduzenleid"/>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2 col-xs-4">Ürün Adı:</label>
                                        <div class="col-xs-8">
                                            <input type="text" id="urunduzenle"  name="urunduzenle" value="{{Input::old('urunduzenle')}}" data-required="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="button" class="btn green urunguncelle" data-dismiss="modal">Değiştir</button>
                                                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
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
<div class="modal fade" id="portlet-urun-sil" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Ürün Silinecek</h4>
            </div>
            <div class="modal-body">
                Seçilen Ürünü Kategoriden Silmek İstediğinizden Emin Misiniz?
            </div>
            <div class="modal-footer">
                <a id="urunsil" href="" type="button" class="btn blue urunsil">Sil</a>
                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="portlet-kategori-sil" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Kategori Silinecek</h4>
                    </div>
                    <div class="modal-body">
                             Seçilen Kategoriyi Silmek İstediğinizden Emin Misiniz?
                    </div>
                    <div class="modal-footer">
                            <a id="kategorisil" href="" type="button" class="btn blue kategorisil">Sil</a>
                            <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                    </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@stop
