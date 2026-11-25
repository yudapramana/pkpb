<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Data pegawai (ASN maupun Non-ASN).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);                  // nama lengkap beserta gelar
            $table->string('nip', 18)->nullable()->unique(); // string: 18 digit, Non-ASN boleh kosong
            $table->string('photo')->nullable();          // path relatif di disk "public"
            $table->enum('employment_type', ['pns', 'pppk', 'non_asn'])->default('pns');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
