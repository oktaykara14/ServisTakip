<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayacparca extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayacparca', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayaccap_id')->unsigned()->default(1);
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->string('parcalar',512)->nullable();
            $table->integer('parcasayi')->default(0);
            $table->integer('servisstokkod_id')->unsigned()->nullable();
            $table->foreign('servisstokkod_id')->references('id')->on('servisstokkod');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayacparca');
	}

}
