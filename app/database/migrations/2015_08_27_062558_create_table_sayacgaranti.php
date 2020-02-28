<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayacgaranti extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayacgaranti', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayaccap_id')->unsigned()->default(1);
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->integer('garanti');
            $table->string('parcalar',512)->nullable();
            $table->string('garantiler',512)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayacgaranti');
	}

}
