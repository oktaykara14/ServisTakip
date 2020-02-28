<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableDepoteslim extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('depoteslim', function(Blueprint $table)
		{
            $table->dropForeign('depoteslim_uretimyer_id_foreign');
            $table->dropColumn('uretimyer_id');
            $table->integer('parabirimi_id')->unsigned()->nullable();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->string('aktarilandepo',2)->nullable();
		});
        DB::statement('ALTER TABLE depoteslim ADD subegonderim INTEGER NOT NULL CONSTRAINT DF_depoteslim_subegonderim DEFAULT(0)');
        DB::statement('ALTER TABLE depoteslim ADD CONSTRAINT DF_depoteslim_parabirimi DEFAULT(1) FOR parabirimi_id');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('depoteslim', function(Blueprint $table)
		{
            $table->integer('uretimyer_id')->unsigned()->after('netsiscari_id');
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->dropColumn('aktarilandepo');
		});
        DB::statement('ALTER TABLE depoteslim DROP CONSTRAINT DF_depoteslim_subegonderim, COLUMN subegonderim');
        DB::statement('ALTER TABLE depoteslim DROP CONSTRAINT DF_depoteslim_parabirimi, COLUMN parabirimi_id');
	}

}
