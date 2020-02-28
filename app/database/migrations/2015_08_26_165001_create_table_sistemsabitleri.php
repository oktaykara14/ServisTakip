<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSistemsabitleri extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sistemsabitleri', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('adi');
            $table->string('deger');
            $table->string('ilkdeger');
            $table->string('aciklama')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sistemsabitleri');
	}

}
