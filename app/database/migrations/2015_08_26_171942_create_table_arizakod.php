<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableArizakod extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('arizakod', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('kod');
            $table->string('tanim');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('garanti');
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
		Schema::drop('arizakod');
	}

}
