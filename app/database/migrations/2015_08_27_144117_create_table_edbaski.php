<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdbaski extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekbaski', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
			$table->integer('edestekplasiyer_id')->unsigned()->nullable();
                        $table->foreign('edestekplasiyer_id')->references('id')->on('edestekplasiyer');
                        $table->timestamp('siparistarihi')->nullable();
			$table->integer('edestekkartbaski_id')->unsigned()->nullable();
                        $table->foreign('edestekkartbaski_id')->references('id')->on('edestekkartbaski');
                        $table->integer('miktar');
                        $table->timestamp('teslimtarihi')->nullable();
			$table->integer('edestekpersonel_id')->unsigned()->nullable();
                        $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
                        $table->boolean('durum')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekbaski');
	}

}
