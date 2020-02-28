<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdkayit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekkayit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
            $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
            $table->integer('konu_id');
			$table->integer('edestekkayitkonu_id')->unsigned()->nullable();
            $table->foreign('edestekkayitkonu_id')->references('id')->on('edestekkayitkonu');
            $table->string('yapilanislem');
			$table->integer('edestekpersonel_id')->unsigned()->nullable();
            $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
            $table->timestamp('tarih');
            $table->integer('durum')->default(0);
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
		Schema::drop('edestekkayit');
	}

}
