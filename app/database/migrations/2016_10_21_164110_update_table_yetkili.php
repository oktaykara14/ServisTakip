<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableYetkili extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('yetkili', function(Blueprint $table)
		{
            $table->integer('kullanici_id')->unsigned()->after('servis_id')->nullable();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('yetkili', function(Blueprint $table)
		{
            $table->dropForeign('yetkili_kullanici_id_foreign');
            $table->dropColumn('kullanici_id');
		});
	}

}
