<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('value');
            $table->string('group');
            $table->text('comment');
            $table->timestamps();
        });

        DB::table('variables')->insert([
            'name' => 'vmware_cores',
            'value' => '0',
            'group' => 'system',
            'comment' => 'Number of host CPU cores reserved for VMware or other VM workloads.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('variables');
    }
};
