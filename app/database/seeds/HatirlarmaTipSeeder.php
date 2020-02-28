<?php

class HatirlatmaTipSeeder extends Seeder {

	public function run()
	{
        HatirlatmaTip::create([
            'tur'=>'Sayaç Kayıdı'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Arıza Kayıdı'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Fiyatlandırma'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Onay Formu Gönderilmesi'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Müşteri Onayı'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Tekrar Fiyatlandırma'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Kalibrasyon'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Depo Teslim'
        ]);
        HatirlatmaTip::create([
            'tur'=>'İşlem Tamamlandı'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Hurdaya Gönderildi'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Şube Gönderim'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Şube Fiyatlandırma'
        ]);
        HatirlatmaTip::create([
            'tur'=>'Abone Teslim'
        ]);
	}

}
