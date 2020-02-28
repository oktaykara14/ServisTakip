<?php

class EdDatabaseSeeder extends Seeder {

	public function run()
	{
            EdestekDatabase::create([
                'adi'=>'Oracle'
            ]);
            EdestekDatabase::create([
                'adi'=>'Microsoft SQL Server'
            ]);
            EdestekDatabase::create([
                'adi'=>'MySQL'
            ]);
	}

}
