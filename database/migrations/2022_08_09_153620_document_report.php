<?php

use App\Enums\ReportStatus;
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
        Schema::create('document_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('admin_id')->nullable()->index();
            $table->integer('status')->default(ReportStatus::Pending->value());
            $table->text('message')->nullable();
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
        Schema::dropIfExists('document_reports');
    }
};
