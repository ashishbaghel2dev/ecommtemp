<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('about_parts')) {
            Schema::create('about_parts', function (Blueprint $table) {
                $table->id();
                $table->string('title', 160);
                $table->string('slug', 180)->unique();
                $table->text('short_description')->nullable();
                $table->longText('description');
                $table->string('image_1')->nullable();
                $table->string('image_2')->nullable();
                $table->string('image_3')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table) {
                $table->id();
                $table->string('title', 160);
                $table->string('image');
                $table->string('alt_text', 180)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->longText('answer');
                $table->string('written_by', 120)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title', 180);
                $table->string('slug', 200)->unique();
                $table->string('category', 140)->nullable();
                $table->string('image')->nullable();
                $table->string('image_alt', 180)->nullable();
                $table->longText('description')->nullable();
                $table->text('meta_keyword')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_tags')->nullable();
                $table->string('schema_type', 80)->nullable();
                $table->longText('schema_markup')->nullable();
                $table->longText('faq_schema')->nullable();
                $table->string('publish_status', 20)->default('draft');
                $table->boolean('status')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('title', 160);
                $table->string('slug', 180)->unique();
                $table->longText('description')->nullable();
                $table->text('meta_description')->nullable();
                $table->boolean('status')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('inquiries')) {
            Schema::create('inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name', 160);
                $table->string('phone', 30);
                $table->string('email', 180)->nullable();
                $table->longText('message');
                $table->string('status', 30)->default('pending');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('about_parts');
    }
};
