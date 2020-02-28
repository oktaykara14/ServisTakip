<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUretimyer extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uretimyer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('yeradi');
            $table->string('issue',5);
            $table->integer('parabirimi_id')->unsigned();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->integer('oracle_id')->nullable();
            $table->integer('mekanik')->default(0);
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uretimyer');
	}

}
