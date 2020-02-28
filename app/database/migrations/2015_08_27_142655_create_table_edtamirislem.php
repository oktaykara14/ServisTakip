<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdtamirislem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestektamirislem', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestektamirurun_id')->unsigned()->nullable();
                        $table->foreign('edestektamirurun_id')->references('id')->on('edestektamirurun');
                        $table->string('adi');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestektamirislem');
	}

}
