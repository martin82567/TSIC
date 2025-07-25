<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMenteeAddSalesforceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentee', function (Blueprint $table) {
            $table->string('externalId')->default("");
            $table->string('externalSchoolId')->default("");
            $table->string('externalSchoolName')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentee', function (Blueprint $table) {
            $table->dropColumn('externalId');
            $table->dropColumn('externalSchoolId');
            $table->dropColumn('externalSchoolName');
        });
    }
}
