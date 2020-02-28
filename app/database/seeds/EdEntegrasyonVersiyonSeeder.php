<?php

class EdEntegrasyonVersiyonSeeder extends Seeder {

	public function run()
	{
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>''
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'pCard.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'MagLib.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'NfcLib.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'EksV1.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'EksV2.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'EksV3.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'EksV4.dll'
            ]);
            EdestekEntegrasyonVersiyon::create([
                'versiyon'=>'EksV5.dll'
            ]);
	}

}
