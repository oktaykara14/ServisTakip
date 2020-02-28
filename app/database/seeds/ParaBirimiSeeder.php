<?php

class ParaBirimiSeeder extends Seeder {

	public function run()
	{
            ParaBirimi::create([
                'adi'=>'Türk Lirası',
                'birimi'=>'₺',
                'oncelik'=>1
            ]);
            ParaBirimi::create([
                'adi'=>'Euro',
                'birimi'=>'€',
                'oncelik'=>0
            ]);
            ParaBirimi::create([
                'adi'=>'Dolar',
                'birimi'=>'$',
                'oncelik'=>0
            ]);
            ParaBirimi::create([
                'adi'=>'Sterlin',
                'birimi'=>'£',
                'oncelik'=>0
            ]);
	}

}
