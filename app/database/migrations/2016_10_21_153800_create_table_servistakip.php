<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServistakip extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servistakip', function(Blueprint $table)
		{
			$table->increments('id');
            $table->bigInteger('serino');
            $table->integer('sayacadi_id')->unsigned()->nullable();;
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('depogelen_id')->unsigned()->nullable();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('sayacgelen_id')->unsigned()->nullable();
            $table->foreign('sayacgelen_id')->references('id')->on('sayacgelen');
            $table->integer('arizakayit_id')->unsigned()->nullable();
            $table->foreign('arizakayit_id')->references('id')->on('arizakayit');
            $table->integer('arizafiyat_id')->unsigned()->nullable();
            $table->foreign('arizafiyat_id')->references('id')->on('arizafiyat');
            $table->integer('ucretlendirilen_id')->unsigned()->nullable();
            $table->foreign('ucretlendirilen_id')->references('id')->on('ucretlendirilen');
            $table->integer('onaylanan_id')->unsigned()->nullable();
            $table->foreign('onaylanan_id')->references('id')->on('onaylanan');
            $table->integer('kalibrasyon_id')->unsigned()->nullable();
            $table->foreign('kalibrasyon_id')->references('id')->on('kalibrasyon');
            $table->integer('depoteslim_id')->unsigned()->nullable();
            $table->foreign('depoteslim_id')->references('id')->on('depoteslim');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('durum')->default(0);
            $table->timestamp('depotarih')->nullable();
            $table->timestamp('sayacgiristarihi')->nullable();
            $table->timestamp('arizakayittarihi')->nullable();
            $table->timestamp('ucretlendirmetarihi')->nullable();
            $table->timestamp('gondermetarihi')->nullable();
            $table->timestamp('onaylanmatarihi')->nullable();
            $table->timestamp('reddetmetarihi')->nullable();
            $table->timestamp('tekrarucrettarihi')->nullable();
            $table->timestamp('kalibrasyontarihi')->nullable();
            $table->timestamp('depoteslimtarihi')->nullable();
            $table->timestamp('gerigonderimtarihi')->nullable();
            $table->timestamp('hurdalamatarihi')->nullable();
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('sonislemtarihi')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servistakip');
	}

}
