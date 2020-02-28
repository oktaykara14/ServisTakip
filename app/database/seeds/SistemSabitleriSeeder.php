<?php

class SistemSabitleriSeeder extends Seeder {

	public function run()
	{
        SistemSabitleri::create([
            'adi'=>'ServerIc',
            'deger'=>'192.168.100.13',
            'ilkdeger'=>'192.168.100.13',
            'aciklama'=>'Server Ip Adresi (Local)'
        ]);
        SistemSabitleri::create([
            'adi'=>'ServerDis',
            'deger'=>'195.142.123.154:801',
            'ilkdeger'=>'195.142.123.154:801',
            'aciklama'=>'Server Ip Adresi (Dış Bağlantı)'
        ]);
        SistemSabitleri::create([
            'adi'=>'SistemSonTarihi',
            'deger'=>'2099-12-31',
            'ilkdeger'=>'2099-12-31',
            'aciklama'=>'Sistemin Sonlanacağı Tarih'
        ]);
        SistemSabitleri::create([
            'adi'=>'BakimDurum',
            'deger'=>'0',
            'ilkdeger'=>'0',
            'aciklama'=>'Sistemin Bakım Durumunu Tutar'
        ]);
        SistemSabitleri::create([
            'adi'=>'StokDurum',
            'deger'=>'1',
            'ilkdeger'=>'0',
            'aciklama'=>'Stok Takibi Yapılacak mı?'
        ]);
        SistemSabitleri::create([
            'adi'=>'KdvOrani',
            'deger'=>'18',
            'ilkdeger'=>'18',
            'aciklama'=>'Sistemdeki KDV Oranı'
        ]);
        SistemSabitleri::create([
            'adi'=>'IndirimOrani',
            'deger'=>'20',
            'ilkdeger'=>'20',
            'aciklama'=>'Maksimum Uygulanabilecek İndirim Oranı'
        ]);
        SistemSabitleri::create([
            'adi'=>'NetsisFatura',
            'deger'=>'1',
            'ilkdeger'=>'1',
            'aciklama'=>'Depo teslim sırasında Netsis Üzerinden Fatura Kesimi'
        ]);
        SistemSabitleri::create([
            'adi'=>'BakimSonTarih',
            'deger'=>'2016-04-22 18:00:00',
            'ilkdeger'=>'2000-01-01 00:00:00.000',
            'aciklama'=>'Bakımın Sona Ereceği Tarih'
        ]);
	}

}
