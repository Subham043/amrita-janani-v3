<?php

use App\Enums\Restricted;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->index();
            $table->string('page_name')->unique()->index();
            $table->string('url')->unique()->index();
            $table->integer('status')->default(Status::Active->value());
            $table->integer('restricted')->default(Restricted::No->value());
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        // DB::table('pages')->insert(array('title' => 'Home','page_name' => 'home','url' => 'home','user_id' => 1));
        // DB::table('pages')->insert(array('title' => 'About Us','page_name' => 'about','url' => 'about','user_id' => 1));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
