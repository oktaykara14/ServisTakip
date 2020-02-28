<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableParabirimi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parabirimi', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('adi');
            $table->string('birimi');
            $table->integer('oncelik');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parabirimi');
	}

}
