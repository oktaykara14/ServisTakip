<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableHurdanedeni extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hurdanedeni', function(Blueprint $table)
		{
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
		});
        DB::statement('ALTER TABLE hurdanedeni ADD kullanim INTEGER NULL CONSTRAINT DF_hurdanedeni_kullanim DEFAULT(0)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hurdanedeni', function(Blueprint $table)
		{
            $table->dropForeign('hurdanedeni_sayactur_id_foreign');
            $table->dropColumn('sayactur_id');
		});
        DB::statement('ALTER TABLE hurdanedeni DROP CONSTRAINT DF_hurdanedeni_kullanim, COLUMN kullanim');
	}

}
