<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVersionFlagsToEmployeePositionsTable extends Migration
{
    /**
     * Satu pegawai bisa punya penempatan definitif (sesuai SK/DUK) yang berbeda
     * dengan penempatan nyata berdasarkan nota tugas/surat perintah (termasuk Plt).
     *
     *  in_duk  in_assignment
     *    1         1          penempatan normal (DUK = kenyataan)
     *    1         0          penempatan DUK, tetapi pegawai ditugaskan ke unit lain
     *    0         1          penempatan berdasarkan nota tugas / Plt
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_positions', function (Blueprint $table) {
            $table->boolean('in_duk')->default(true)->after('is_acting');
            $table->boolean('in_assignment')->default(true)->after('in_duk');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('employee_positions', function (Blueprint $table) {
            $table->dropColumn(['in_duk', 'in_assignment']);
        });
    }
}
