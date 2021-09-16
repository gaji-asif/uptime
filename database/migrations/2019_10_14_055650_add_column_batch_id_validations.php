<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBatchIdValidations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('validations', function (Blueprint $table) {
            $table->string('batch_id',36)->nullable();
        });
        DB::statement('ALTER TABLE `validations` MODIFY `employee_id` Int( 11 ) NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('validations', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
        DB::statement('ALTER TABLE `validations` MODIFY `employee_id` Int( 11 ) NOT NULL DEFAULT 0;');
    }
}
