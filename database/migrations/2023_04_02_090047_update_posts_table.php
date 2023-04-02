<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts',  function(Blueprint $table){
            $table->enum('status', array('draft', 'published', 'archived'))->default('draft');
            $table->dateTime('publish_date')->nullable();
            $table->boolean('is_featured')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts',  function(Blueprint $table){
            $table->dropColumn('status');
            $table->dropColumn('publish_date');
            $table->dropColumn('is_featured');
        });
    }
};
