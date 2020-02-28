<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdgorusme extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekgorusme', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
                        $table->string('yetkiliadi')->nullable();
                        $table->string('yetkilitel')->nullable();
                        $table->timestamp('tarih')->nullable();
			$table->integer('edestekkonu_id')->unsigned()->nullable();
                        $table->foreign('edestekkonu_id')->references('id')->on('edestekkonu');
			$table->integer('edestekkonudetay_id')->unsigned()->nullable();
                        $table->foreign('edestekkonudetay_id')->references('id')->on('edestekkonudetay');
                        $table->string('problem')->nullable();
                        $table->string('cozum')->nullable();   
			$table->integer('devreden_id')->nullable();  
                        $table->timestamp('devretmetarihi')->nullable();
			$table->integer('edestekpersonel_id')->unsigned()->nullable();
                        $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
                        $table->integer('durum')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekgorusme');
	}

}
