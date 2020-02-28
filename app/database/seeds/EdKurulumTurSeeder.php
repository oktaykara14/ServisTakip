<?php

class EdKurulumTurSeeder extends Seeder {

	public function run()
	{
            EdestekKurulumTur::create([
                'adi'=>'Entegre(Epic) Kurulumu'
            ]);
            EdestekKurulumTur::create([
                'adi'=>'EpicSmart Kurulumu'
            ]);
            EdestekKurulumTur::create([
                'adi'=>'4com Kurulumu'
            ]);
            EdestekKurulumTur::create([
                'adi'=>'Kiosk Kurulumu'
            ]);
	}

}
