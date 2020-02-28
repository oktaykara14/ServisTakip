@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Ücretlendirme <small>Onay Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/ucretlendirme/form-validation-3.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
           Metronic.init(); // init metronic core componets
           Layout.init(); // init layout
           Demo.init(); // init demo features
           FormValidationUcretlendirme.init();
        });
    </script>
    <script>
        var table = $('#sample_editable_1');
        table.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": false,
            "ordering": false,
            "bProcessing": true,
            "ajax": {
                "url": "{{ URL::to('ucretlendirme/musterionaylist') }}",
                "type": "POST",
                "data": {
                    "ucretlendirilenid" : "@if(isset($ucretlendirilenid)){{$ucretlendirilenid}}@endif"
                }
            },
            "bServerSide": true,
            "bFilter" : false,
            "iDisplayLength": 5,
            "bInfo": false,
            "bPaginate": true,
            "language": {
                "emptyTable": "Veri Bulunamadı",
                "info": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
                "infoEmpty": "Kayıt Yok",
                "infoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
                "lengthMenu": "Sayfada _MENU_ Kayıt Göster",
                "paginate": {
                    "first": "İlk",
                    "last": "Son",
                    "previous": "Önceki",
                    "next": "Sonraki"
                },
                "search": "Bul:",
                "zeroRecords": "Eşleşen Kayıt Bulunmadı",
                "processing": "<h1><i class='fa fa-spinner fa-spin icon-lg-processing fa-fw'></i>İşlem Devam Ediyor...</h1>"

            },
            "columns": [
                {data: 'id', name: 'arizafiyat.id' },
                {data: 'ariza_serino', name: 'arizafiyat.ariza_serino'},
                {data: 'sayacadi', name: 'sayacadi.sayacadi'},
                {data: 'ggaranti', name: 'arizafiyat.ggaranti'},
                {data: 'toplamtutar', name: 'arizafiyat.toplamtutar'}
            ],
            "bLengthChange": false,
            "lengthMenu": [
                [5],
                [5]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_1_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $('body').toggleClass('page-header-fixed page-sidebar-closed-hide-logo page-sidebar-closed-hide-logo page-sidebar-closed');
        $('.page-sidebar-menu').toggleClass('page-sidebar-menu-closed');
        $('.page-sidebar-menu').attr('data-keep-expanded',false);
        $('.page-sidebar-menu').attr('data-auto-scroll',true);
        $('.page-sidebar-menu').attr('data-slide-speed',200);
        $('.onayla').click(function() {
            var form = $('#eklenendosya').val();
            if(form !==""){
                $.extend({
                    redirectPost: function(location, args)
                    {
                        var form = '';
                        form += '<input type="hidden" name="durum" value="'+args['durum']+'">';
                        form += '<input type="file" name="eklenendosya" value="'+args['eklenendosya']+'">';

                        $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                    }
                });
                var redirect = "";
                $.redirectPost(redirect, {eklenendosya: form,durum:'1',ireport:'1'});
            }
        });
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
    </script>
@stop

@section('content')
    <div class="portlet box">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-try"></i>Sayaç Servis Fiyatlandırma Ekranı
            </div>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->

            @if(isset($durum) && $durum==1)
                <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                    <div class="form-body">
                        <div class="form-group" style="height: 30px"></div>
                        <div class="form-group">
                            <div class="col-xs-10" style="font-size: 24px;text-align: center">
                                Manas Enerji Yönetimi Servis Birimi - Sayaç Servis Fiyatlandırma Ekranı
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-10 col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                                Sayaçların Fiyatlandırması yapılmıştır. Servis Birimi ile irtibata geçiniz.
                            </div>
                        </div>
                        <div class="form-group">{{ Form::token() }}</div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                        </div>
                    </div>
                </form>
            @elseif(isset($durum) && $durum==2)
                <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                    <div class="form-body">
                        <div class="form-group" style="height: 30px"></div>
                        <div class="form-group">
                            <div class="col-xs-10" style="font-size: 24px;text-align: center">
                                Manas Enerji Yönetimi Servis Birimi - Sayaç Servis Fiyatlandırma Ekranı
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-10 col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                                Sayaçların Fiyatlandırması Eski ya da Daha Fiyatlandırılmamış Olabilir. Servis Birimi ile irtibata geçiniz.
                            </div>
                        </div>
                        <div class="form-group">{{ Form::token() }}</div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                        </div>
                    </div>
                </form>
            @elseif(isset($durum) && $durum==-1)
                <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                    <div class="form-body">
                        <div class="form-group" style="height: 30px"></div>
                        <div class="form-group">
                            <div class="col-xs-10" style="font-size: 24px;text-align: center">
                                Manas Enerji Yönetimi Servis Birimi  - Sayaç Servis Fiyatlandırma Ekranı
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-xs col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                                Bu Fiyatlandırma Eski.Yenisi için Servis Birimi ile irtibata geçiniz.
                            </div>
                        </div>
                        <div class="form-group">{{ Form::token() }}</div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                        </div>
                    </div>
                </form>
            @elseif(isset($durum) && $durum==0)
            <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                <div class="form-body">
                    <div class="form-group" style="height: 30px"></div>
                    <div class="form-group">
                        <div class="col-md-xs" style="font-size: 24px;text-align: center">
                            Manas Enerji Yönetimi Servis Birimi - Sayaç Servis Fiyatlandırma Ekranı
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-10 col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                            Sayaçların Fiyatlandırması reddedilmiştir. Servis Birimi ile irtibata geçiniz.
                        </div>
                    </div>
                    <div class="form-group">{{ Form::token() }}</div>
                </div>
                <div class="form-actions">
                    <div class="row">
                    </div>
                </div>
            </form>
            @else
            <form action="{{ URL::to('musterionay') }}" id="form_sample" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        Girilen Bilgilerde Hata Var.
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        Bilgiler Doğru!
                    </div>
                    <div class="form-group" style="height: 30px"></div>
                    <div class="form-group">
                        <div class="col-xs-10" style="font-size: 24px;text-align: center">
                            Manas Enerji Yönetimi Servis Birimi - Sayaç Servis Fiyatlandırma Ekranı
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4" style="font-size: 18px">Yeri:</label>
                        <label class="col-sm-10 col-xs-8 yer" style="padding-top: 9px;font-size: 18px">{{$ucretlendirilen->uretimyer->yeradi}}</label>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4" style="font-size: 18px">Netsis Cari Adı:</label>
                        <label class="col-sm-10 col-xs-8 cariadi" style="padding-top: 9px;font-size: 18px">{{$ucretlendirilen->netsiscari->cariadi}}</label>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4" style="font-size: 18px">Toplam Tutar:</label>
                        <label class="col-sm-10 col-xs-8 cariadi" style="padding-top: 9px;font-size: 18px">
                            {{$ucretlendirilen->fiyat2>0 ? $ucretlendirilen->fiyat.' '.$ucretlendirilen->parabirimi->adi.' + '.$ucretlendirilen->fiyat2.' '.$ucretlendirilen->parabirimi2->adi2 : $ucretlendirilen->fiyat.' '.$ucretlendirilen->parabirimi->adi}}
                        </label>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Seri No</th>
                                <th>Sayac Adı</th>
                                <th>Garanti</th>
                                <th>Tutar</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-10 col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                        Size gönderilen Onay Formu'nu imzalayıp buradan yüklediğinizde tarafımıza onayladığınıza dair bilgilendirme gelecektir.
                        Bilgilendirmeyi aldığımızda sayacınıza ait ücretin yatırıldığı kontrol edilip sayacınız paketlenecektir ve size gönderilecektir.
                        </div>
                        <input type="text" id="ucretlendirilenid" name="ucretlendirilenid" value="{{ $ucretlendirilenid }}" class="form-control hide"/>
                    </div>
                    <h4 class="form-section" style="padding-left:20px">İmzalanan formu buradan yükleyebilirsiniz</h4>
                    <div class="form-group">
                        <div class="col-xs-9" style="padding-left: 40px"><span class="required" aria-required="true" style="color: red"> * </span>
                            <div id="file" class="fileinput fileinput-new" data-provides="fileinput">
                                <span class="btn green btn-file">
                                    <span class="fileinput-new"> Dosya Seç</span>
                                    <span class="fileinput-exists"> Değiştir </span>
                                    <input type="file" id="eklenendosya" name="eklenendosya" accept="image/jpeg,image/gif,image/png,application/pdf">
                                </span>
                                <span class="fileinput-filename"> </span> &nbsp;
                                <a href="" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                            </div>
                        </div>
                    </div>
                    <h4 class="form-section" style="padding-left:20px;color: red">!!İkinci bir onaylama seçeneği olarak online ödeme sistemi yakında sisteme eklenecektir!!</h4>
                    <div class="form-group">{{ Form::token() }}</div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center">
                            <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Fiyatlandırma Onayla</button>
                            <a href='#portlet_onaylama' data-toggle='modal' data-id='{{$ucretlendirilenid}}' class='btn red onaylama'>Onaylama</a>
                        </div>
                    </div>
                </div>
            </form>
            @endif
            <!-- END FORM-->
        </div>
        <!-- END VALIDATION STATES-->
    </div>
@stop

@section('modal')
    <div class="modal fade" id="portlet_onaylama" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyatlandırma Onaylanmayacak</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ URL::to('musterireddet') }}" id="form_sample2" method="POST" class="form-horizontal" novalidate="novalidate">
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
                                <div class="col-xs-10 col-xs-offset-1" style="font-size: 18px;text-indent: 30px">
                                    Fiyatlandırmayı neden onaylamıyorsunuz?
                                </div>
                                <input type="text" id="ucretlendirilenid" name="ucretlendirilenid" value="{{ $ucretlendirilenid }}" class="form-control hide"/>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Bir Neden Belirtiniz<span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-xs-8">
                                    <i class="fa"></i><textarea name="aciklama" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">{{ Form::token() }}</div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <button type="submit" class="btn red">Fiyatlandırma Onaylama</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Fiyatlandırması Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Sayaçlara ait tamir bedelleri onaylanacaktır?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
