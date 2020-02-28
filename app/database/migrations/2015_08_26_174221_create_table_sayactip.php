<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayactip extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayactip', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sayacmarka_id')->unsigned();
            $table->foreign('sayacmarka_id')->references('id')->on('sayacmarka');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
			$table->string('tipadi');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayactip');
	}

}
