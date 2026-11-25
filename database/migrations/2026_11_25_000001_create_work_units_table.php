<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkUnitsTable extends Migration
{
    /**
     * Unit kerja berbentuk pohon (adjacency list).
     * Contoh: Kantor -> Sub Bagian TU, Seksi PAIS, Seksi Bimas Islam, dst.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()
                ->constrained('work_units')
                ->restrictOnDelete(); // unit yang masih punya sub-unit tidak bisa dihapus
            $table->string('name', 150);                  // "Seksi Pendidikan Madrasah"
            $table->string('short_name', 50)->nullable(); // "Penmad" (label section)
            $table->enum('type', ['kantor', 'sub_bagian', 'seksi', 'penyelenggara'])
                ->default('seksi');                       // sub_bagian => cabang samping di bagan
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_units');
    }
}
