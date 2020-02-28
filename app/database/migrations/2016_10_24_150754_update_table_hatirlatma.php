<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableHatirlatma extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hatirlatma', function(Blueprint $table)
		{
            $table->dropForeign('hatirlatma_uretimyer_id_foreign');
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
		Schema::table('hatirlatma', function(Blueprint $table)
		{
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
		});
	}

}
