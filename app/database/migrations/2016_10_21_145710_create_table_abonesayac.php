<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAbonesayac extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('abonesayac', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('abone_id')->unsigned()->nullable();
            $table->foreign('abone_id')->references('id')->on('abone');
            $table->bigInteger('serino');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayaccap_id')->unsigned()->default(1);
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('abonesayac');
	}

}
