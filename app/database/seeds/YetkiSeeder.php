<?php

class YetkiSeeder extends Seeder {

	public function run()
	{
        Yetki::create([
            'adi'=>'Seviye 1',
            'seviye'=>1,
            'aciklama'=>'Admin Yetkisine Sahiptir'
        ]);
        Yetki::create([
            'adi'=>'Seviye 2',
            'seviye'=>2,
            'aciklama'=>'Müdür Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 3',
            'seviye'=>3,
            'aciklama'=>'Birim Müdürü Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 4',
            'seviye'=>4,
            'aciklama'=>'Birim Amiri Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 5',
            'seviye'=>5,
            'aciklama'=>'Su Servis Yetkili Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 6',
            'seviye'=>6,
            'aciklama'=>'Elektrik Servis Yetkili Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 7',
            'seviye'=>7,
            'aciklama'=>'Gaz Servis Yetkili Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 8',
            'seviye'=>8,
            'aciklama'=>'İstanbul Servis Yetkili Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 9',
            'seviye'=>9,
            'aciklama'=>'Bolu Servis Yetkili Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 10',
            'seviye'=>10,
            'aciklama'=>'EDestek Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 11',
            'seviye'=>11,
            'aciklama'=>'Su Servis Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 12',
            'seviye'=>12,
            'aciklama'=>'Elektrik Servis Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 13',
            'seviye'=>13,
            'aciklama'=>'Gaz Servis Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 14',
            'seviye'=>14,
            'aciklama'=>'İstanbul Servis Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 15',
            'seviye'=>15,
            'aciklama'=>'Bolu Servis Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 16',
            'seviye'=>16,
            'aciklama'=>'Depo Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 17',
            'seviye'=>17,
            'aciklama'=>'Şube Yetkili Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 18',
            'seviye'=>18,
            'aciklama'=>'Şube Personel Yetki Seviyesi'
        ]);
        Yetki::create([
            'adi'=>'Seviye 19',
            'seviye'=>19,
            'aciklama'=>'Kullanıcı Yetki Seviyesi'
        ]);
	}

}
