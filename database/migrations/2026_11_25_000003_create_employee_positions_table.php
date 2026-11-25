<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePositionsTable extends Migration
{
    /**
     * Penempatan pegawai di unit kerja beserta jabatannya (kondisi terkini).
     * Satu pegawai bisa punya lebih dari satu penempatan,
     * mis. Kepala Seksi Penmad sekaligus Plt Kepala Seksi Bimas Islam.
     *
     * Catatan: "satu kepala per unit" divalidasi di level aplikasi.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_unit_id')->constrained()->restrictOnDelete();
            $table->string('title', 150);                    // jabatan, mis. "Analis SDM Aparatur Ahli Muda"
            $table->boolean('is_head')->default(false);      // true = kepala unit (tampil di bagan)
            $table->boolean('is_acting')->default(false);    // true = Plt
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'work_unit_id']);
            $table->index(['work_unit_id', 'is_head', 'sort_order']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_positions');
    }
}
