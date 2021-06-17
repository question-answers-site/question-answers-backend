<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topicables', function (Blueprint $table) {
            $table->integer('topicable_id')->unsigned();
            $table->string('topicable_type');
            $table->integer('topic_id')->unsigned();
            $table->float('rank')->default(0);
            $table->integer('answers_count')->default(0);
            $table->timestamps();
            $table->primary(['topicable_id','topicable_type','topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topicables');
    }
}
