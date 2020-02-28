<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdsistemurun extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edesteksistemurun', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekurun_id')->unsigned()->nullable();
                        $table->foreign('edestekurun_id')->references('id')->on('edestekurun');
                        $table->string('adi');
                        $table->string('adet');
                        $table->string('issue')->nullable();
                        $table->string('detay')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edesteksistemurun');
	}

}
