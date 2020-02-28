<?php

class NetsisDepolarSeeder extends Seeder {

	public function run()
	{
        NetsisDepolar::create([
            'kodu'=>1,
            'adi'=>'ANKARA DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>2,
            'adi'=>'ANK DİZGİ DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>3,
            'adi'=>'ANK ÜRETİM DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>4,
            'adi'=>'AQUADIS DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>5,
            'adi'=>'ANK NUMUNE MLZ. DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>6,
            'adi'=>'ANK SUSERVİS DEPO',
            'netsiscari_id'=>2631
        ]);
        NetsisDepolar::create([
            'kodu'=>7,
            'adi'=>'FASON DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>8,
            'adi'=>'SMD DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>9,
            'adi'=>'ARGE DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>10,
            'adi'=>'REZERVE DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>11,
            'adi'=>'İSTANBUL DEPO',
            'netsiscari_id'=>1169
        ]);
        NetsisDepolar::create([
            'kodu'=>12,
            'adi'=>'BOLU DEPO',
            'netsiscari_id'=>1134
        ]);
        NetsisDepolar::create([
            'kodu'=>13,
            'adi'=>'GEREDE DEPO',
            'netsiscari_id'=>1135
        ]);
        NetsisDepolar::create([
            'kodu'=>14,
            'adi'=>'KÜÇÜKKÖY DEPO',
            'netsiscari_id'=>1136
        ]);
        NetsisDepolar::create([
            'kodu'=>15,
            'adi'=>'KARAÇULHA DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>16,
            'adi'=>'ESKİŞEHİR DEPO',
            'netsiscari_id'=>1161
        ]);
        NetsisDepolar::create([
            'kodu'=>17,
            'adi'=>'BİLECİK DEPO',
            'netsiscari_id'=>1206
        ]);
        NetsisDepolar::create([
            'kodu'=>18,
            'adi'=>'KOCAELİ DEPO',
            'netsiscari_id'=>1191
        ]);
        NetsisDepolar::create([
            'kodu'=>19,
            'adi'=>'BAŞKENTGAZ DEPO',
            'netsiscari_id'=>1213
        ]);
        NetsisDepolar::create([
            'kodu'=>20,
            'adi'=>'ANK ELEKTRIK DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>21,
            'adi'=>'ANK DİZGİ ARIZA DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>22,
            'adi'=>'ÇERKEŞ DEPO',
            'netsiscari_id'=>1235
        ]);
        NetsisDepolar::create([
            'kodu'=>23,
            'adi'=>'AKÇAKOCA DEPO',
            'netsiscari_id'=>1147
        ]);
        NetsisDepolar::create([
            'kodu'=>24,
            'adi'=>'ANK GAZSERVİS DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>25,
            'adi'=>'ANK ELEKTSERVİS DEPO'
        ]);
        NetsisDepolar::create([
            'kodu'=>30,
            'adi'=>'HİZMET DEPO'
        ]);
            
	}

}
