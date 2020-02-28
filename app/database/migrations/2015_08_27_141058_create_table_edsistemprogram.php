<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdsistemprogram extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edesteksistemprogram', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekprogram_id')->unsigned()->nullable();
                        $table->foreign('edestekprogram_id')->references('id')->on('edestekprogram');
                        $table->string('versiyon')->nullable();
                        $table->string('kullaniciadi')->nullable();
                        $table->string('sifre')->nullable();
                        $table->string('yetkilisifre')->nullable();
                        $table->string('diger')->nullable();
			$table->integer('edestekentegrasyonfirma_id')->unsigned()->nullable();
                        $table->foreign('edestekentegrasyonfirma_id')->references('id')->on('edestekentegrasyonfirma');
			$table->integer('edestekentegrasyontip_id')->unsigned()->nullable();
                        $table->foreign('edestekentegrasyontip_id')->references('id')->on('edestekentegrasyontip');
			$table->integer('edestekentegrasyonprogram_id')->unsigned()->nullable();
                        $table->foreign('edestekentegrasyonprogram_id')->references('id')->on('edestekentegrasyonprogram');
			$table->integer('edestekentegrasyonversiyon_id')->unsigned()->nullable();
                        $table->foreign('edestekentegrasyonversiyon_id')->references('id')->on('edestekentegrasyonversiyon');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edesteksistemprogram');
	}

}
