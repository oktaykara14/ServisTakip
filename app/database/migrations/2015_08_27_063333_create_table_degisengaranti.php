<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDegisengaranti extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('degisengaranti', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sayac_id')->unsigned();
            $table->foreign('sayac_id')->references('id')->on('sayac');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('degisenler_id')->unsigned();
            $table->foreign('degisenler_id')->references('id')->on('degisenler');
            $table->timestamp('sonkayittarihi');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('degisengaranti');
	}

}
