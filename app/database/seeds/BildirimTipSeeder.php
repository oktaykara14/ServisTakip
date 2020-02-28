<?php

class BildirimTipSeeder extends Seeder {

	public function run()
	{
        BildirimTip::create([
            'tur'=>'Depo Sayaç Girişi Yapıldı'
        ]);
        BildirimTip::create([
            'tur'=>'Sisteme Sayaç Kayıdı Yapıldı'
        ]);
        BildirimTip::create([
            'tur'=>'Sayaçların Arıza Kayıdı Yapıldı'
        ]);
        BildirimTip::create([
            'tur'=>'Sayaçların Tamir Bakımı Onaylandı'
        ]);
        BildirimTip::create([
            'tur'=>'Müşteriye Onay Formu Gönderildi'
        ]);
        BildirimTip::create([
            'tur'=>'Fiyatlandırma Reddedildi'
        ]);
        BildirimTip::create([
            'tur'=>'Tamir Bakımın Müşteri Onayı Alındı'
        ]);
        BildirimTip::create([
            'tur'=>'Kalibrasyon Yapıldı'
        ]);
        BildirimTip::create([
            'tur'=>'Sayaçlar Depoya Teslim Edildi'
        ]);
        BildirimTip::create([
            'tur'=>'Hurdaya Gönderildi'
        ]);
	}

}
