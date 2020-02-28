<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdhatacozum extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekhatacozum', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekkonu_id')->unsigned();
            $table->foreign('edestekkonu_id')->references('id')->on('edestekkonu');
			$table->integer('edestekkonudetay_id')->unsigned();
            $table->foreign('edestekkonudetay_id')->references('id')->on('edestekkonudetay');
            $table->string('problem');
            $table->string('cozum');
			$table->integer('edestekpersonel_id')->unsigned();
            $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
            $table->integer('guncelleyen_id')->nullable();
            $table->timestamps();
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekhatacozum');
	}

}
