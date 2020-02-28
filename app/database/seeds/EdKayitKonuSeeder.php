<?php

class EdKayitKonuSeeder extends Seeder {

	public function run()
	{
            EdestekKayitKonu::create([
                'adi'=>'Müşteri Destek'
            ]);
            EdestekKayitKonu::create([
                'adi'=>'Kurulum'
            ]);
            EdestekKayitKonu::create([
                'adi'=>'Tamir Bakım'
            ]);
            EdestekKayitKonu::create([
                'adi'=>'Kart Baskısı'
            ]);
            EdestekKayitKonu::create([
                'adi'=>'Düzenli İşlem'
            ]);
	}

}
