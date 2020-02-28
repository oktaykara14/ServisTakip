<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdsistembilgi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edesteksistembilgi', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('cariadi')->nullable();
			$table->integer('edestekplasiyer_id')->unsigned()->nullable();
            $table->foreign('edestekplasiyer_id')->references('id')->on('edestekplasiyer');
			$table->string('urunler')->nullable();
			$table->string('programlar')->nullable();
			$table->string('veritabanlari')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edesteksistembilgi');
	}

}
