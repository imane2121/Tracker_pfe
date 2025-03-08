<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable(); // For preview images
            $table->unsignedBigInteger('author_id'); // Link to users table
            $table->enum('category', [
                'news',
                'educational', // For educational content
                'awareness', // For awareness campaigns
                'best_practices', // For sharing best practices
                'initiative', // For local initiatives
                'report', // For statistical reports
                'event' // For upcoming events
            ])->default('news');
            $table->json('metadata')->nullable(); // For additional data like related links, resources
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create tags table for article categorization
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Create pivot table for article-tag relationship
        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('tag_id');
            
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            
            $table->unique(['article_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('articles');
    }
}; 