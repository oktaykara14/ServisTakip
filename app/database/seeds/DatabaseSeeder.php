<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('SistemSabitleriSeeder');
        $this->call('YetkiSeeder');
        $this->call('GrupSeeder');
		$this->call('ServisSeeder');
        $this->call('IllerSeeder');
		$this->call('SayacMarkaSeeder');
		$this->call('SayacTurSeeder');
		$this->call('SayacTipSeeder');
		$this->call('SayacDurumSeeder');
		$this->call('SayacCapSeeder');
		$this->call('HatirlatmaTipSeeder');
		$this->call('BildirimTipSeeder');
		$this->call('ServisDepolarSeeder');
		$this->call('ParaBirimiSeeder');
		$this->call('NetsisDepolarSeeder');
		$this->call('EdIlgiSeeder');
		$this->call('EdEntegrasyonFirmaSeeder');
		$this->call('EdEntegrasyonProgramSeeder');
		$this->call('EdEntegrasyonTipSeeder');
		$this->call('EdEntegrasyonVersiyonSeeder');
		$this->call('EdBaskiTurSeeder');
		$this->call('EdUrunSeeder');
		$this->call('EdTamirUrunSeeder');
		$this->call('EdTamirIslemSeeder');
		$this->call('EdKurulumTurSeeder');
		$this->call('EdKayitKonuSeeder');
		$this->call('EdDatabaseSeeder');
        $this->call('IstasyonSeeder');
        $this->call('KalibrasyonTipiSeeder');
        $this->call('KalibrasyonStandartSeeder');
	}

}
