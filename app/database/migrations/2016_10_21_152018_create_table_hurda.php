<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHurda extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hurda', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('sayac_id')->unsigned();
            $table->foreign('sayac_id')->references('id')->on('sayac');
            $table->integer('hurdanedeni_id')->unsigned();
            $table->foreign('hurdanedeni_id')->references('id')->on('hurdanedeni');
            $table->timestamp('hurdatarihi');
            $table->integer('sayacgelen_id')->unsigned();
            $table->foreign('sayacgelen_id')->references('id')->on('sayacgelen');
            $table->integer('arizakayit_id')->unsigned()->nullable();
            $table->foreign('arizakayit_id')->references('id')->on('arizakayit');
            $table->integer('arizafiyat_id')->unsigned()->nullable();;
            $table->foreign('arizafiyat_id')->references('id')->on('arizafiyat');
            $table->integer('ucretlendirilen_id')->unsigned()->nullable();;
            $table->foreign('ucretlendirilen_id')->references('id')->on('ucretlendirilen');
            $table->integer('depoteslim_id')->unsigned()->nullable();;
            $table->foreign('depoteslim_id')->references('id')->on('depoteslim');
            $table->integer('kullanici_id')->unsigned()->nullable();;
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
		Schema::drop('hurda');
	}

}
