<?php

class SayacMarkaSeeder extends Seeder {

	public function run()
	{
        SayacMarka::create([
            'marka'=>'MANAS'
        ]);
        SayacMarka::create([
            'marka'=>'ITRON'
        ]);
        SayacMarka::create([
            'marka'=>'SCHLUMBERGER'
        ]);
        SayacMarka::create([
            'marka'=>'ACTARIS'
        ]);
        SayacMarka::create([
            'marka'=>'ECA'
        ]);
        SayacMarka::create([
            'marka'=>'ELSTER'
        ]);
        SayacMarka::create([
            'marka'=>'I-METER'
        ]);
        SayacMarka::create([
            'marka'=>'KALEKALIP'
        ]);
        SayacMarka::create([
            'marka'=>'RMG'
        ]);
        SayacMarka::create([
            'marka'=>'DRESSER'
        ]);

	}

}
