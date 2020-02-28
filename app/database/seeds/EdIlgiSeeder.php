<?php

class EdIlgiSeeder extends Seeder {

	public function run()
	{
            EdestekIlgi::create([
                'adi'=>'Epic'
            ]);
            EdestekIlgi::create([
                'adi'=>'EpicSmart'
            ]);
            EdestekIlgi::create([
                'adi'=>'4com'
            ]);
            EdestekIlgi::create([
                'adi'=>'Gprs Sayfası'
            ]);
            EdestekIlgi::create([
                'adi'=>'El Terminali'
            ]);
            EdestekIlgi::create([
                'adi'=>'Kiosk'
            ]);
            EdestekIlgi::create([
                'adi'=>'Kart Baskı'
            ]);
            EdestekIlgi::create([
                'adi'=>'Donanım Test'
            ]);
            EdestekIlgi::create([
                'adi'=>'Oracle Destek'
            ]);
            EdestekIlgi::create([
                'adi'=>'MySQL Destek'
            ]);
            EdestekIlgi::create([
                'adi'=>'MSSQL Destek'
            ]);
            EdestekIlgi::create([
                'adi'=>'Müşteri Destek'
            ]);
            EdestekIlgi::create([
                'adi'=>'Rapor Tasarımı'
            ]);
	}

}
