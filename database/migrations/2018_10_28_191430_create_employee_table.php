<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
         $table->increments('id');
            $table->string('full_name',191);
            $table->string('email',191);
            $table->string('password');
            $table->integer('company_id');
            $table->integer('category_id');
            $table->string('industry',191);
            $table->enum('is_deleted',['0','-1']);
            $table->string('phone_number',255);
            $table->string('image',255);
            $table->integer('point_number');
            $table->integer('access_level');
            $table->rememberToken();
            // $table->string('remember_token',191); 
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
        Schema::dropIfExists('employee');
    }
}
