<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ühtlustab posts tabeli algse create_posts_table skeemiga:
 * id, title, content, author_id (FK authors), published, timestamps.
 *
 * Idempotentne — sobib nii vana katkise SQLite kui uue paigalduse jaoks.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        $this->ensureContentColumn();
        $this->ensureAuthorIdColumn();
        $this->ensurePublishedColumn();
        $this->backfillAuthorIds();
        $this->dropLegacyDescriptionIfRedundant();
    }

    private function ensureContentColumn(): void
    {
        if (Schema::hasColumn('posts', 'content')) {
            return;
        }

        if (Schema::hasColumn('posts', 'description')) {
            DB::statement('ALTER TABLE posts RENAME COLUMN description TO content');

            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->text('content')->nullable();
        });
    }

    private function ensureAuthorIdColumn(): void
    {
        if (Schema::hasColumn('posts', 'author_id')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->constrained('authors')->cascadeOnDelete();
        });
    }

    private function ensurePublishedColumn(): void
    {
        if (Schema::hasColumn('posts', 'published')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('published')->default(false);
        });
    }

    private function backfillAuthorIds(): void
    {
        $firstAuthorId = DB::table('authors')->orderBy('id')->value('id');
        if ($firstAuthorId === null) {
            return;
        }

        DB::table('posts')->whereNull('author_id')->update(['author_id' => $firstAuthorId]);
    }

    private function dropLegacyDescriptionIfRedundant(): void
    {
        if (! Schema::hasColumn('posts', 'description') || ! Schema::hasColumn('posts', 'content')) {
            return;
        }

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        } catch (\Throwable) {
            // Vanem SQLite ilma DROP COLUMN — ignoreeri
        }
    }

    public function down(): void
    {
        // Skeemi parandus ei ole tagasipööratav (andmed võivad olla juba ühendatud).
    }
};
