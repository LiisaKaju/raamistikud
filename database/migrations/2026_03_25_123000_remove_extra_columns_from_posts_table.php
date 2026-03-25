<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'author_id')) {
                $table->dropForeign(['author_id']);
                $table->dropColumn('author_id');
            }

            if (Schema::hasColumn('posts', 'published')) {
                $table->dropColumn('published');
            }

            if (Schema::hasColumn('posts', 'content')) {
                $table->dropColumn('content');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'content')) {
                $table->text('content')->nullable();
            }
            if (!Schema::hasColumn('posts', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable();
            }
            if (!Schema::hasColumn('posts', 'published')) {
                $table->boolean('published')->default(false);
            }
        });
    }
};

