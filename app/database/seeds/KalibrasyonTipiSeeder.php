<?php

class KalibrasyonTipiSeeder extends Seeder {

	public function run()
	{
        KalibrasyonTipi::create([
            'tipadi'=>'DİYAFRAM'
        ]);
        KalibrasyonTipi::create([
            'tipadi'=>'ROTARY'
        ]);
        KalibrasyonTipi::create([
            'tipadi'=>'TURBIN'
        ]);
        KalibrasyonTipi::create([
            'tipadi'=>'QUANTOMETRE'
        ]);
	}

}
