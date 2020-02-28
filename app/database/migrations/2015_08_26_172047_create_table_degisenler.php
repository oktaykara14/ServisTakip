<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDegisenler extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('degisenler', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tanim');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('parcadurum'); //YOK,VAR,PARCA
            $table->string('parcalar')->default();
            $table->boolean('stokkontrol');
            $table->boolean('sabit')->default(false);
            $table->integer('kullanim')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('degisenler');
	}

}
