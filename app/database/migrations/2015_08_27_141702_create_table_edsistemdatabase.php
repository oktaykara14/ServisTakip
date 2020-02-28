<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdsistemdatabase extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edesteksistemdatabase', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekdatabase_id')->unsigned()->nullable();
                        $table->foreign('edestekdatabase_id')->references('id')->on('edestekdatabase');
                        $table->string('versiyon')->nullable();
                        $table->string('adi')->nullable();
                        $table->string('kullaniciadi')->nullable();
                        $table->string('sifre')->nullable();
                        $table->string('diger')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edesteksistemdatabase');
	}

}
