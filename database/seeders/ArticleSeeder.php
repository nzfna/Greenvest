<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $general     = Category::where('slug', 'general')->first()->id;
        $greenBonds  = Category::where('slug', 'green-bonds')->first()->id;
        $energiSurya = Category::where('slug', 'energi-surya')->first()->id;
        $esg         = Category::where('slug', 'esg')->first()->id;

        $articles = [
            // General (3 artikel)
            [
                'title'       => 'Apa itu Greenvest?',
                'category_id' => $general,
                'description' => 'Greenvest adalah ekosistem edukasi investasi hijau yang dirancang untuk menjembatani kesenjangan antara kesadaran lingkungan dan keputusan finansial yang cerdas.',
                'content'     => '<p>Di tengah meningkatnya krisis iklim global, investasi tidak lagi sekadar mencari keuntungan finansial semata. <strong>Greenvest</strong> hadir sebagai jawaban bagi para investor modern yang ingin menyelaraskan pertumbuhan kekayaan mereka dengan keberlanjutan planet bumi.</p><h2>Misi Kami: Literasi untuk Keberlanjutan</h2><p>Misi utama Greenvest adalah memberikan akses edukasi yang transparan, berbasis data, dan mudah dipahami mengenai instrumen investasi berbasis ESG (Environmental, Social, and Governance). Kami menyediakan modul pembelajaran mulai dari dasar-dasar Green Bonds hingga analisis mendalam mengenai infrastruktur energi terbarukan.</p><blockquote><p><em>"Investasi hijau bukan lagi pilihan alternatif, melainkan keharusan strategis bagi masa depan ekonomi yang tangguh."</em></p></blockquote><h2>Mengapa Memilih Greenvest?</h2><ul><li><strong>Kurikulum Terstruktur:</strong> Materi edukasi yang disusun oleh pakar keuangan dan lingkungan.</li><li><strong>Simulasi Real-time:</strong> Fitur simulator untuk menghitung dampak lingkungan dari portofolio Anda.</li><li><strong>Komunitas Terverifikasi:</strong> Hubungkan diri Anda dengan ribuan investor hijau lainnya di seluruh Indonesia.</li></ul>',
                'cover_image' => 'images/greenvest.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Contoh Investasi Hijau',
                'category_id' => $general,
                'description' => 'Ada banyak contoh Investasi Hijau contohnya panel surya, turbin angin, dan proyek ESG berkelanjutan.',
                'content'     => '<p>Investasi hijau mencakup berbagai instrumen keuangan dan proyek yang berkontribusi pada lingkungan yang lebih baik.</p><h2>Panel Surya (Solar Energy)</h2><p>Investasi di perusahaan panel surya atau proyek PLTS (Pembangkit Listrik Tenaga Surya) adalah salah satu bentuk investasi hijau yang paling populer. Sektor ini tumbuh rata-rata 25% per tahun secara global.</p><h2>Turbin Angin (Wind Energy)</h2><p>Energi angin adalah sumber energi terbarukan yang tumbuh pesat, terutama di negara-negara dengan garis pantai panjang seperti Indonesia.</p><h2>Proyek ESG Berkelanjutan</h2><p>Banyak perusahaan besar kini menerbitkan obligasi hijau untuk mendanai proyek-proyek berkelanjutan, mulai dari pengelolaan air bersih hingga transportasi ramah lingkungan.</p>',
                'cover_image' => 'images/investasi-hijau.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Langkah Awal Menuju Kebebasan Finansial dan Lingkungan',
                'category_id' => $general,
                'description' => 'Panduan praktis bagi pemula untuk mulai membangun aset tanpa mengabaikan kelestarian bumi.',
                'content'     => '<p>Memulai perjalanan investasi tidak harus rumit. Dengan pemahaman dasar yang tepat, siapa pun bisa mulai berinvestasi secara bertanggung jawab.</p><h2>Langkah 1: Pahami Tujuan Finansial Anda</h2><p>Sebelum berinvestasi, tentukan tujuan finansial yang jelas. Apakah untuk dana pensiun, pendidikan anak, atau kebebasan finansial jangka panjang?</p><h2>Langkah 2: Kenali Profil Risiko Anda</h2><p>Setiap investor memiliki toleransi risiko yang berbeda. Greenvest membantu Anda memahami profil risiko dan memilih instrumen yang sesuai.</p><h2>Langkah 3: Mulai dengan Simulasi</h2><p>Gunakan fitur Simulasi Greenvest untuk melihat proyeksi hasil investasi Anda berdasarkan data historis yang akurat.</p>',
                'cover_image' => 'images/langkah-awal.jpg',
                'status'      => 'published',
            ],

            // Green Bonds (3 artikel)
            [
                'title'       => 'Mengenal Obligasi Hijau',
                'category_id' => $greenBonds,
                'description' => 'Pelajari bagaimana surat utang ini mendanai proyek yang bermanfaat bagi lingkungan dan planet.',
                'content'     => '<p><strong>Green Bonds</strong> atau Obligasi Hijau adalah instrumen utang yang diterbitkan khusus untuk mendanai proyek-proyek ramah lingkungan. Dana yang terkumpul dari penerbitan green bonds harus digunakan untuk proyek yang memberikan dampak positif bagi lingkungan.</p><h2>Karakteristik Green Bonds</h2><ul><li>Dana harus digunakan untuk proyek hijau yang terverifikasi</li><li>Pelaporan penggunaan dana yang transparan dan teratur</li><li>Sertifikasi dari lembaga internasional (CBI, GBP)</li></ul><h2>Jenis Proyek yang Didanai</h2><p>Green bonds dapat mendanai berbagai proyek, mulai dari pembangunan PLTS, proyek pengelolaan air, transportasi rendah emisi, hingga pembangunan gedung hijau bersertifikasi.</p>',
                'cover_image' => 'images/green-bonds.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Keuntungan Green Bonds bagi Investor',
                'category_id' => $greenBonds,
                'description' => 'Selain dampak positif, temukan potensi imbal hasil yang kompetitif bagi investor.',
                'content'     => '<p>Green Bonds tidak hanya memberikan dampak positif bagi lingkungan, tetapi juga menawarkan keuntungan finansial yang menarik bagi investor.</p><h2>Return Kompetitif</h2><p>Rata-rata imbal hasil Green Bonds korporasi Indonesia berkisar antara 7-9% per tahun, kompetitif dibandingkan obligasi konvensional.</p><h2>Diversifikasi Portofolio</h2><p>Menambahkan Green Bonds ke portofolio memberikan diversifikasi yang baik, karena korelasinya dengan aset lain cenderung rendah.</p><h2>Dampak Ganda</h2><p>Investor mendapatkan keuntungan finansial sekaligus berkontribusi pada keberlanjutan lingkungan — ini yang disebut "double dividend".</p>',
                'cover_image' => 'images/keuntungan-green-bonds.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Regulasi & Standar Green Bonds Global',
                'category_id' => $greenBonds,
                'description' => 'Memahami kerangka kerja global yang memastikan transparansi investasi hijau.',
                'content'     => '<p>Agar Green Bonds dapat dipercaya oleh investor global, diperlukan standar dan regulasi yang ketat dan terverifikasi.</p><h2>Green Bond Principles (GBP)</h2><p>Diterbitkan oleh ICMA (International Capital Market Association), GBP adalah panduan sukarela yang menjadi standar internasional utama untuk penerbitan green bonds.</p><h2>Climate Bonds Standard (CBS)</h2><p>Climate Bonds Initiative (CBI) mengembangkan standar yang lebih ketat, mencakup kriteria ilmiah untuk menentukan apakah sebuah proyek layak disebut "hijau".</p><h2>Regulasi di Indonesia</h2><p>OJK (Otoritas Jasa Keuangan) telah menerbitkan Peraturan OJK tentang penerbitan Efek Berwawasan Lingkungan, memastikan transparansi dan akuntabilitas di pasar domestik.</p>',
                'cover_image' => 'images/regulasi-green-bonds.jpg',
                'status'      => 'published',
            ],

            // Energi Surya (3 artikel)
            [
                'title'       => 'Masa Depan Panel Surya di Indonesia',
                'category_id' => $energiSurya,
                'description' => 'Bagaimana efisiensi fotovoltaik terus berkembang pesat tahun ini dan apa artinya bagi investor.',
                'content'     => '<p>Indonesia, sebagai negara tropis yang terletak di garis khatulistiwa, memiliki potensi energi surya yang luar biasa. Rata-rata Indonesia menerima sinar matahari 4-5 jam per hari secara konsisten sepanjang tahun.</p><h2>Perkembangan Teknologi</h2><p>Efisiensi panel surya terus meningkat dari waktu ke waktu. Panel surya konvensional kini mampu mengkonversi hingga 22% energi matahari, sementara teknologi perovskite menjanjikan efisiensi hingga 30%+.</p><h2>Target Pemerintah</h2><p>Pemerintah Indonesia menargetkan 23% bauran energi terbarukan pada 2025, dengan energi surya menjadi komponen utama. Hal ini membuka peluang investasi yang sangat besar.</p><h2>Peluang Investasi</h2><p>Investor dapat berpartisipasi melalui saham perusahaan energi surya, green bonds proyek PLTS, atau secara langsung melalui kepemilikan panel surya atap.</p>',
                'cover_image' => 'images/masa-depan-panel-surya.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Infrastruktur Surya Mikro untuk Komunitas',
                'category_id' => $energiSurya,
                'description' => 'Investasi pada jaringan listrik mandiri untuk komunitas lokal yang belum terjangkau PLN.',
                'content'     => '<p>Sistem surya mikro (micro-solar grid) adalah solusi elektrifikasi untuk komunitas terpencil yang belum terjangkau jaringan listrik konvensional PLN.</p><h2>Apa itu Surya Mikro?</h2><p>Surya mikro adalah sistem pembangkit listrik tenaga surya berskala kecil yang dapat melayani 50-500 rumah tangga dalam satu komunitas. Sistem ini otonom dan tidak memerlukan koneksi ke jaringan utama.</p><h2>Model Bisnis</h2><p>Investor dapat berpartisipasi melalui model sewa-bayar (PAYG), di mana komunitas membayar cicilan kecil setiap bulan untuk akses listrik. Ini menciptakan arus kas yang stabil bagi investor.</p><h2>Dampak Sosial</h2><p>Di luar keuntungan finansial, investasi surya mikro memberikan dampak sosial yang signifikan: akses listrik, peningkatan produktivitas, dan perbaikan kualitas hidup masyarakat terpencil.</p>',
                'cover_image' => 'images/infrastruktur-surya-mikro.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Solar vs Traditional: Perbandingan Performa Jangka Panjang',
                'category_id' => $energiSurya,
                'description' => 'Analisis mendalam perbandingan performa jangka panjang antara energi terbarukan dan fosil.',
                'content'     => '<p>Perdebatan antara energi surya dan energi fosil bukan lagi soal lingkungan semata — ini juga soal ekonomi dan ketahanan investasi jangka panjang.</p><h2>Biaya Produksi (LCOE)</h2><p>Levelized Cost of Energy (LCOE) energi surya telah turun 89% dalam satu dekade terakhir. Kini, energi surya adalah sumber listrik termurah di sejarah umat manusia di banyak wilayah dunia.</p><h2>Risiko Regulasi</h2><p>Investasi di energi fosil menghadapi risiko regulasi yang semakin besar seiring komitmen net-zero global. Sementara itu, energi surya justru didukung oleh kebijakan pemerintah di seluruh dunia.</p><h2>Proyeksi 2030</h2><p>Analis memperkirakan kapasitas energi surya global akan tumbuh 3x lipat pada 2030, menjadikannya investasi dengan pertumbuhan jangka panjang yang solid.</p>',
                'cover_image' => 'images/solar-vs-traditional.jpg',
                'status'      => 'published',
            ],

            // ESG (3 artikel)
            [
                'title'       => 'Apa itu Rating ESG?',
                'category_id' => $esg,
                'description' => 'Pahami bagaimana perusahaan dinilai berdasarkan dampak sosial dan lingkungan mereka.',
                'content'     => '<p>Rating ESG adalah penilaian komprehensif terhadap kinerja perusahaan dalam tiga dimensi utama: <strong>Environmental</strong> (lingkungan), <strong>Social</strong> (sosial), dan <strong>Governance</strong> (tata kelola).</p><h2>Komponen Penilaian</h2><h3>Environmental (E)</h3><p>Mencakup emisi karbon, pengelolaan limbah, efisiensi energi, dan dampak terhadap ekosistem. Perusahaan dengan nilai E tinggi adalah mereka yang aktif mengurangi jejak lingkungannya.</p><h3>Social (S)</h3><p>Meliputi kondisi tenaga kerja, hak asasi manusia, hubungan komunitas, dan keselamatan produk. Perusahaan dengan nilai S tinggi menunjukkan tanggung jawab sosial yang kuat.</p><h3>Governance (G)</h3><p>Mencakup transparansi manajemen, komposisi dewan direksi, kebijakan anti-korupsi, dan hak pemegang saham. Governance yang baik adalah fondasi kepercayaan investor.</p>',
                'cover_image' => 'images/rating-esg.jpg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Etika dalam Investasi: Pendekatan ESG',
                'category_id' => $esg,
                'description' => 'Mengapa tata kelola yang baik menjadi fondasi utama kesuksesan jangka panjang.',
                'content'     => '<p>Investasi berbasis ESG bukan hanya tentang moral — ini adalah strategi yang terbukti menghasilkan return yang lebih baik dalam jangka panjang.</p><h2>Bukti Empiris</h2><p>Studi dari Morgan Stanley Institute for Sustainable Investing menunjukkan bahwa reksa dana ESG mengungguli reksa dana konvensional selama periode volatilitas pasar yang tinggi.</p><h2>Menghindari Risiko Tersembunyi</h2><p>Perusahaan dengan praktik governance yang buruk cenderung menghadapi skandal, denda regulasi, dan kerusakan reputasi yang akhirnya merugikan pemegang saham.</p><h2>Tren Global</h2><p>Lebih dari $35 triliun aset dikelola dengan pendekatan ESG secara global pada 2022, dan angka ini terus bertumbuh seiring meningkatnya kesadaran investor akan keberlanjutan.</p>',
                'cover_image' => 'images/etika-investasi-esg.jpeg',
                'status'      => 'published',
            ],
            [
                'title'       => 'Strategi Portfolio ESG untuk Pemula',
                'category_id' => $esg,
                'description' => 'Cara membangun diversifikasi aset yang tetap berpegang pada nilai keberlanjutan.',
                'content'     => '<p>Membangun portofolio ESG yang efektif tidak harus rumit. Dengan strategi yang tepat, pemula pun bisa memulai perjalanan investasi yang bertanggung jawab.</p><h2>Prinsip Diversifikasi ESG</h2><p>Sama seperti portofolio konvensional, diversifikasi adalah kunci. Campurkan berbagai instrumen: reksa dana ESG, green bonds, dan saham perusahaan berperingkat ESG tinggi.</p><h2>Alokasi yang Disarankan untuk Pemula</h2><ul><li><strong>40% Green Bonds</strong> — Pendapatan tetap, risiko rendah</li><li><strong>35% Reksa Dana ESG</strong> — Pertumbuhan jangka menengah</li><li><strong>25% Saham Energi Terbarukan</strong> — Pertumbuhan jangka panjang</li></ul><h2>Mulai dari Nominal Kecil</h2><p>Banyak platform kini menawarkan investasi ESG mulai dari Rp 10.000. Konsistensi dan kesabaran adalah kunci sukses investasi jangka panjang.</p>',
                'cover_image' => 'images/strategi-portofolio-esg.jpeg',
                'status'      => 'published',
            ],
        ];

        foreach ($articles as $data) {
            $data['slug']         = Article::generateUniqueSlug($data['title']);
            $data['author_id']    = 1;
            $data['published_at'] = now()->subDays(rand(1, 30));

            Article::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}