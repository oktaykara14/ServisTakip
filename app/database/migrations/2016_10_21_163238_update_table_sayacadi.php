<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableSayacadi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sayacadi', function(Blueprint $table)
		{
            $table->integer('kalibrasyontipi_id')->unsigned()->nullable();
            $table->foreign('kalibrasyontipi_id')->references('id')->on('kalibrasyontipi');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sayacadi', function(Blueprint $table)
		{
            $table->dropForeign('sayacadi_kalibrasyontipi_id_foreign');
			$table->dropColumn('kalibrasyontipi_id');
		});
	}

}
