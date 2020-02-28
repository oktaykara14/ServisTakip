<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableAbonesayac extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('abonesayac', function(Blueprint $table)
		{
            $table->dropForeign('abonesayac_abone_id_foreign');
            $table->dropColumn('abone_id');
            $table->integer('uretimyer_id')->unsigned()->after('serino');
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
        });
        DB::statement('ALTER TABLE abonesayac ADD satisdurum INTEGER NOT NULL CONSTRAINT DF_abonesayac_satisdurum DEFAULT(0)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('abonesayac', function(Blueprint $table)
		{
            $table->dropForeign('abonesayac_uretimyer_id_foreign');
            $table->dropColumn('uretimyer_id');
		});
        DB::statement('ALTER TABLE abonesayac DROP CONSTRAINT DF_abonesayac_satisdurum, COLUMN satisdurum');
	}

}
