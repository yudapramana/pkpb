<?php

namespace App\Http\Controllers;

use App\Models\EmployeePosition;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Halaman struktur organisasi. Setiap halaman punya 2 versi (?versi=):
 *  - penugasan (default): penempatan nyata berdasarkan nota tugas/surat perintah, termasuk Plt
 *  - duk: penempatan definitif sesuai Daftar Urut Kepangkatan (SK)
 */
class OrganizationStructureController extends Controller
{
    /** Bagan lengkap dengan foto. */
    public function index(Request $request)
    {
        return view('organization.index', $this->structure($request));
    }

    /** Versi sederhana tanpa foto, mudah disalin ke Word. */
    public function simple(Request $request)
    {
        return view('organization.simple', $this->structure($request));
    }

    /** Bagan pohon tanpa foto (kotak teks), staf menggantung di bawah kepalanya. */
    public function tree(Request $request)
    {
        return view('organization.tree', $this->structure($request));
    }

    private function structure(Request $request)
    {
        $version = $request->query('versi') === EmployeePosition::VERSION_DUK
            ? EmployeePosition::VERSION_DUK
            : EmployeePosition::VERSION_ASSIGNMENT;

        // Hanya penempatan pada versi terpilih, milik pegawai aktif (tidak soft-deleted).
        // employee.positions.workUnit dimuat untuk keterangan "Nota tugas · DUK: ..." / "Ditugaskan ke ...".
        $positions = function ($query) use ($version) {
            $query->forVersion($version)
                ->whereHas('employee', function ($q) {
                    $q->active();
                })
                ->with('employee.positions.workUnit');
        };

        $root = WorkUnit::active()
            ->root()
            ->orderBy('sort_order')
            ->with([
                'headPosition' => $positions,
                'staffPositions' => $positions,
                'children' => function ($query) {
                    $query->active();
                },
                'children.headPosition' => $positions,
                'children.staffPositions' => $positions,
            ])
            ->first();

        // Unit untuk daftar staf: puncak lalu anak-anaknya sesuai urutan.
        $units = $root ? collect([$root])->concat($root->children) : collect();

        // Staf diurutkan berdasarkan tahun masuk (NIP digit 9–12), lalu tanggal lahir.
        foreach ($units as $unit) {
            $unit->setRelation('staffPositions', EmployeePosition::sortBySeniority($unit->staffPositions));
        }

        $children = $root ? $root->children : collect();

        $asOf = config('organization.as_of')
            ? Carbon::parse(config('organization.as_of'))->locale('id')->translatedFormat('d F Y')
            : null;

        // Tautan pindah versi pada halaman yang sama.
        $routeName = $request->route()->getName();
        $versionLinks = [];
        foreach (EmployeePosition::versionLabels() as $key => $label) {
            $versionLinks[] = [
                'label' => $label,
                'url' => route($routeName, $key === EmployeePosition::VERSION_ASSIGNMENT ? [] : ['versi' => $key]),
                'active' => $key === $version,
            ];
        }

        return [
            'root' => $root,
            'units' => $units,
            'sideUnits' => $children->filter->isSideBranch()->values(),
            'branchUnits' => $children->filter->isChartBranch()->values(),
            'functionalUnits' => $children->filter->isFunctionalGroup()->values(),
            'asOf' => $asOf,
            'version' => $version,
            'versionLabel' => EmployeePosition::versionLabels()[$version],
            'versionLinks' => $versionLinks,
            // Parameter versi untuk tautan antarhalaman.
            'versionQuery' => $version === EmployeePosition::VERSION_DUK ? ['versi' => $version] : [],
        ];
    }
}
