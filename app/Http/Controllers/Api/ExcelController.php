<?php

namespace App\Http\Controllers\Api;

use App\Exports\AlumnisExport;
use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Imports\NisnImport;
use App\Models\Nisn;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importNisn()
    {
        try {
            Nisn::query()->doesntHave('alumni')->delete();
            Excel::import(new NisnImport, request()->file('alumni_excel'));

            return ResponseBuilder::success()
                ->message('Sukses import excel')
                ->build();
        } catch (\Throwable $th) {
            return ResponseBuilder::fail()
                ->message($th->getMessage())
                ->build();
        }
    }

    public  function exportAlumni()
    {
        return Excel::download(AlumnisExport::class, 'export_data_alumni.xlsx');
    }
}
