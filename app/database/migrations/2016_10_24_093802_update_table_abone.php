<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableAbone extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('abone', function(Blueprint $table)
		{
            $table->string('vergidairesi',40)->after('adisoyadi')->nullable();
            $table->string('tckimlikno',11)->after('adisoyadi')->nullable();
            $table->integer('netsiscari_id')->unsigned()->after('adresi')->nullable();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('abone', function(Blueprint $table)
		{
            $table->dropForeign('abone_netsiscari_id_foreign');
            $table->dropColumn('netsiscari_id');
            $table->dropColumn('vergidairesi');
            $table->dropColumn('tckimlikno');
		});
	}

}
