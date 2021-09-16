<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenge', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image');
            $table->string('challenge_text');
            $table->enum('status',['-1','0','1'])->default('0');
            $table->integer('point');
            $table->integer('company_id');
            $table->integer('category_id');
            $table->integer('subcategory_id');
            $table->integer('sent_in');
            $table->string('end_on');
            $table->string('preset_type');
            $table->string('type');
            $table->integer('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge');
    }
}
