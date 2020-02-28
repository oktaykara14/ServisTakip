<?php

class IstasyonSeeder extends Seeder {

	public function run()
	{
        Istasyon::create([
            'istasyonadi'=>'MGTB VT-1',
            'sayacsayi'=>12,
            'sayactipleri'=>'10,14,18,22,23'
        ]);
        Istasyon::create([
            'istasyonadi'=>'MGTB VT-2',
            'sayacsayi'=>12,
            'sayactipleri'=>'10,14,18,22,23'
        ]);
        Istasyon::create([
            'istasyonadi'=>'BELL PROVER 200L',
            'sayacsayi'=>8,
            'sayactipleri'=>'10,14,18,22,23'
        ]);
        Istasyon::create([
            'istasyonadi'=>'BELL 650 L',
            'sayacsayi'=>12,
            'sayactipleri'=>'10,14,18,22,23'
        ]);
        Istasyon::create([
            'istasyonadi'=>'BELL PROVER 2000L',
            'sayacsayi'=>2,
            'sayactipleri'=>'11,12,13,15,16,17,19,20,21,24,25,26,27,28,29,30,31,32'
        ]);
        Istasyon::create([
            'istasyonadi'=>'UT G-1600',
            'sayacsayi'=>1,
            'sayactipleri'=>'11,12,13,15,16,17,19,20,21,24,25,26,27,28,29,30,31,32'
        ]);
	}

}
