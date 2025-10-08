<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->json('objectives')->nullable();
            $table->json('actions')->nullable();
            $table->string('contact_info')->nullable();
            $table->json('media_urls')->nullable();
            $table->string('category')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->string('status')->default('upcoming');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['category', 'status']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};