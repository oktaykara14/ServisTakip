<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServisstokkod extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servisstokkod', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('stokkodu',10);
            $table->string('stokadi',100);
            $table->integer('servisid')->unsigned()->default(0)->nullable();
            $table->foreign('servisid')->references('id')->on('servis');
            $table->string('servisbirimi',20);
            $table->string('aciklama',20);
            $table->boolean('koddurum')->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servisstokkod');
	}

}
