<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeIdToUsersTable extends Migration
{
    /**
     * Hanya sebagian kecil pegawai yang dijadikan user (admin pengelola).
     * Nullable agar akun tanpa data pegawai (mis. superadmin) tetap bisa ada.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->unique()->after('id')
                ->constrained()->nullOnDelete();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropUnique(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
}
