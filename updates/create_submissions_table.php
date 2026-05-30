<?php namespace JumpLink\Forms\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('jumplink_forms_submissions', function ($table) {
            $table->increments('id');
            $table->string('form')->index();          // technischer Schlüssel, z. B. "contact"
            $table->string('form_label')->nullable(); // Anzeigename fürs Backend
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->mediumText('data')->nullable();    // alle Felder als JSON
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jumplink_forms_submissions');
    }
}
