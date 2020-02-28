<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKasakod extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kasakod', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('kasakod',8);
            $table->string('kasaadi',50)->nullable();
            $table->integer('netsiscari_id')->unsigned()->nullable();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
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
		Schema::drop('kasakod');
	}

}
