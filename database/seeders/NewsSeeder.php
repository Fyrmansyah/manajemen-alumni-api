<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for author_id
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->info('No admin user found. Please run AdminSeeder first.');
            return;
        }

        $newsData = [
            [
                'title' => 'Pembukaan Bursa Kerja Khusus SMKN 1 Surabaya 2025',
                'content' => '<p>SMKN 1 Surabaya dengan bangga mengumumkan pembukaan Bursa Kerja Khusus (BKK) untuk tahun 2025. Program ini bertujuan untuk memfasilitasi para lulusan dalam mendapatkan pekerjaan yang sesuai dengan kompetensi yang telah dipelajari selama menempuh pendidikan di sekolah.</p>

<p>BKK SMKN 1 Surabaya telah menjalin kerjasama dengan berbagai perusahaan dan instansi, baik di tingkat lokal maupun nasional, untuk menyediakan lowongan pekerjaan yang berkualitas bagi para alumni.</p>

<p>Para alumni diharapkan dapat memanfaatkan fasilitas ini dengan optimal untuk mengembangkan karir profesional mereka.</p>',
                'slug' => 'pembukaan-bursa-kerja-khusus-smkn-1-surabaya-2025',
                'status' => 'published',
                'is_featured' => true,
                'category' => 'Pengumuman',
                'author_id' => $admin->id,
                'published_at' => now(),
                'views' => 150,
            ],
            [
                'title' => 'Tips Sukses Menghadapi Interview Kerja',
                'content' => '<p>Menghadapi interview kerja merupakan salah satu tahapan penting dalam mencari pekerjaan. Berikut adalah beberapa tips yang dapat membantu para alumni dalam menghadapi interview:</p>

<h3>1. Persiapan Sebelum Interview</h3>
<ul>
<li>Pelajari profil perusahaan</li>
<li>Siapkan pertanyaan yang akan diajukan</li>
<li>Latihan menjawab pertanyaan umum interview</li>
</ul>

<h3>2. Penampilan dan Sikap</h3>
<ul>
<li>Berpakaian rapi dan profesional</li>
<li>Datang tepat waktu</li>
<li>Tunjukkan sikap yang percaya diri</li>
</ul>

<h3>3. Saat Interview</h3>
<ul>
<li>Dengarkan pertanyaan dengan baik</li>
<li>Jawab dengan jelas dan jujur</li>
<li>Tunjukkan antusiasme terhadap posisi yang dilamar</li>
</ul>',
                'slug' => 'tips-sukses-menghadapi-interview-kerja',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Tips Karir',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(2),
                'views' => 89,
            ],
            [
                'title' => 'Pelatihan Soft Skills untuk Alumni',
                'content' => '<p>SMKN 1 Surabaya akan mengadakan pelatihan soft skills untuk para alumni yang ingin meningkatkan kemampuan non-teknis mereka. Pelatihan ini akan meliputi:</p>

<h3>Materi Pelatihan:</h3>
<ul>
<li>Komunikasi Efektif</li>
<li>Teamwork dan Kolaborasi</li>
<li>Leadership dan Manajemen Waktu</li>
<li>Problem Solving</li>
<li>Adaptabilitas</li>
</ul>

<h3>Waktu dan Tempat:</h3>
<p><strong>Tanggal:</strong> 25-27 Juli 2025<br>
<strong>Waktu:</strong> 08.00 - 16.00 WIB<br>
<strong>Tempat:</strong> Aula SMKN 1 Surabaya</p>

<p>Pendaftaran dapat dilakukan melalui website BKK atau datang langsung ke kantor BKK SMKN 1 Surabaya.</p>',
                'slug' => 'pelatihan-soft-skills-untuk-alumni',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Pelatihan',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(5),
                'views' => 67,
            ],
            [
                'title' => 'Kerjasama dengan PT. Tech Innovation Indonesia',
                'content' => '<p>BKK SMKN 1 Surabaya telah menandatangani kerjasama dengan PT. Tech Innovation Indonesia untuk menyediakan lowongan pekerjaan bagi lulusan jurusan Teknik Komputer dan Jaringan serta Multimedia.</p>

<p>PT. Tech Innovation Indonesia merupakan perusahaan teknologi yang bergerak di bidang pengembangan software dan aplikasi mobile. Perusahaan ini menawarkan berbagai posisi menarik seperti:</p>

<ul>
<li>Junior Programmer</li>
<li>UI/UX Designer</li>
<li>Network Administrator</li>
<li>Quality Assurance</li>
<li>Technical Support</li>
</ul>

<p>Para alumni yang berminat dapat mengirimkan lamaran melalui sistem BKK online atau datang langsung ke kantor BKK untuk mendapatkan informasi lebih lanjut.</p>',
                'slug' => 'kerjasama-dengan-pt-tech-innovation-indonesia',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Kerjasama',
                'author_id' => $admin->id,
                'published_at' => now()->subWeek(),
                'views' => 234,
            ],
            [
                'title' => 'Success Story: Alumni TKJ Berkarir di Perusahaan Multinasional',
                'content' => '<p>Kami bangga berbagi kisah sukses salah satu alumni jurusan Teknik Komputer dan Jaringan (TKJ) angkatan 2022, Budi Santoso, yang kini berkarir sebagai Network Engineer di sebuah perusahaan multinasional.</p>

<h3>Perjalanan Karir Budi</h3>
<p>Setelah lulus dari SMKN 1 Surabaya, Budi langsung mendapatkan pekerjaan sebagai IT Support di sebuah perusahaan lokal melalui program BKK. Dengan dedikasi dan kerja keras, dalam waktu 2 tahun ia berhasil naik jabatan menjadi Junior Network Administrator.</p>

<p>Tidak puas dengan pencapaiannya, Budi terus mengembangkan kemampuannya dengan mengikuti berbagai sertifikasi IT seperti CCNA dan CompTIA Network+. Usaha kerasnya membuahkan hasil ketika ia diterima bekerja di perusahaan multinasional dengan gaji yang sangat kompetitif.</p>

<h3>Pesan untuk Adik-Adik Alumni</h3>
<p>"Jangan pernah berhenti belajar dan selalu manfaatkan fasilitas BKK yang tersedia. Networking dan pengembangan skill adalah kunci sukses di dunia kerja," pesan Budi untuk adik-adik alumninya.</p>',
                'slug' => 'success-story-alumni-tkj-berkarir-di-perusahaan-multinasional',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Success Story',
                'author_id' => $admin->id,
                'published_at' => now()->subWeeks(2),
                'views' => 156,
            ],
        ];

        foreach ($newsData as $news) {
            News::create($news);
        }

        $this->command->info('News seeded successfully!');
    }
}
