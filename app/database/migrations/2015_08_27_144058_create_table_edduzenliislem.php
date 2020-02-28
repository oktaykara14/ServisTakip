<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdduzenliislem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekduzenliislem', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
                        $table->timestamp('baslangictarih')->nullable();
                        $table->integer('aralik');
                        $table->integer('araliktip');
			$table->integer('edestekkonu_id')->unsigned()->nullable();
                        $table->foreign('edestekkonu_id')->references('id')->on('edestekkonu');
			$table->integer('edestekkonuislem_id')->unsigned()->nullable();
                        $table->foreign('edestekkonuislem_id')->references('id')->on('edestekkonuislem');
                        $table->string('detay')->nullable();
			$table->integer('edestekpersonel_id')->unsigned()->nullable();
                        $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
                        $table->boolean('durum')->default(false);
            $table->timestamp('sonislemtarihi')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekduzenliislem');
	}

}
