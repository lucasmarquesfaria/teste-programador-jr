<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Usar SQL direto para modificar a coluna
        DB::statement('ALTER TABLE tarefas MODIFY usuario_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE tarefas MODIFY usuario_id BIGINT UNSIGNED NOT NULL');
    }
};