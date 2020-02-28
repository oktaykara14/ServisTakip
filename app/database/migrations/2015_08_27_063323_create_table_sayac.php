<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayac extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayac', function(Blueprint $table)
		{
			$table->increments('id');
            $table->bigInteger('serino');
            $table->bigInteger('cihazno');
            $table->integer('sayactur_id')->unsigned()->nullable();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayacadi_id')->unsigned()->nullable();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayaccap_id')->unsigned()->nullable();
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->timestamp('uretimtarihi')->nullable();
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->timestamp('songelistarihi')->nullable();
            $table->integer('kullanici_id')->unsigned()->nullable();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayac');
	}

}
