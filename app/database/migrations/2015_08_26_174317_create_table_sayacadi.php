<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayacadi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayacadi', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('sayacadi');
            $table->integer('sayacmarka_id')->unsigned();
            $table->foreign('sayacmarka_id')->references('id')->on('sayacmarka');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayactip_id')->unsigned();
            $table->foreign('sayactip_id')->references('id')->on('sayactip');
            $table->boolean('cap');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayacadi');
	}

}
