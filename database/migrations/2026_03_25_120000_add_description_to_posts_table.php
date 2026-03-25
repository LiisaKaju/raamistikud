<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        // Kui postid olid enne seda veergu loodud, siis kopeeri description = content, et sisu ei kaoks.
        DB::table('posts')->whereNull('description')->update([
            'description' => DB::raw('content'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};

