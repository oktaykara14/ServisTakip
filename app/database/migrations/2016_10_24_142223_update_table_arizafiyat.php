<?php

use Illuminate\Database\Migrations\Migration;

class UpdateTableArizafiyat extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE arizafiyat ADD subedurum INTEGER NOT NULL CONSTRAINT DF_arizafiyat_subedurum DEFAULT(0)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE arizafiyat DROP CONSTRAINT DF_arizafiyat_subedurum, COLUMN subedurum');
	}

}
