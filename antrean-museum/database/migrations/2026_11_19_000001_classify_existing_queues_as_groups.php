<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('queues')->update([
            'visitor_type' => 'group',
            'institution_name' => DB::raw('visitor_name'),
        ]);
    }

    public function down(): void
    {
        // The original visitor categories cannot be recovered safely.
    }
};
