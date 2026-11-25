<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddKelompokFungsionalTypeToWorkUnitsTable extends Migration
{
    /**
     * Jenis unit baru untuk Kelompok Jabatan Fungsional (Pengawas Sekolah, Penyuluh Agama, dst).
     * Unit jenis ini tampil sebagai kelompok staf, tetapi tidak digambar di bagan.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE work_units MODIFY type "
            . "ENUM('kantor', 'sub_bagian', 'seksi', 'penyelenggara', 'kelompok_fungsional') "
            . "NOT NULL DEFAULT 'seksi'"
        );
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::table('work_units')->where('type', 'kelompok_fungsional')->update(['type' => 'seksi']);

        DB::statement(
            "ALTER TABLE work_units MODIFY type "
            . "ENUM('kantor', 'sub_bagian', 'seksi', 'penyelenggara') "
            . "NOT NULL DEFAULT 'seksi'"
        );
    }
}
