<?php

use App\Enums\Restricted;
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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('uuid')->unique()->index();
            $table->text('description')->nullable();
            $table->text('description_unformatted')->nullable();
            $table->text('tags')->nullable()->index();
            $table->string('year')->nullable()->index();
            $table->string('version')->nullable()->index();
            $table->string('deity')->nullable()->index();
            $table->bigInteger('views')->default(0);
            $table->bigInteger('favourites')->default(0);
            $table->text('video')->nullable();
            $table->text('topics')->nullable();
            $table->integer('status')->default(Status::Active->value());
            $table->integer('restricted')->default(Restricted::No->value());
            $table->foreignId('user_id')->nullable()->index();
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
        Schema::dropIfExists('videos');
    }
};
