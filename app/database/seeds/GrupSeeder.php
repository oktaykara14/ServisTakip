<?php

class GrupSeeder extends Seeder {

	public function run()
	{
        Grup::create([
            'adi'=>'Admin',
            'yetkiseviye'=>1
        ]);
        Grup::create([
            'adi'=>'Müdür',
            'yetkiseviye'=>2
        ]);
        Grup::create([
            'adi'=>'Birim Müdürü',
            'yetkiseviye'=>3
        ]);
        Grup::create([
            'adi'=>'Birim Amiri',
            'yetkiseviye'=>4
        ]);
        Grup::create([
            'adi'=>'Su Isı Servis Yetkilisi',
            'yetkiseviye'=>5
        ]);
        Grup::create([
            'adi'=>'Elektrik Servis Yetkilisi',
            'yetkiseviye'=>6
        ]);
        Grup::create([
            'adi'=>'Gaz Servis Yetkilisi',
            'yetkiseviye'=>7
        ]);
        Grup::create([
            'adi'=>'İstanbul Servis Yetkilisi',
            'yetkiseviye'=>8
        ]);
        Grup::create([
            'adi'=>'Bolu Servis Yetkilisi',
            'yetkiseviye'=>9
        ]);
        Grup::create([
            'adi'=>'Eğitim Destek Elemanı',
            'yetkiseviye'=>10
        ]);
        Grup::create([
            'adi'=>'Su Isı Servis Elemanı',
            'yetkiseviye'=>11
        ]);
        Grup::create([
            'adi'=>'Elektrik Servis Elemanı',
            'yetkiseviye'=>12
        ]);
        Grup::create([
            'adi'=>'Gaz Servis Elemanı',
            'yetkiseviye'=>13
        ]);
        Grup::create([
            'adi'=>'İstanbul Servis Elemanı',
            'yetkiseviye'=>14
        ]);
        Grup::create([
            'adi'=>'Bolu Servis Elemanı',
            'yetkiseviye'=>15
        ]);
        Grup::create([
            'adi'=>'Depo Kullanıcısı',
            'yetkiseviye'=>16
        ]);
        Grup::create([
            'adi'=>'Şube Yetkilisi',
            'yetkiseviye'=>17
        ]);
        Grup::create([
            'adi'=>'Şube Elemanı',
            'yetkiseviye'=>18
        ]);
        Grup::create([
            'adi'=>'Kullanıcı',
            'yetkiseviye'=>19
        ]);
	}

}
