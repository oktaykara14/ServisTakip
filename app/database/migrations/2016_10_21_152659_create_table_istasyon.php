<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIstasyon extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('istasyon', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('istasyonadi',100);
            $table->integer('sayacsayi');
            $table->string('sayactipleri',512);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('istasyon');
	}

}
