<?php

class EdEntegrasyonTipSeeder extends Seeder {

	public function run()
	{
            EdestekEntegrasyonTip::create([
                'tipi'=>'DLL'
            ]);
            EdestekEntegrasyonTip::create([
                'tipi'=>'WebServis'
            ]);
            EdestekEntegrasyonTip::create([
                'tipi'=>'SAP'
            ]);
            EdestekEntegrasyonTip::create([
                'tipi'=>'Veritabanı'
            ]);
	}

}
