<?php

class KalibrasyonStandartSeeder extends Seeder {

	public function run()
	{
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>1,
            'hassasiyet'=>'Yok',
            'noktasayisi'=>3,
            'nokta1'=>'Qmax',
            'sapma1'=>1.50,
            'nokta2'=>'0,20 Qmax',
            'sapma2'=>1.50,
            'nokta3'=>'Qmin',
            'sapma3'=>3.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>2,
            'hassasiyet'=>'1/10',
            'noktasayisi'=>5,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>2,
            'hassasiyet'=>'1/20',
            'noktasayisi'=>6,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>2,
            'hassasiyet'=>'1/30',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>2,
            'hassasiyet'=>'1/50 ve üstü',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,15 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>3,
            'hassasiyet'=>'1/10',
            'noktasayisi'=>5,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>3,
            'hassasiyet'=>'1/20',
            'noktasayisi'=>6,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>3,
            'hassasiyet'=>'1/30',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>3,
            'hassasiyet'=>'1/50 ve üstü',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,15 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>4,
            'hassasiyet'=>'1/10',
            'noktasayisi'=>5,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>4,
            'hassasiyet'=>'1/20',
            'noktasayisi'=>6,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>2.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>4,
            'hassasiyet'=>'1/30',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,10 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);
        KalibrasyonStandart::create([
            'kalibrasyontipi_id'=>4,
            'hassasiyet'=>'1/50 ve üstü',
            'noktasayisi'=>7,
            'nokta1'=>'Qmax',
            'sapma1'=>1.00,
            'nokta2'=>'0,70 Qmax',
            'sapma2'=>1.00,
            'nokta3'=>'0,40 Qmax',
            'sapma3'=>1.00,
            'nokta4'=>'0,25 Qmax',
            'sapma4'=>1.00,
            'nokta5'=>'0,15 Qmax',
            'sapma5'=>1.00,
            'nokta6'=>'0,05 Qmax',
            'sapma6'=>2.00,
            'nokta7'=>'Qmin',
            'sapma7'=>2.00
        ]);

	}

}
