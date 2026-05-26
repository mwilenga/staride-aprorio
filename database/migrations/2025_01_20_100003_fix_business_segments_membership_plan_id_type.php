<?php

use Illuminate\Database\Migrations\Migration;

/**
 * No-op: fix is handled in 2025_01_20_100001 (raw SQL).
 * Kept so environments that already ran an empty/partial 100003 stay consistent.
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
