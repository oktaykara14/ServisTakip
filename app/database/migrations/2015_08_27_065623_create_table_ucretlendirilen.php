<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUcretlendirilen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ucretlendirilen', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->boolean('garanti');
            $table->string('secilenler',512);
            $table->integer('sayacsayisi')->default(0);
            $table->integer('durum')->default(0);
            $table->double('fiyat',25,2)->default(0);
            $table->integer('parabirimi_id')->unsigned();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('kayittarihi');
            $table->timestamp('kurtarihi');
            $table->integer('onaytipi')->nullable();
            $table->integer('mail')->default(0);
            $table->timestamp('gonderimtarihi')->nullable();
            $table->timestamp('reddetmetarihi')->nullable();
            $table->string('musterinotu',512)->nullable();
            $table->string('reddedilenler',512)->nullable();
            $table->timestamp('tekrarkayittarihi')->nullable();
            $table->timestamp('gerigonderimtarihi')->nullable();
            $table->timestamp('garantigonderimtarihi')->nullable();
            $table->string('dosyalar',512)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ucretlendirilen');
	}

}
