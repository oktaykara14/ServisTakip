<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdkonuislem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekkonuislem', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('edestekkonu_id')->unsigned();
            $table->foreign('edestekkonu_id')->references('id')->on('edestekkonu');
			$table->string('islem');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekkonuislem');
	}

}
