<?php

class EdTamirUrunSeeder extends Seeder {

	public function run()
	{
            EdestekTamirUrun::create([
                'adi'=>'Kart Okuyucu'
            ]);
            EdestekTamirUrun::create([
                'adi'=>'El Terminali'
            ]);
            EdestekTamirUrun::create([
                'adi'=>'Kiosk'
            ]);
            EdestekTamirUrun::create([
                'adi'=>'Smart Kart'
            ]);
	}

}
