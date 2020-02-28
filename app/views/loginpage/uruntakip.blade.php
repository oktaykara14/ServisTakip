<!DOCTYPE html>
<html lang="tr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="">
	<meta name="description" content="Manas Servis Sayaç Takip Sistemi">
	<link rel="shortcut icon" href="{{ URL::to('favicon.ico') }}" />
	<link rel="apple-touch-icon" sizes="76x76" href="{{ URL::to('favicon.ico') }}" />
	<title>Manas Online Sayaç Servis Takip Sistemi</title>

	<!--     Fonts and icons     -->
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	<link href="{{ URL::to('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />

	<link href="{{ URL::to('assets/loginpage/css/loginstyle.css') }}" rel="stylesheet" />

	<!-- CSS Files -->
	<link href="{{ URL::to('assets/loginpage/assets/css/bootstrap.min.css') }}" rel="stylesheet" />
	<link href="{{ URL::to('assets/loginpage/assets/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link href="assets/css/demo.css" rel="stylesheet" />
</head>

<body>
	<div class="image-container set-full-height" style="background-image: url('{{ URL::to('assets/loginpage/images/bg2.jpg') }}')">

		<!--   Big container   -->
		<div class="container">
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<!--      Wizard container        -->
					<div class="wizard-container">
						<div class="card wizard-card" data-color="sari" id="wizard">
							<form action="" >
								<div class="wizard-header">
									<a href="{{ URL::to('login') }}">
										<img src="{{ URL::to('assets/images/logo/manas_logo.png') }}" width="150" alt="">
									</a>
									<h5>Ana sayfaya dönmek için logoya tıklayınız.</h5>
								</div>
								<div class="wizard-navigation">
									<ul>
										<li><a href="#adim1" data-toggle="tab">FABRİKA GİRİŞ</a></li>
										<li><a href="#adim2" data-toggle="tab">ÜRÜN KONTROLÜ</a></li>
										<li><a href="#adim3" data-toggle="tab">ARIZA TESPİT</a></li>
										<li><a href="#adim4" data-toggle="tab">ONAY SÜRECİ</a></li>
										<li><a href="#adim5" data-toggle="tab">TESLİM DURUMU</a></li>

									</ul>
								</div>

								<div class="tab-content">
									<div class="tab-pane" id="adim1">
										<div class="row">
											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text"> Gelen Sayaç Bilgisi</h4>
												</div>
											</div>

											<div class="col-sm-5 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Kayıt Yapan:</label>
													<label class="form-control">{{$kullanici->adi_soyadi}}</label>
												</div>
											</div>
											<div class="col-sm-5">
												<div class="form-group label-floating">
													<label class="control-label">Kayıt Tarihi:</label>
													<label class="form-control">{{$sayacgelen->gdurumtarihi}}</label>
												</div>
											</div>

											<div class="col-sm-5 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Belge No:</label>
													<label class="form-control">{{$depogelen->fisno}}</label>
												</div>
											</div>
											<div class="col-sm-5">
												<div class="form-group label-floating">
													<label class="control-label">Depo Geliş Tarihi:</label>
													<label class="form-control">{{$depogelen->gtarih}}</label>
												</div>
											</div>

											<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Stok Kodu:</label>
													<label class="form-control">{{$netsisstokkod->kodu.' - '.$netsisstokkod->adi}}</label>
												</div>
											</div>

											<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Cari Kodu:</label>
													<label class="form-control">{{$netsiscari->carikod.' - '.$netsiscari->cariadi}}</label>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="adim2">
										<div class="row">
											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text">Sayaç Üretim Bilgisi</h4>
												</div>
											</div>

											<div class="col-sm-5 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Üretim Yeri:</label>
													<label class="form-control">{{$uretimyer->yeradi}}</label>
												</div>
											</div>

											<div class="col-sm-5">
												<div class="form-group label-floating">
													<label class="control-label">Üretim Tarihi:</label>
													<label class="form-control">{{$sayac ? $sayac->guretimtarihi : ''}}</label>
												</div>
											</div>

											<div class="col-sm-5 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Seri No:</label>
													<label class="form-control">{{$servistakip->serino}}</label>
												</div>
											</div>

											<div class="col-sm-5">
												<div class="form-group label-floating">
													<label class="control-label">Sayaç Adı:</label>
													<label class="form-control">{{$sayacadi->sayacadi}}</label>
												</div>
											</div>

											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text">Garanti Durumu</h4>
												</div>
											</div>

											<div class="col-sm-5 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Kontrol Tarihi:</label>
													<label class="form-control">{{$arizakayit ? $arizakayit->garizakayittarihi : ''}}</label>
												</div>
											</div>

											<div class="col-sm-5 ">
												<div class="form-group label-floating">
													<label class="control-label">Garanti Durumu:</label>
													<label class="form-control">{{$arizafiyat ? ($arizafiyat->ariza_garanti ? 'İÇİNDE' : 'DIŞINDA') : '' }}</label>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="adim3">
										<div class="row">
											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text">Kontrol Bilgileri</h4>
												</div>
											</div>
											@if($arizakayit)
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Belirlenen Arızalar:</label>
														<label class="form-control-ozel">
															@foreach($arizalar as $ariza)
															{{$ariza->tanim}}<br>
															@endforeach
														</label>
													</div>
												</div>
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Yapılan / Yapılacak İşlemler:</label>
														<label class="form-control-ozel">
															@foreach($yapilanlar as $yapilan)
																{{$yapilan->tanim}}<br>
															@endforeach
														</label>
													</div>
												</div>
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Değişen / Değişecek Parçalar - Alınacak Bedeller:</label>
														<label class="form-control-ozel">
															@foreach($degisenler as $degisen)
																{{$degisen->tanim}}{{$degisen->ucretsiz==1 ? '- ÜCRETSİZ' : ''}}<br>
															@endforeach
														</label>
													</div>
												</div>
												@if($arizafiyat && ($arizafiyat->durum==1 || $arizafiyat->durum==3))
													<div class="col-sm-5 col-sm-offset-1">
														<div class="form-group label-floating">
															<label class="control-label">Toplam Tutar:</label>
															<label class="form-control">{{$toplamtutar}}</label>
														</div>
													</div>
												@else
													<div class="col-sm-5 col-sm-offset-1">
														<div class="form-group label-floating">
															<label class="control-label">Fiyatlandırma Henüz Yapılmamış!</label>
														</div>
													</div>
												@endif
											@else
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Arıza Kayıdı Henüz Tamamlanmamış!</label>
													</div>
												</div>
											@endif
										</div>
									</div>
									<div class="tab-pane" id="adim4">
										<div class="row">
											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text">Müşteri Onay Süreci</h4>
												</div>
											</div>
											@if($ucretlendirme)
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Cari Bilgisi:</label>
														<label class="form-control">{{$netsiscari->cariadi}}</label>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Durum:</label>
														<label class="form-control">{{$ucretlendirme->durum==0 ? 'Fiyatlandırma Bekleniyor' : $ucretlendirme->gabonedurum}}</label>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Onaylama Tarihi:</label>
														<label class="form-control">{{$onaylanan ? $onaylanan->gonaytarihi : ''}}</label>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Onaylayan:</label>
														<label class="form-control">{{$onaylanan && $onaylanan->yetkili ? $onaylanan->yetkili->kullanici->adi_soyadi : ''}}</label>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Onaylama Tipi:</label>
														<label class="form-control">{{$onaylanan ? $onaylanan->gonaylamatipi : ''}}</label>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Onay Formu:</label>
														<label class="form-control">{{$onaylanan && $onaylanan->onayformu ? 'VAR' : 'YOK'}}</label>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="baslik">
														<h4 class="info-text">Bildirim Seçenekleri</h4>
													</div>
												</div>
												<div class="col-sm-5 col-sm-offset-1 ">
													<div class="form-group label-floating">
														<label class="control-label">Mail Durumu:</label>
														<label class="form-control">{{$ucretlendirme->gmail}}</label>
													</div>
												</div>
											@else
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Ücretlendirme Henüz Yapılmamış!</label>
													</div>
												</div>
											@endif
										</div>
									</div>

									<div class="tab-pane" id="adim5">
										<div class="row">
											<div class="col-sm-12">
												<div class="baslik">
													<h4 class="info-text">Teslimat Bilgileri</h4>
												</div>
											</div>
											@if($depoteslim)
											<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Cari Bilgisi:</label>
													<label class="form-control-ozel">{{$netsiscari->cariadi}}</label>
												</div>
											</div>
											<div class="col-sm-5 col-sm-offset-1 ">
												<div class="form-group label-floating">
													<label class="control-label">Durum:</label>
													<label class="form-control">{{$depoteslim->gdepodurum}}</label>
												</div>
											</div>
											<div class="col-sm-5 ">
												<div class="form-group label-floating">
													<label class="control-label">Teslim Tarihi:</label>
													<label class="form-control">{{$depoteslim->gteslimtarihi}}</label>
												</div>
											</div>
											<div class="col-sm-5 col-sm-offset-1 ">
												<div class="form-group label-floating">
													<label class="control-label">Fatura No:</label>
													<label class="form-control">{{$depoteslim->faturano ? $depoteslim->faturano : 'Elle Çıkış Yapılmıştır!'}}</label>
												</div>
											</div>
											<div class="col-sm-5 ">
												<div class="form-group label-floating">
													<label class="control-label">Gönderim Türü:</label>
													<label class="form-control">{{$depoteslim->gtipi}}</label>
												</div>
											</div>
												<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Fatura Adresi:</label>
													<label class="form-control">{{$depoteslim->faturaadres}}</label>
												</div>
											</div>
											<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Teslim Adresi:</label>
													<label class="form-control">{{$depoteslim->teslimadres=="" ? $depoteslim->faturaadres : $depoteslim->teslimadres}}</label>
												</div>
											</div>
											<div class="col-sm-10 col-sm-offset-1">
												<div class="form-group label-floating">
													<label class="control-label">Sayaçlar:</label>
													<label class="form-control-ozel">{{$servistakip->serino.' - '.$sayacadi->sayacadi.' - '.$toplamtutar}}</label>
												</div>
											</div>
											@else
												<div class="col-sm-10 col-sm-offset-1">
													<div class="form-group label-floating">
														<label class="control-label">Teslimat Henüz Yapılmamış!</label>
													</div>
												</div>
											@endif
											<div class="col-sm-12">
													<div class="baslik">
														<h4 class="info-text">Açıklama</h4>
													</div>
												</div>
											<div class="col-sm-10 col-sm-offset-1">
												<p class="description">Ürünler, depoya teslim tarihinde veya bir sonraki gün kargoya verilir.
													Hafta sonuna denk gelen durumlarda, ilk iş günü gönderim yapılacaktır. </p>
											</div>
										</div>
									</div>
								</div>
								<div class="wizard-footer">
									<div class="pull-right">
										<input type='button' class='btn btn-next btn-fill btn-sari btn-wd'
											name='next' value='İLERİ' />
										<!-- <input type='button' class='btn btn-finish btn-fill btn-sari btn-wd'
											name='finish' value='SON' /> -->
									</div>
									<div class="pull-left">
										<input type='button' class='btn btn-previous btn-fill btn-default btn-wd'
											name='previous' value='GERİ' />
									</div>
									<div class="clearfix"></div>
								</div>
							</form>
						</div>
					</div> <!-- wizard container -->
				</div>
			</div> <!-- row -->
		</div> <!--  big container -->


	</div>

</body>
<!--   Core JS Files   -->
<script src="{{ URL::to('assets/loginpage/assets/js/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/loginpage/assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/loginpage/assets/js/jquery.bootstrap.js') }}" type="text/javascript"></script>

<!--  Plugin for the Wizard -->
<script src="{{ URL::to('assets/loginpage/assets/js/material-bootstrap-wizard.js') }}" type="text/javascript"></script>

<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>

</html>