<?php

use Illuminate\Database\Migrations\Migration;

class DropTableHatirlatmaUyari extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('hatirlatma_uyari');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}

}
