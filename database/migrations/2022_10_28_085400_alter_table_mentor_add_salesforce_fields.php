<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMentorAddSalesforceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor', function (Blueprint $table) {
            $table->string('externalId')->default("");
            $table->string('externalOfficeId')->default("");
            $table->string('externalOfficeName')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor', function (Blueprint $table) {
            $table->dropColumn('externalId');
            $table->dropColumn('externalOfficeId');
            $table->dropColumn('externalOfficeName');
        });
    }
}
