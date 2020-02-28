<?php

class EdBaskiTurSeeder extends Seeder {

	public function run()
	{
            EdestekBaskiTur::create([
                'adi'=>'Su'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Kalorimetre'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Manas'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Trifaze Elektrik'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Monofaze Elektrik'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Baskısız'
            ]);
            EdestekBaskiTur::create([
                'adi'=>'Klimatik'
            ]);
        EdestekBaskiTur::create([
            'adi'=>'Gaz'
        ]);
	}

}
