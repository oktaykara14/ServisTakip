<?php

class ServisSeeder extends Seeder {

	public function run()
	{
        Servis::create([
            'servisadi'=>'SU',
            'netsisadi'=>'SU'
        ]);
        Servis::create([
            'servisadi'=>'ELEKTRIK',
            'netsisadi'=>'ELEKTRNK'
        ]);
        Servis::create([
            'servisadi'=>'GAZ',
            'netsisadi'=>'DOGALGAZ'
        ]);
        Servis::create([
            'servisadi'=>'ISI',
            'netsisadi'=>'ISI'
        ]);
        Servis::create([
            'servisadi'=>'GAZ MEKANIK',
            'netsisadi'=>'DOGALGAZ'
        ]);
        Servis::create([
            'servisadi'=>'SUBE',
            'netsisadi'=>'SUBE'
        ]);
	}

}
