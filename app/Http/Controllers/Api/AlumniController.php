<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateAlumniRequest;
use App\Http\Resources\Api\AlumniResource;
use App\Imports\AlumnisImport;
use App\Models\Alumni;
use App\Models\Nisn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AlumniController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        $includedRelations = ['jurusan', 'kuliahs', 'kerjas', 'usahas', 'nisn'];

        $data = Alumni::query()
            ->with($includedRelations)
            ->cursorPaginate();

        return ResponseBuilder::success()
            ->data(AlumniResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    public function getDetail(Request $request): JsonResponse
    {
        $alumni = Alumni::with('jurusan')->find($request->alumni_id);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('Alumni dengan id: ' . $request->alumni_id . ' tidak ada')
                ->build();
        }

        return ResponseBuilder::success()
            ->data(AlumniResource::make($alumni))
            ->build();
    }

    public function checkEmailExist(Request $request): JsonResponse
    {
        $emailExist = Alumni::query()->where('email', $request->input('email'))->first();
        if ($emailExist) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function create(CreateAlumniRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));

        $nisn = Nisn::query()->where('number', $data['nisn'])->first('id');

        $alumni = Alumni::create([...$data, 'nisn_id' => $nisn->id]);

        if (!empty($data['kuliahs'])) {
            foreach ($data['kuliahs'] as $item) {
                $alumni->kuliahs()->create($item);
            }
        }

        if (!empty($data['kerjas'])) {
            foreach ($data['kerjas'] as $item) {
                $item['tgl_mulai'] = date('Y-m-d', strtotime($item['tgl_mulai']));
                $item['tgl_selesai'] = date('Y-m-d', strtotime($item['tgl_selesai']));
                $alumni->kerjas()->create($item);
            }
        }

        if (!empty($data['usahas'])) {
            foreach ($data['usahas'] as $item) {
                $item['tgl_mulai'] = date('Y-m-d', strtotime($item['tgl_mulai']));
                $item['tgl_selesai'] = date('Y-m-d', strtotime($item['tgl_selesai']));
                $alumni->usahas()->create($item);
            }
        }

        return ResponseBuilder::success()
            ->data($alumni)
            ->message('Sukses menambah data alumni baru')
            ->build();
    }

    public function update(Request $request, string $alumniId): JsonResponse
    {
        $alumni = Alumni::find($alumniId);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('data alumni tidak ditemukan')
                ->build();
        }

        $alumni->update($request->all());

        return ResponseBuilder::success()
            ->message('sukses memperbarui data alumni')
            ->data($alumni)
            ->build();
    }

    public function destroy(Request $request): JsonResponse
    {
        $alumni = Alumni::find($request->alumni_id);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('data alumni tidak ditemukan')
                ->build();
        }

        $alumni->delete();

        return ResponseBuilder::success()
            ->message('data alumni berhasil dihapus')
            ->build();
    }

    public function importExcel()
    {
        try {
            Excel::import(new AlumnisImport, request()->file('alumni_excel'));

            return ResponseBuilder::success()
                ->message('Sukses import excel')
                ->build();
        } catch (\Throwable $th) {
            return ResponseBuilder::fail()
                ->message($th->getMessage())
                ->build();
        }
    }

    public function getChart(Request $request): JsonResponse
    {
        $total_pengangguran = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereNull('tempat_kerja')
            ->whereNull('tempat_kuliah')
            ->count();

        $total_kuliah = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereNotNull('tempat_kuliah')
            ->whereNull('tempat_kerja')
            ->count();

        $total_kerja = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereNotNull('tempat_kerja')
            ->whereNull('tempat_kuliah')
            ->count();

        $total_kuliah_dan_kerja =  Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereNotNull('tempat_kerja')
            ->whereNotNull('tempat_kuliah')
            ->count();

        $bar_data = compact('total_pengangguran', 'total_kuliah', 'total_kerja', 'total_kuliah_dan_kerja');

        $pct_tidak_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->where('kesesuaian_kerja', false)
            ->orWhere('kesesuaian_kuliah', false)
            ->count();
        $pct_kuliah_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->where('kesesuaian_kuliah', true)
            ->count();
        $pct_kerja_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->where('kesesuaian_kerja', true)
            ->count();

        $total_pct = ($pct_tidak_sesuai + $pct_kuliah_sesuai + $pct_kerja_sesuai);
        $pct_tidak_sesuai = round($pct_tidak_sesuai / $total_pct * 100);
        $pct_kuliah_sesuai = round($pct_kuliah_sesuai / $total_pct * 100);
        $pct_kerja_sesuai = round($pct_kerja_sesuai / $total_pct * 100);

        $pie_data = compact('pct_tidak_sesuai', 'pct_kuliah_sesuai', 'pct_kerja_sesuai');

        return ResponseBuilder::success()
            ->data(compact('bar_data', 'pie_data'))
            ->build();
    }

    public function checkNisnValid(string $nisn): JsonResponse
    {
        $valid_nisn = Nisn::query()->where('number', $nisn)->first();
        if (!$valid_nisn) {
            return ResponseBuilder::fail()->message('NISN Tidak ditemukan')->build();
        }

        return ResponseBuilder::success()
            ->message('NISN valid')
            ->data($valid_nisn)
            ->build();
    }
}
