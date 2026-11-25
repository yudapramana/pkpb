<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom tambahan agar item portal bisa dikelola dari panel admin:
 * - image_path : ikon hasil unggahan (disk public), diutamakan daripada image_url
 * - sort_order : urutan tampil di halaman depan
 * - is_active  : sembunyikan item tanpa menghapusnya
 */
class AddAdminFieldsToPortalItemsTable extends Migration
{
    public function up()
    {
        Schema::table('portal_items', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('image_url');
            $table->unsignedInteger('sort_order')->default(0)->after('is_modal');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->index(['is_active', 'sort_order']);
        });

        // Pertahankan urutan lama (berdasarkan id) untuk data yang sudah ada.
        DB::table('portal_items')->update(['sort_order' => DB::raw('id * 10')]);
    }

    public function down()
    {
        Schema::table('portal_items', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'sort_order']);
            $table->dropColumn(['image_path', 'sort_order', 'is_active']);
        });
    }
}
