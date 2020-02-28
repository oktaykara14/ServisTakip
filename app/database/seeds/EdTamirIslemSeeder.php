<?php

class EdTamirIslemSeeder extends Seeder {

	public function run()
	{
            EdestekTamirIslem::create([
                'edestektamirurun_id'=>1,
                'adi'=>'Kart Okuyucu Tamiri'
            ]);
            EdestekTamirIslem::create([
                'edestektamirurun_id'=>2,
                'adi'=>'El Terminali Tamiri'
            ]);
            EdestekTamirIslem::create([
                'edestektamirurun_id'=>3,
                'adi'=>'Kiosk Tamiri'
            ]);
            EdestekTamirIslem::create([
                'edestektamirurun_id'=>4,
                'adi'=>'Smart Kart Tamiri'
            ]);
	}

}
