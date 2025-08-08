<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Dompdf\Dompdf as BaseDompdf;
use Dompdf\Options;

class CVController extends Controller
{
    public function index()
    {
        $alumni = Auth::guard('alumni')->user();
        $cvs = $alumni->cvs ?? collect();
        
        return view('alumni.cv.index', compact('alumni', 'cvs'));
    }

    public function create()
    {
        $alumni = Auth::guard('alumni')->user();
        $templates = $this->getTemplates();
        
        return view('alumni.cv.create', compact('alumni', 'templates'));
    }

    public function store(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'template' => 'required|string|in:modern,classic,creative',
                'data_source' => 'required|in:profile,custom',
                'set_as_default' => 'boolean',
            ];
            
            // Add custom field validation only if data_source is 'custom'
            if ($request->input('data_source') === 'custom') {
                $rules = array_merge($rules, [
                    'custom_name' => 'required|string|max:255',
                    'custom_last_name' => 'nullable|string|max:255',
                    'custom_email' => 'required|email',
                    'custom_phone' => 'nullable|string|max:20',
                    'custom_birth_date' => 'nullable|date',
                    'custom_birth_place' => 'nullable|string|max:255',
                    'custom_address' => 'nullable|string|max:500',
                    'custom_postal_code' => 'nullable|string|max:10',
                    'custom_city' => 'nullable|string|max:255',
                    'custom_gender' => 'nullable|string|in:Laki-laki,Perempuan',
                    'custom_nationality' => 'nullable|string|max:255',
                    'custom_skills' => 'nullable|string|max:2000',
                    
                    // Experience fields (arrays)
                    'custom_job_title' => 'nullable|array',
                    'custom_job_title.*' => 'nullable|string|max:255',
                    'custom_company' => 'nullable|array',
                    'custom_company.*' => 'nullable|string|max:255',
                    'custom_start_month' => 'nullable|array',
                    'custom_start_month.*' => 'nullable|string',
                    'custom_start_year' => 'nullable|array',
                    'custom_start_year.*' => 'nullable|string',
                    'custom_end_month' => 'nullable|array',
                    'custom_end_month.*' => 'nullable|string',
                    'custom_end_year' => 'nullable|array',
                    'custom_end_year.*' => 'nullable|string',
                    'custom_job_description' => 'nullable|array',
                    'custom_job_description.*' => 'nullable|string|max:1000',
                    
                    // Education fields (arrays)
                    'custom_school_name' => 'nullable|array',
                    'custom_school_name.*' => 'nullable|string|max:255',
                    'custom_degree' => 'nullable|array',
                    'custom_degree.*' => 'nullable|string|max:255',
                    'custom_edu_start_month' => 'nullable|array',
                    'custom_edu_start_month.*' => 'nullable|string',
                    'custom_edu_start_year' => 'nullable|array',
                    'custom_edu_start_year.*' => 'nullable|string',
                    'custom_edu_end_month' => 'nullable|array',
                    'custom_edu_end_month.*' => 'nullable|string',
                    'custom_edu_end_year' => 'nullable|array',
                    'custom_edu_end_year.*' => 'nullable|string',
                    'custom_edu_description' => 'nullable|array',
                    'custom_edu_description.*' => 'nullable|string|max:500',
                    
                    // Reference fields
                    'references_available' => 'nullable|boolean',
                    'custom_ref_name_1' => 'nullable|string|max:255',
                    'custom_ref_position_1' => 'nullable|string|max:255',
                    'custom_ref_phone_1' => 'nullable|string|max:20',
                    'custom_ref_name_2' => 'nullable|string|max:255',
                    'custom_ref_position_2' => 'nullable|string|max:255',
                    'custom_ref_phone_2' => 'nullable|string|max:20',
                ]);
            }
            
            $request->validate($rules);

            $alumni = Auth::guard('alumni')->user();
            
            // Load jurusan relationship to avoid N+1 queries
            $alumni->load('jurusan');
            
