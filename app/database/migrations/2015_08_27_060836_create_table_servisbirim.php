<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServisbirim extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servisbirim', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('adi');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->integer('netsiscari_id')->unsigned()->nullable();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servisbirim');
	}

}
