<?php

class SayacTurSeeder extends Seeder {

	public function run()
	{
        SayacTur::create([
            'tur'=>'Su'
        ]);
        SayacTur::create([
            'tur'=>'Elektrik'
        ]);
        SayacTur::create([
            'tur'=>'Gaz'
        ]);
        SayacTur::create([
            'tur'=>'Isı'
        ]);
        SayacTur::create([
            'tur'=>'Gaz Mekanik'
        ]);
            
	}

}