            \Log::info('CV Store - Request Data:', [
                'data_source' => $request->input('data_source'),
                'title' => $request->input('title'),
                'template' => $request->input('template'),
                'alumni_id' => $alumni->id,
            ]);
            
            // Generate CV data
            $cvData = $this->generateCVData($request, $alumni);
            
            \Log::info('CV Store - Generated CV Data:', $cvData);
        
            // Generate PDF
            $pdf = $this->generatePDF($cvData, $request->template);
            
            \Log::info('CV Store - PDF Generated successfully');
            
            // Save CV
            $filename = 'cv_' . time() . '_' . $alumni->id . '.pdf';
            Storage::disk('public')->put('cvs/' . $filename, $pdf);
            
            \Log::info('CV Store - File saved:', ['filename' => $filename]);
            
            // Save CV record to database
            $cv = $alumni->cvs()->create([
                'title' => $request->title,
                'template' => $request->template,
                'filename' => $filename,
                'data' => $cvData,
                'is_default' => $request->has('set_as_default'),
            ]);
            
            \Log::info('CV Store - CV record created:', ['cv_id' => $cv->id]);
            
            // If this is set as default, unset others
            if ($request->has('set_as_default')) {
                $alumni->cvs()->where('id', '!=', $cv->id)->update(['is_default' => false]);
            }
            
