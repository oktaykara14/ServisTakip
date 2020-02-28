<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableArizakayit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('arizakayit', function(Blueprint $table)
		{
			$table->increments('id');
            $table->float('ilkkredi')->default(0);
            $table->float('ilkharcanan')->default(0);
            $table->float('ilkmekanik')->default(0);
            $table->float('kalankredi')->default(0);
            $table->float('harcanankredi')->default(0);
            $table->float('mekanik')->default(0);
            $table->integer('depogelen_id')->unsigned();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('sayacgelen_id')->unsigned();
            $table->foreign('sayacgelen_id')->references('id')->on('sayacgelen');
            $table->integer('sayac_id')->unsigned();
            $table->foreign('sayac_id')->references('id')->on('sayac');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayaccap_id')->unsigned();
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->boolean('garanti')->nullable();
            $table->string('musteriaciklama',512)->nullable();
            $table->string('arizaaciklama',512)->nullable();
            $table->integer('sayacariza_id')->unsigned();
            $table->foreign('sayacariza_id')->references('id')->on('sayacariza');
            $table->integer('sayacyapilan_id')->unsigned();
            $table->foreign('sayacyapilan_id')->references('id')->on('sayacyapilan');
            $table->integer('sayacdegisen_id')->unsigned();
            $table->foreign('sayacdegisen_id')->references('id')->on('sayacdegisen');
            $table->integer('arizakayit_kullanici_id')->unsigned();
            $table->foreign('arizakayit_kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('arizakayittarihi');
            $table->boolean('arizakayit_durum')->default(false);
            $table->string('resimler',512)->default();
            $table->string('arizanot',512)->nullable();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('arizakayit');
	}

}
