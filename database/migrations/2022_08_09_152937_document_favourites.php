<?php

use App\Enums\Status;
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
        Schema::create('document_favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->integer('status')->default(Status::Active->value());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_favourites');
    }
};
