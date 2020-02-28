<?php

class EdUrunSeeder extends Seeder {

	public function run()
	{
            EdestekUrun::create([
                'adi'=>'Su Sayacı'
            ]);
            EdestekUrun::create([
                'adi'=>'Sıcak Su Sayacı'
            ]);
            EdestekUrun::create([
                'adi'=>'Elektrik Sayacı'
            ]);
            EdestekUrun::create([
                'adi'=>'Gaz Sayacı'
            ]);
            EdestekUrun::create([
                'adi'=>'Isı Sayacı'
            ]);
            EdestekUrun::create([
                'adi'=>'Pay Ölçer'
            ]);
            EdestekUrun::create([
                'adi'=>'El Terminali'
            ]);
            EdestekUrun::create([
                'adi'=>'Kiosk'
            ]);
            EdestekUrun::create([
                'adi'=>'Klima Kontrol Cihazı'
            ]);
            EdestekUrun::create([
                'adi'=>'Diğer'
            ]);
	}

}
