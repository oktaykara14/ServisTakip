<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableHurda extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hurda', function(Blueprint $table)
		{
            $table->dropColumn('uretimyer_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hurda', function(Blueprint $table)
		{
            $table->integer('uretimyer_id')->unsigned();
		});
	}

}
