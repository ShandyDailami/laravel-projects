<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS note_tags (
                note_id UUID NOT NULL REFERENCES notes(id),
                tag_id UUID NOT NULL REFERENCES tags(id),
                created_at TIMESTAMP,
                PRIMARY KEY (note_id, tag_id)
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS note_tags");
    }
};
