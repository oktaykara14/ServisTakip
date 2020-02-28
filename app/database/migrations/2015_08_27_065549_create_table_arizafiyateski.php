<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableArizafiyateski extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('arizafiyateski', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('arizafiyat_id')->unsigned()->nullable();
            $table->foreign('arizafiyat_id')->references('id')->on('arizafiyat');
            $table->bigInteger('ariza_serino');
            $table->integer('sayac_id')->unsigned();
            $table->foreign('sayac_id')->references('id')->on('sayac');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayaccap_id')->unsigned();
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->boolean('ariza_garanti');
            $table->boolean('fiyatdurum');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('arizakayit_id')->unsigned()->nullable();
            $table->foreign('arizakayit_id')->references('id')->on('arizakayit');
            $table->integer('depogelen_id')->unsigned();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('sayacgelen_id')->unsigned();
            $table->foreign('sayacgelen_id')->references('id')->on('sayacgelen');
            $table->string('degisenler',512)->nullable();
            $table->string('genel',512)->nullable();
            $table->string('ozel',512)->nullable();
            $table->string('ucretsiz',512)->nullable();
            $table->decimal('fiyat',12,2)->default(0);
            $table->boolean('indirim')->default(false);
            $table->integer('indirimorani')->default(0);
            $table->decimal('tutar',12,2)->default(0);
            $table->decimal('kdv',12,2)->default(0);
            $table->decimal('toplamtutar',12,2)->default(0);
            $table->integer('parabirimi_id')->unsigned();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->integer('durum')->default(0);
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('kayittarihi');
            $table->timestamp('kurtarihi')->nullable();;
            $table->timestamp('tekrarkayittarihi')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('arizafiyateski');
	}

}
