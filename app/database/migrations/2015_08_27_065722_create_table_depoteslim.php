<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDepoteslim extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('depoteslim', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->string('secilenler',512);
            $table->integer('sayacsayisi')->default(0);
            $table->integer('depodurum')->default(0);
            $table->integer('tipi')->default(0);
            $table->integer('periyodik')->default(0);
            $table->integer('subegonderim')->default(0);
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('teslimtarihi');
            $table->string('faturano',30)->nullable();
            $table->string('faturaadres',512)->nullable();
            $table->string('carikod',50)->nullable();
            $table->string('ozelkod',50)->nullable();
            $table->string('plasiyerkod',10)->nullable();
            $table->string('teslimadres',512)->nullable();
            $table->string('depokodu',2)->nullable();
            $table->string('aciklama',20)->nullable();
            $table->string('belge1')->nullable();
            $table->string('belge2')->nullable();
            $table->string('netsiskullanici',100)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('depoteslim');
	}

}
