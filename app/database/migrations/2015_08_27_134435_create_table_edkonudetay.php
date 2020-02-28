<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdkonudetay extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekkonudetay', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('edestekkonu_id')->unsigned();
            $table->foreign('edestekkonu_id')->references('id')->on('edestekkonu');
			$table->string('detay');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekkonudetay');
	}

}
