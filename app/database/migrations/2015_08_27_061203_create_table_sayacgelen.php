<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayacgelen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayacgelen', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('depogelen_id')->unsigned()->nullable();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->string('stokkodu');
            $table->bigInteger('serino');
            $table->timestamp('depotarihi');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayaccap_id')->unsigned();
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->boolean('arizakayit')->default(false);
            $table->boolean('fiyatlandirma')->default(false);
            $table->boolean('musterionay')->default(false);
            $table->boolean('kalibrasyon')->default(false);
            $table->boolean('depoteslim')->default(false);
            $table->integer('sayacdurum')->default(1);
            $table->integer('teslimdurum')->default(1);
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('eklenmetarihi');
            $table->timestamp('guncellenmetarihi');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayacgelen');
	}

}
