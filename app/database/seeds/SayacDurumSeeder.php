<?php

class SayacDurumSeeder extends Seeder {

	public function run()
	{
            SayacDurum::create([
                'durumadi'=>'Servis Sayaç Kayıdı Yapıldı',
                'ozel'=>false
            ]);
            SayacDurum::create([
                'durumadi'=>'Arıza Kayıdı Yapıldı',
                'ozel'=>false
            ]);
            SayacDurum::create([
                'durumadi'=>'Fiyatlandırma Yapıldı',
                'ozel'=>false
            ]);
            SayacDurum::create([
                'durumadi'=>'Onay Formu Gönderildi',
                'ozel'=>false
            ]);
            SayacDurum::create([
                'durumadi'=>'Müşteri Onayı Alındı',
                'ozel'=>false
            ]);
        SayacDurum::create([
            'durumadi'=>'Fiyatlandırma Reddedildi',
            'ozel'=>false
        ]);
        SayacDurum::create([
            'durumadi'=>'Tekrar Fiyatlandırıldı',
            'ozel'=>false
        ]);
        SayacDurum::create([
            'durumadi'=>'Kalibrasyon Yapıldı',
            'ozel'=>false
        ]);
            SayacDurum::create([
                'durumadi'=>'Depoya Teslim Edildi',
                'ozel'=>false
            ]);
            SayacDurum::create([
                'durumadi'=>'Geri Gönderildi',
                'ozel'=>false
            ]);
        SayacDurum::create([
            'durumadi'=>'Hurdaya Ayrıldı',
            'ozel'=>false
        ]);
            SayacDurum::create([
                'durumadi'=>'Parça Bekleniyor',
                'ozel'=>true
            ]);
            SayacDurum::create([
                'durumadi'=>'Parça Tedarik Ediliyor',
                'ozel'=>true
            ]);
	}

}
