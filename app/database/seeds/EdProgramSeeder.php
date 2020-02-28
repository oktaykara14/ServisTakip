<?php

class EdProgramSeeder extends Seeder {

	public function run()
	{
            EdestekUrun::create([
                'adi'=>'Entegre (Epic)'
            ]);
            EdestekUrun::create([
                'adi'=>'EpicSmart'
            ]);
            EdestekUrun::create([
                'adi'=>'4com'
            ]);
            EdestekUrun::create([
                'adi'=>'Entegrasyon'
            ]);
            
	}

}