            return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil dibuat!');
            
        } catch (\Exception $e) {
            \Log::error('CV Store - Error occurred:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'alumni_id' => Auth::guard('alumni')->id(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat CV: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        return view('alumni.cv.show', compact('cv'));
    }

    public function download($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        $path = Storage::disk('public')->path('cvs/' . $cv->filename);
        
        return response()->download($path, $cv->title . '.pdf');
    }

    public function preview($id)
    {
        $alumni = Auth::guard('alumni')->user();
        
        // Debug: log authentication status
        \Log::info('CV Preview Debug', [
            'alumni_authenticated' => $alumni ? true : false,
            'alumni_id' => $alumni ? $alumni->id : null,
            'cv_id' => $id,
            'auth_check' => Auth::guard('alumni')->check()
        ]);
        
        if (!$alumni) {
            abort(401, 'Unauthorized - Alumni not authenticated');
        }
        
        $cv = $alumni->cvs()->findOrFail($id);
        
        $path = Storage::disk('public')->path('cvs/' . $cv->filename);
        
        // Periksa apakah file ada
        if (!file_exists($path)) {
            abort(404, 'File CV tidak ditemukan: ' . $path);
        }
        
        // Return PDF untuk preview di browser (bukan download)
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $cv->title . '.pdf"'
        ]);
    }

    public function destroy($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        // Delete file
        Storage::disk('public')->delete('cvs/' . $cv->filename);
        
        // Delete record
        $cv->delete();
        
        return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil dihapus!');
    }

    public function setAsDefault($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        // Unset all other CVs as default
        $alumni->cvs()->update(['is_default' => false]);
        
        // Set this CV as default
        $cv->update(['is_default' => true]);
        
        return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil diatur sebagai default!');
    }

    private function getTemplates()
    {
        return [
            'modern' => [
                'name' => 'Modern',
                'description' => 'Template modern dengan layout yang clean dan profesional',
                'preview' => 'modern-preview.png'
            ],
            'classic' => [
                'name' => 'Classic',
                'description' => 'Template klasik yang formal dan mudah dibaca',
                'preview' => 'classic-preview.png'
            ],
            'creative' => [
                'name' => 'Creative',
                'description' => 'Template kreatif dengan warna-warna yang dinamis',
                'preview' => 'creative-preview.png'
            ]
        ];
    }

    private function generateCVData(Request $request, $alumni)
    {
        if ($request->input('data_source') === 'profile') {
            // Debug: Log alumni data
            \Log::info('CV Generation - Alumni Data:', [
                'id' => $alumni->id,
                'nama' => $alumni->nama,
                'nama_lengkap' => $alumni->nama_lengkap,
                'email' => $alumni->email,
                'phone' => $alumni->phone,
                'no_tlp' => $alumni->no_tlp,
                'alamat' => $alumni->alamat,
                'tanggal_lahir' => $alumni->tanggal_lahir,
                'tgl_lahir' => $alumni->tgl_lahir,
                'jenis_kelamin' => $alumni->jenis_kelamin,
                'nisn' => $alumni->nisn,
                'tahun_mulai' => $alumni->tahun_mulai,
                'tahun_lulus' => $alumni->tahun_lulus,
                'tempat_kerja' => $alumni->tempat_kerja,
                'jabatan_kerja' => $alumni->jabatan_kerja,
                'tempat_kuliah' => $alumni->tempat_kuliah,
                'prodi_kuliah' => $alumni->prodi_kuliah,
                'pengalaman_kerja' => $alumni->pengalaman_kerja,
                'keahlian' => $alumni->keahlian,
                'jurusan' => $alumni->jurusan ? $alumni->jurusan->nama : null,
            ]);
            
            // Use profile data with proper fallbacks and formatting
            // Access raw attributes to avoid accessor method issues
            $namaLengkap = $alumni->getAttributeValue('nama_lengkap') ?: $alumni->getAttributeValue('nama');
            $phone = $alumni->getAttributeValue('phone') ?: $alumni->getAttributeValue('no_tlp');
            $tanggalLahir = $alumni->getAttributeValue('tanggal_lahir') ?: $alumni->getAttributeValue('tgl_lahir');
            
            return [
                'name' => $namaLengkap ?: 'Nama Lengkap',
                'email' => $alumni->email ?: 'email@example.com',
                'phone' => $phone ?: 'No. Telepon',
                'address' => $alumni->alamat ?: 'Alamat',
                'birth_date' => $tanggalLahir,
                'birth_place' => $alumni->getAttributeValue('tempat_lahir') ?? 'Tempat Lahir',
                'gender' => $alumni->jenis_kelamin ? ($alumni->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') : 'Jenis Kelamin',
                'nisn' => $alumni->nisn ?: 'NISN',
                'education' => [
                    'school' => 'SMKN 1 Surabaya',
                    'major' => $alumni->jurusan ? $alumni->jurusan->nama : 'Jurusan',
                    'start_year' => $alumni->tahun_mulai ?: 'Tahun Mulai',
                    'graduation_year' => $alumni->tahun_lulus ?: 'Tahun Lulus',
                    'nisn' => $alumni->nisn ?: 'NISN',
                ],
                'work_experience' => [
                    'current_job' => $alumni->tempat_kerja ?: null,
                    'current_position' => $alumni->jabatan_kerja ?: null,
                    'job_suitability' => $alumni->kesesuaian_kerja,
                    'experience_detail' => $alumni->pengalaman_kerja ?: 'Belum ada pengalaman kerja',
                ],
                'higher_education' => [
                    'university' => $alumni->tempat_kuliah ?: null,
                    'study_program' => $alumni->prodi_kuliah ?: null,
                    'education_suitability' => $alumni->kesesuaian_kuliah,
                ],
                'skills' => $alumni->keahlian ?: 'Belum ada keahlian yang dicantumkan',
                'photo' => $alumni->foto ? asset('storage/photos/' . $alumni->foto) : null,
                'created_at' => now()->format('d F Y'),
            ];
        } else {
            // Use custom data from form
            $fullName = trim($request->input('custom_name') . ' ' . $request->input('custom_last_name'));
            
            // Process experience data
            $experiences = [];
            $jobTitles = $request->input('custom_job_title', []);
            $companies = $request->input('custom_company', []);
            $startMonths = $request->input('custom_start_month', []);
            $startYears = $request->input('custom_start_year', []);
            $endMonths = $request->input('custom_end_month', []);
            $endYears = $request->input('custom_end_year', []);
            $jobDescriptions = $request->input('custom_job_description', []);
            
            for ($i = 0; $i < count($jobTitles); $i++) {
                if (!empty($jobTitles[$i]) || !empty($companies[$i])) {
                    $startDate = '';
                    if (!empty($startMonths[$i]) && !empty($startYears[$i])) {
                        $startDate = $this->getMonthName($startMonths[$i]) . ' ' . $startYears[$i];
                    }
                    
                    $endDate = '';
                    if (!empty($endMonths[$i])) {
                        if ($endMonths[$i] === 'current') {
                            $endDate = 'Sekarang';
                        } elseif (!empty($endYears[$i])) {
                            $endDate = $this->getMonthName($endMonths[$i]) . ' ' . $endYears[$i];
                        }
                    }
                    
                    $experiences[] = [
                        'job_title' => $jobTitles[$i] ?? '',
                        'company' => $companies[$i] ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'description' => $jobDescriptions[$i] ?? '',
                    ];
                }
            }
            
            // Process education data
            $educations = [];
            $schoolNames = $request->input('custom_school_name', []);
            $degrees = $request->input('custom_degree', []);
            $eduStartMonths = $request->input('custom_edu_start_month', []);
            $eduStartYears = $request->input('custom_edu_start_year', []);
            $eduEndMonths = $request->input('custom_edu_end_month', []);
            $eduEndYears = $request->input('custom_edu_end_year', []);
            $eduDescriptions = $request->input('custom_edu_description', []);
            
            for ($i = 0; $i < count($schoolNames); $i++) {
                if (!empty($schoolNames[$i]) || !empty($degrees[$i])) {
                    $startDate = '';
                    if (!empty($eduStartMonths[$i]) && !empty($eduStartYears[$i])) {
                        $startDate = $this->getMonthName($eduStartMonths[$i]) . ' ' . $eduStartYears[$i];
                    }
                    
                    $endDate = '';
                    if (!empty($eduEndMonths[$i])) {
                        if ($eduEndMonths[$i] === 'current') {
                            $endDate = 'Sekarang';
                        } elseif (!empty($eduEndYears[$i])) {
                            $endDate = $this->getMonthName($eduEndMonths[$i]) . ' ' . $eduEndYears[$i];
                        }
                    }
                    
                    $educations[] = [
                        'school_name' => $schoolNames[$i] ?? '',
                        'degree' => $degrees[$i] ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'description' => $eduDescriptions[$i] ?? '',
                    ];
                }
            }
            
            // Process references
            $references = [];
            if ($request->input('references_available')) {
                if ($request->input('custom_ref_name_1')) {
                    $references[] = [
                        'name' => $request->input('custom_ref_name_1'),
                        'position' => $request->input('custom_ref_position_1'),
                        'phone' => $request->input('custom_ref_phone_1'),
                    ];
                }
                if ($request->input('custom_ref_name_2')) {
                    $references[] = [
                        'name' => $request->input('custom_ref_name_2'),
                        'position' => $request->input('custom_ref_position_2'),
                        'phone' => $request->input('custom_ref_phone_2'),
                    ];
                }
            }
            
            return [
                'name' => $fullName ?: 'Nama Lengkap',
                'email' => $request->input('custom_email') ?: 'email@example.com',
                'phone' => $request->input('custom_phone') ?: 'No. Telepon',
                'address' => $request->input('custom_address') ?: 'Alamat',
                'city' => $request->input('custom_city') ?: '',
                'postal_code' => $request->input('custom_postal_code') ?: '',
                'birth_date' => $request->input('custom_birth_date'),
                'birth_place' => $request->input('custom_birth_place') ?: 'Tempat Lahir',
                'gender' => $request->input('custom_gender') ?: 'Jenis Kelamin',
                'nationality' => $request->input('custom_nationality') ?: 'Indonesia',
                'nisn' => $alumni->nisn ?: 'NISN',
                'experiences' => $experiences,
                'educations' => $educations,
                'skills' => $request->input('custom_skills') ?: 'Belum ada keahlian yang dicantumkan',
                'references' => $references,
                'references_available' => $request->input('references_available', false),
                'photo' => null,
                'created_at' => now()->format('d F Y'),
                
                // Legacy fields for backward compatibility
                'education' => [
                    'school' => !empty($educations) ? $educations[0]['school_name'] : 'SMKN 1 Surabaya',
                    'major' => !empty($educations) ? $educations[0]['degree'] : ($alumni->jurusan ? $alumni->jurusan->nama : 'Jurusan'),
                    'start_year' => $alumni->tahun_mulai ?: 'Tahun Mulai',
                    'graduation_year' => $alumni->tahun_lulus ?: 'Tahun Lulus',
                    'nisn' => $alumni->nisn ?: 'NISN',
                ],
                'work_experience' => [
                    'current_job' => !empty($experiences) ? $experiences[0]['company'] : null,
                    'current_position' => !empty($experiences) ? $experiences[0]['job_title'] : null,
                    'job_suitability' => null,
                    'experience_detail' => !empty($experiences) ? $experiences[0]['description'] : 'Belum ada pengalaman kerja',
                ],
                'higher_education' => [
                    'university' => null,
                    'study_program' => null,
                    'education_suitability' => null,
                ],
            ];
        }
    }

    private function generatePDF($data, $template)
    {
        // Generate HTML based on template
        $html = $this->generateHTML($data, $template);
        
        try {
            // Method 1: Try Laravel DomPDF Facade
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->output();
        } catch (\Exception $e) {
            try {
                // Method 2: Try DomPDF wrapper from service container
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                
                return $pdf->output();
            } catch (\Exception $e2) {
                try {
                    // Method 3: Use direct Dompdf instantiation
                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $options->set('isRemoteEnabled', true);
                    
                    $dompdf = new BaseDompdf($options);
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    
                    return $dompdf->output();
                } catch (\Exception $e3) {
                    // If all methods fail, return error
                    throw new \Exception('PDF generation failed: ' . $e3->getMessage());
                }
            }
        }
    }

    private function generateHTML($data, $template)
    {
        // Use public_path for DomPDF compatibility
        // Main watermark
        $watermarkPath = public_path('assets/images/watermark-smkn1.png');
        $watermarkBase64 = '';
        if (file_exists($watermarkPath)) {
            $watermarkBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($watermarkPath));
        }
        // BKK logo watermark
        $bkkLogoPath = public_path('assets/images/logo BKK.png');
        $bkkLogoBase64 = '';
        if (file_exists($bkkLogoPath)) {
            $bkkLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bkkLogoPath));
        }
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>CV - ' . htmlspecialchars($data['name']) . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: #fff;
                    position: relative;
                    color: #333;
                }
                .watermark {
                    position: absolute;
                    top: 35%;
                    left: 50%;
                    width: 400px;
                    height: auto;
                    opacity: 0.08;
                    z-index: 0;
                    transform: translate(-50%, -50%);
                }
                .bkk-watermark {
                    position: absolute;
                    bottom: 40px;
                    right: 40px;
                    width: 120px;
                    height: auto;
                    opacity: 0.12;
                    z-index: 0;
                }
                .cv-container {
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 30px 40px;
                    position: relative;
                    z-index: 1;
                }
                .personal-info {
                    display: flex;
                    align-items: flex-start;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #2563eb;
                    padding-bottom: 20px;
                }
                .personal-photo {
                    width: 110px;
                    height: 110px;
                    border-radius: 8px;
                    background: #f0f0f0;
                    object-fit: cover;
                    margin-right: 30px;
                }
                .personal-details {
                    flex: 1;
                }
                .personal-details h1 {
                    font-size: 2.1em;
                    margin: 0 0 8px 0;
                    color: #2563eb;
                }
                .personal-details .row {
                    display: flex;
                    flex-wrap: wrap;
                    margin-bottom: 7px;
                }
                .personal-details .row label {
                    width: 120px;
                    font-weight: bold;
                    color: #555;
                }
                .personal-details .row span {
                    flex: 1;
                }
                .section {
                    margin-bottom: 22px;
                }
                .section-title {
                    color: #2563eb;
                    font-size: 1.18em;
                    font-weight: bold;
                    margin-bottom: 7px;
                    border-left: 4px solid #2563eb;
                    padding-left: 8px;
                }
                .section-content {
                    margin-left: 12px;
                }
                .skills-list {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }
                .skill-tag {
                    background: #e0e7ff;
                    color: #1e40af;
                    border-radius: 5px;
                    padding: 4px 12px;
                    font-size: 0.97em;
                }
                .footer {
                    margin-top: 30px;
                    text-align: right;
                    color: #888;
                    font-size: 0.95em;
                }
            </style>
        </head>
        <body>
            <div class="watermark">
                '.($watermarkBase64 ? '<img src="'.$watermarkBase64.'" alt="SMKN 1 Surabaya" style="width: 400px; height: auto;">' : '').'
            </div>
            <div class="bkk-watermark">
                '.($bkkLogoBase64 ? '<img src="'.$bkkLogoBase64.'" alt="BKK SMKN 1 Surabaya" style="width: 120px; height: auto;">' : '').'
            </div>
            <div class="cv-container">
                <div class="personal-info">
                    <div>
                        <img class="personal-photo" src="'.(isset($data['photo']) && $data['photo'] ? htmlspecialchars($data['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($data['name']).'&background=E0E7FF&color=1E40AF&size=110').'" alt="Foto Profil">
                    </div>
                    <div class="personal-details">
                        <h1>' . htmlspecialchars($data['name']) . '</h1>
                        <div class="row"><label>Email</label><span>' . htmlspecialchars($data['email']) . '</span></div>
                        <div class="row"><label>Telepon</label><span>' . htmlspecialchars($data['phone']) . '</span></div>
                        <div class="row"><label>Alamat</label><span>' . htmlspecialchars($data['address'] ?? '-') . (isset($data['city']) && $data['city'] ? ', ' . htmlspecialchars($data['city']) : '') . (isset($data['postal_code']) && $data['postal_code'] ? ' ' . htmlspecialchars($data['postal_code']) : '') . '</span></div>
                        <div class="row"><label>Tempat, Tgl Lahir</label><span>' . htmlspecialchars(($data['birth_place'] ?? '-') . ', ' . ($data['birth_date'] ?? '-')) . '</span></div>
                        <div class="row"><label>Jenis Kelamin</label><span>' . htmlspecialchars($data['gender'] ?? '-') . '</span></div>';
                        
        if (isset($data['nationality']) && $data['nationality']) {
            $html .= '<div class="row"><label>Kewarganegaraan</label><span>' . htmlspecialchars($data['nationality']) . '</span></div>';
        }
        
        $html .= '<div class="row"><label>NISN</label><span>' . htmlspecialchars($data['nisn'] ?? '-') . '</span></div>
                    </div>
                </div>';

        // Education Section - check if we have detailed education data
        $html .= '<div class="section">
                    <div class="section-title">Pendidikan</div>
                    <div class="section-content">';
        
        if (isset($data['educations']) && !empty($data['educations'])) {
            // Display detailed education data from form
            foreach ($data['educations'] as $education) {
                $html .= '<div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-left: 4px solid #2563eb;">
                            <strong>' . htmlspecialchars($education['school_name']) . '</strong><br>
                            <em>' . htmlspecialchars($education['degree']) . '</em><br>';
                
                if ($education['start_date'] || $education['end_date']) {
                    $html .= '<small style="color: #666;">';
                    if ($education['start_date']) {
                        $html .= htmlspecialchars($education['start_date']);
                    }
                    if ($education['start_date'] && $education['end_date']) {
                        $html .= ' - ';
                    }
                    if ($education['end_date']) {
                        $html .= htmlspecialchars($education['end_date']);
                    }
                    $html .= '</small><br>';
                }
                
                if ($education['description']) {
                    $html .= '<small>' . nl2br(htmlspecialchars($education['description'])) . '</small>';
                }
                
                $html .= '</div>';
            }
        } else {
            // Display basic education data
            $html .= '<strong>Sekolah:</strong> ' . htmlspecialchars($data['education']['school']) . '<br>
                      <strong>Jurusan:</strong> ' . htmlspecialchars($data['education']['major']) . '<br>
                      <strong>Tahun Masuk:</strong> ' . htmlspecialchars($data['education']['start_year']) . '<br>
                      <strong>Tahun Lulus:</strong> ' . htmlspecialchars($data['education']['graduation_year']) . '<br>';
        }
        
        $html .= '</div>
                </div>';

        // Work Experience Section
        $html .= '<div class="section">
                    <div class="section-title">Pengalaman Kerja</div>
                    <div class="section-content">';
        
        if (isset($data['experiences']) && !empty($data['experiences'])) {
            // Display detailed experience data from form
            foreach ($data['experiences'] as $experience) {
                $html .= '<div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-left: 4px solid #2563eb;">
                            <strong>' . htmlspecialchars($experience['job_title']) . '</strong><br>
                            <em>' . htmlspecialchars($experience['company']) . '</em><br>';
                
                if ($experience['start_date'] || $experience['end_date']) {
                    $html .= '<small style="color: #666;">';
                    if ($experience['start_date']) {
                        $html .= htmlspecialchars($experience['start_date']);
                    }
                    if ($experience['start_date'] && $experience['end_date']) {
                        $html .= ' - ';
                    }
                    if ($experience['end_date']) {
                        $html .= htmlspecialchars($experience['end_date']);
                    }
                    $html .= '</small><br>';
                }
                
                if ($experience['description']) {
                    $html .= '<div style="margin-top: 8px;">' . nl2br(htmlspecialchars($experience['description'])) . '</div>';
                }
                
                $html .= '</div>';
            }
        } else {
            // Display basic experience data
            $html .= htmlspecialchars($data['work_experience']['experience_detail']);
        }
        
        $html .= '</div>
                </div>';

        // Skills Section
        $html .= '<div class="section">
                    <div class="section-title">Keahlian</div>
                    <div class="section-content skills-list">';
        
        if (!empty($data['skills']) && $data['skills'] !== 'Belum ada keahlian yang dicantumkan') {
            $skills = is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', $data['skills']));
            $skills = array_filter($skills); // Remove empty values
            
            if (!empty($skills)) {
                foreach ($skills as $skill) {
                    $html .= '<span class="skill-tag">' . htmlspecialchars($skill) . '</span>';
                }
            } else {
                $html .= '<span class="skill-tag">' . htmlspecialchars($data['skills']) . '</span>';
            }
        } else {
            $html .= '<span class="skill-tag">' . htmlspecialchars($data['skills']) . '</span>';
        }
        
        $html .= '</div>
                </div>';

        // References Section
        if (isset($data['references_available']) && $data['references_available'] && isset($data['references']) && !empty($data['references'])) {
            $html .= '<div class="section">
                        <div class="section-title">Referensi</div>
                        <div class="section-content">';
            
            foreach ($data['references'] as $reference) {
                $html .= '<div style="margin-bottom: 10px;">
                            <strong>' . htmlspecialchars($reference['name']) . '</strong><br>
                            <em>' . htmlspecialchars($reference['position']) . '</em><br>
                            <small>Telepon: ' . htmlspecialchars($reference['phone']) . '</small>
                          </div>';
            }
            
            $html .= '</div>
                    </div>';
        }
        
        $html .= '<div class="footer">CV dibuat pada ' . htmlspecialchars($data['created_at']) . '</div>
            </div>
        </body>
        </html>';

        return $html;
    }
    
    private function getMonthName($month) 
    {
        $months = [
            '01' => 'Januari',
            '02' => 'Februari', 
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        
        return isset($months[$month]) ? $months[$month] : $month;
    }
}
