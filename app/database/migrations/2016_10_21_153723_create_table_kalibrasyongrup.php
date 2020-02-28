<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKalibrasyongrup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kalibrasyongrup', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('depogelen_id')->unsigned();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('adet')->default(0);
            $table->integer('biten')->default(0);
            $table->timestamp('kayittarihi');
            $table->integer('kalibrasyondurum')->default(0);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('kalibrasyongrup');
	}

}
