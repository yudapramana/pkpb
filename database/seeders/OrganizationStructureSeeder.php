<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Struktur Organisasi Kantor Kementerian Agama Kabupaten Pesisir Selatan.
 *
 * Sumber data: Daftar Urut Kepangkatan (DUK) SISDM Kementerian Agama, satker Kantor,
 * dicetak 25/11/2026. Ditambah:
 *  - Plt Kepala Seksi Bimas Islam (dari bagan struktur organisasi; DUK hanya memuat jabatan definitif)
 *  - pegawai Non-ASN (tidak tercatat di DUK)
 *
 * PERHATIAN: seeder ini MEMBANGUN ULANG seluruh unit kerja, pegawai, dan jabatan.
 * Perubahan yang dibuat lewat panel admin akan tertimpa. Yang dipertahankan (berdasarkan NIP):
 * tautan akun user ke pegawai dan foto pegawai.
 */
class OrganizationStructureSeeder extends Seeder
{
    public function run()
    {
        // Simpan tautan user & foto sebelum data dihapus.
        $userLinks = DB::table('users')
            ->join('employees', 'employees.id', '=', 'users.employee_id')
            ->whereNotNull('employees.nip')
            ->pluck('employees.nip', 'users.id');
        $photos = DB::table('employees')->whereNotNull('nip')->whereNotNull('photo')->pluck('photo', 'nip');

        Schema::disableForeignKeyConstraints();

        try {
            DB::transaction(function () use ($userLinks, $photos) {
                DB::table('users')->update(['employee_id' => null]);
                DB::table('employee_positions')->delete();
                DB::table('employees')->delete();
                DB::table('work_units')->delete();

                $units = $this->seedWorkUnits();

                foreach ($this->positions() as $unitKey => $rows) {
                    foreach ($rows as $row) {
                        [$name, $nip, $title] = $row;

                        $employee = $this->upsertEmployee($name, $nip, $photos[$nip] ?? null);

                        EmployeePosition::create(array_merge([
                            'employee_id' => $employee->id,
                            'work_unit_id' => $units[$unitKey]->id,
                            'title' => $title,
                            'is_head' => $row[3] ?? false,
                            'is_acting' => $row[4] ?? false,
                            'sort_order' => 0, // staf diurutkan otomatis berdasarkan NIP
                        ], EmployeePosition::scopeToFlags($row[5] ?? EmployeePosition::SCOPE_BOTH)));
                    }
                }

                $this->applyAssignments($units);

                foreach ($userLinks as $userId => $nip) {
                    $employeeId = Employee::where('nip', $nip)->value('id');
                    if ($employeeId) {
                        DB::table('users')->where('id', $userId)->update(['employee_id' => $employeeId]);
                    }
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /** @return array<string, WorkUnit> */
    private function seedWorkUnits()
    {
        $kantor = WorkUnit::create([
            'name' => 'Kantor Kementerian Agama Kabupaten Pesisir Selatan',
            'short_name' => 'Kankemenag Pessel',
            'type' => WorkUnit::TYPE_KANTOR,
            'sort_order' => 0,
        ]);

        // key => [nama, nama singkat, jenis, urutan]
        $children = [
            'tu'      => ['Sub Bagian Tata Usaha', 'Subbag TU', WorkUnit::TYPE_SUB_BAGIAN, 0],
            'zawa'    => ['Penyelenggara Zakat dan Wakaf', 'Zakat & Wakaf', WorkUnit::TYPE_PENYELENGGARA, 1],
            'bimas'   => ['Seksi Bimbingan Masyarakat Islam', 'Bimas Islam', WorkUnit::TYPE_SEKSI, 2],
            'pontren' => ['Seksi Pendidikan Diniyah dan Pondok Pesantren', 'PD Pontren', WorkUnit::TYPE_SEKSI, 3],
            'pais'    => ['Seksi Pendidikan Agama Islam', 'PAIS', WorkUnit::TYPE_SEKSI, 4],
            'penmad'  => ['Seksi Pendidikan Madrasah', 'Penmad', WorkUnit::TYPE_SEKSI, 5],
            'jf'      => ['Kelompok Jabatan Pengawas', 'Pengawas', WorkUnit::TYPE_KELOMPOK_FUNGSIONAL, 99],
        ];

        $units = ['kantor' => $kantor];
        foreach ($children as $key => [$name, $short, $type, $order]) {
            $units[$key] = WorkUnit::create([
                'parent_id' => $kantor->id,
                'name' => $name,
                'short_name' => $short,
                'type' => $type,
                'sort_order' => $order,
            ]);
        }

        return $units;
    }

    private function upsertEmployee($name, $nip, $photo)
    {
        $attributes = [
            'name' => $name,
            'nip' => $nip,
            'photo' => $photo,
            'employment_type' => $this->employmentType($nip),
            'is_active' => true,
        ];

        // Satu pegawai bisa punya lebih dari satu jabatan (mis. Kasi Penmad + Plt Kasi Bimas).
        return $nip
            ? Employee::firstOrCreate(['nip' => $nip], $attributes)
            : Employee::firstOrCreate(['name' => $name, 'nip' => null], $attributes);
    }

    /**
     * Digit ke-13–14 NIP PPPK berisi "21" (bukan bulan TMT).
     * Tanpa NIP dianggap Non-ASN.
     */
    private function employmentType($nip)
    {
        if (!$nip) {
            return Employee::TYPE_NON_ASN;
        }

        return substr($nip, 12, 2) === '21' ? Employee::TYPE_PPPK : Employee::TYPE_PNS;
    }

    /**
     * Pegawai yang bertugas di unit lain berdasarkan nota tugas / penunjukan langsung.
     * nip => unit tempat bertugas.
     *
     * Penempatan DUK-nya tetap ada (hanya tampil di versi DUK), lalu dibuat penempatan baru
     * di unit tujuan dengan jabatan yang sama (hanya tampil di versi penugasan).
     */
    private function assignments()
    {
        return [
            '198605052023212065' => 'pais',    // Meiriza Lidya, S.S        (DUK: Subbag TU)
            '198810252023211021' => 'pontren', // Fauzhi Abdhilah, S.Kom    (DUK: Subbag TU)
            '199009152023211019' => 'pontren', // Kiki Desetri, S.Pd.I      (DUK: Subbag TU)
            '199307222023211014' => 'penmad',  // Azka Juanda, S.Pd.        (DUK: Subbag TU)
            '198212262005011004' => 'bimas',   // Heru Syafri, A.Ma         (DUK: Penyelenggara Zakat dan Wakaf)
            '197205062007101002' => 'zawa',    // Endrizal, S.Ag, M.A       (DUK: Seksi PD Pontren)
        ];
    }

    /** @param array<string, WorkUnit> $units */
    private function applyAssignments(array $units)
    {
        foreach ($this->assignments() as $nip => $unitKey) {
            $home = EmployeePosition::query()
                ->whereHas('employee', function ($q) use ($nip) {
                    $q->where('nip', $nip);
                })
                ->where('is_head', false)
                ->where('in_duk', true)
                ->firstOrFail();

            $home->update(['in_assignment' => false]);

            EmployeePosition::create([
                'employee_id' => $home->employee_id,
                'work_unit_id' => $units[$unitKey]->id,
                'title' => $home->title,
                'is_head' => false,
                'is_acting' => false,
                'in_duk' => false,
                'in_assignment' => true,
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Format baris: [nama, nip|null, jabatan, is_head = false, is_acting = false, cakupan = SCOPE_BOTH]
     * Urutan staf di halaman publik dihitung otomatis dari NIP, bukan dari urutan di sini.
     */
    private function positions()
    {
        return [
            'kantor' => [
                ['H. Yufrizal, S.Ag., M.H.I', '197206262000031004', 'Kepala Kantor', true],
            ],

            'tu' => [
                ['Yossef Yuda, S.HI, MA', '198008042005011007', 'Kepala Sub Bagian Tata Usaha', true],
                ['Yunefri, S.Kom.', '197406142007101001', 'Pranata Keuangan APBN Penyelia'],
                ['Jarmil, S.ThI', '198005152005011007', 'Analis SDM Aparatur Ahli Muda'],
                ['Muchlis, S.TP, M.M', '197603152005011006', 'Perencana Ahli Muda'],
                ['Anna Yoladevika, S.H', '197505152005012003', 'Penata Layanan Operasional'],
                ['Del Junefri, SE', '197606082009121001', 'Analis Pengelolaan Keuangan APBN Ahli Muda'],
                ['Neldafina, A.Md', '197112252007102002', 'Pranata Keuangan APBN Penyelia'],
                ['Ramadhanera Puteri Madya, SE', '198705122011012008', 'Pranata Keuangan APBN Penyelia'],
                ['Sri Mulyani, S.H.', '198006222014112002', 'Penata Layanan Operasional'],
                ['Pramana Yuda Sayeti, S.Kom, M.T.I', '199407292022031002', 'Pranata Komputer Ahli Pertama'],
                ['Roni Hendra', '198212152005011002', 'Operator Layanan Operasional'],
                ['Syafria Norawati, S.PdI', '198004142007102006', 'Penata Layanan Operasional'],
                ['Fauzhi Abdhilah, S.Kom', '198810252023211021', 'Pengembang Teknologi Pembelajaran Ahli Pertama'],
                ['Lisa Arischa, S.Sos', '199606102025052003', 'Pranata Hubungan Masyarakat Ahli Pertama'],
                ['Deni Marlina, S.Pd.I', '197703072023212012', 'Arsiparis Ahli Pertama'],
                ['Ahmad A\'idil Fauzan, S.E.', '199702212025051003', 'Perencana Ahli Pertama'],
                ['Upik Mike, S.A.P', '198505202014122005', 'Penata Layanan Operasional'],
                ['Kiki Desetri, S.Pd.I', '199009152023211019', 'Pengembang Teknologi Pembelajaran Ahli Pertama'],
                ['M. Fatih Alfaris, S.Si.', '200003132025051003', 'Pengelola Pengadaan Barang/Jasa Ahli Pertama'],
                ['Rini Rahmasari, S.E', '200003062025052013', 'Pengelola Pengadaan Barang/Jasa Ahli Pertama'],
                ['Azka Juanda, S.Pd.', '199307222023211014', 'Pengembang Teknologi Pembelajaran Ahli Pertama'],
                ['Meiriza Lidya, S.S', '198605052023212065', 'Pengembang Teknologi Pembelajaran Ahli Pertama'],
                ['Nigel Rodesta, S.Kom', '200203202025051002', 'Pranata Komputer Ahli Pertama'],
                ['Afrinozayenti, S.AP', '197904092023212012', 'Analis SDM Aparatur Ahli Pertama'],
                ['Koko Nurhadi Yanto', '198305042014111002', 'Operator Layanan Operasional'],
                ['Hendri', '199107232025211012', 'Pengadministrasi Perkantoran'],
                ['Pauzi Damra', '199210012025211009', 'Pengadministrasi Perkantoran'],
                ['Syahril', '197408082025211011', 'Pengadministrasi Perkantoran'],
                ['Muhammad Lutfi', null, 'Sopir'], // Non-ASN, tidak tercatat di DUK
            ],

            'zawa' => [
                ['Gustiwarni, S.Ag, M.M', '196906151996032002', 'Penyelenggara Zakat dan Wakaf', true],
                ['Heru Syafri, A.Ma', '198212262005011004', 'Pengadministrasi Perkantoran'],
                ['Martinis, S.HI', '197101152007102002', 'Penata Kelola Zakat dan Wakaf'],
            ],

            'bimas' => [
                // Plt: dari bagan struktur organisasi (tidak tercatat di DUK) => hanya versi penugasan.
                ['Sumardi, S.Ag, M.Pd', '197107051998031013', 'Plt Kepala Seksi Bimbingan Masyarakat Islam', true, true, EmployeePosition::SCOPE_ASSIGNMENT_ONLY],
                ['Liswarti, S.Th.I', '198104122009012004', 'Pembimbing Teknis Urusan Agama'],
                ['Usri, S.Ag', '197505162014111001', 'Penata Layanan Operasional'],
            ],

            'pontren' => [
                ['Sudirman, S.Ag', '197202112003121003', 'Kepala Seksi Pendidikan Diniyah dan Pondok Pesantren', true],
                ['Endrizal, S.Ag, M.A', '197205062007101002', 'Penata Kelola Madrasah, Pendidikan Agama dan Keagamaan'],
                ['Reni Pitriyeni, S.Pd.I', '198004022007102005', 'Penata Layanan Operasional'],
                ['Yossi Anita', '198508052007102003', 'Pengadministrasi Perkantoran'],
            ],

            'pais' => [
                ['Masrizal, S.Ag, M.Pd', '197202051997031003', 'Kepala Seksi Pendidikan Agama Islam', true],
                ['Nila Oksana, A.Ma., S.Pd.I', '198210132007102002', 'Penata Kelola Madrasah, Pendidikan Agama dan Keagamaan'],
                ['Ermaningsih, S.Pd.I', '197706292007102008', 'Penata Layanan Operasional'],
            ],

            'penmad' => [
                ['Sumardi, S.Ag, M.Pd', '197107051998031013', 'Kepala Seksi Pendidikan Madrasah', true],
                ['Richo Hardiatno, S.Pd', '198312122009011013', 'Analis Pengelolaan Keuangan APBN Ahli Pertama'],
                ['Marseharti, S.Ag', '197208212000032003', 'Penata Layanan Operasional'],
                ['Didik Marhadi, S.Pd', '198708122023211028', 'Pengolah Data dan Informasi'],
                ['Dedi Gustiawan, S.Pd', '198808222023211013', 'Pengembang Teknologi Pembelajaran Ahli Pertama'],
                ['Afnizon, S.A.P', '197610182009011004', 'Pranata Keuangan APBN Mahir'],
                ['Fitri Yuliana', '198402062014122001', 'Pengadministrasi Perkantoran'],
            ],

            'jf' => [
                ['Yeni Roswati, S.Pd', '197009141998032003', 'Pengawas Sekolah Ahli Madya Tingkat Menengah'],
                ['Elimustikawati, S.Ag', '197201012000032002', 'Penyuluh Agama Ahli Madya'],
                ['Hj. Fithidayati, S.Ag, M.Pd', '197409161997032003', 'Pengawas Sekolah Ahli Madya Tingkat Menengah'],
                ['Herma Rosrita, S.Ag, M.A', '196901282000032001', 'Penyuluh Agama Ahli Madya'],
                ['Hasrizal, S.Pd', '197009291995031001', 'Pengawas Sekolah Ahli Madya Tingkat Menengah'],
                ['Burhanapion, S.Ag', '197311272005011002', 'Pengawas Sekolah Ahli Madya Tingkat TK/RA/SD/MI'],
                ['Nurhayani, S.Pd.I, M.A', '197208202005012009', 'Pengawas Sekolah Ahli Madya Tingkat TK/SD'],
                ['Zulfira, S.Ag, M.A', '197105152005011004', 'Pengawas Sekolah Ahli Madya Tingkat Menengah'],
                ['Srianita Zauja, S.Pd, M.Pd', '197306052005012010', 'Pengawas Sekolah Ahli Madya Tingkat Menengah'],
                ['Saipul, S.Ag', '196612312006041259', 'Pengawas Sekolah Ahli Madya Tingkat Dasar'],
                ['Sahril, S.Ag', '197206102005011007', 'Pengawas Sekolah Ahli Madya Tingkat Dasar'],
                ['Rozi Pebria Santi, M.A', '197902142005012005', 'Pengawas Sekolah Ahli Muda'],
            ],
        ];
    }
}
