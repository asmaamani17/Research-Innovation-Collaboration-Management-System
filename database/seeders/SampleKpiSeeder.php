<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KpiStrategy;
use App\Models\KpiRecord;
use App\Models\KpiYear;
use App\Models\KpiPhase;

class SampleKpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing KPI data
        DB::table('kpi_phases')->delete();
        DB::table('kpi_years')->delete();
        DB::table('kpi_records')->delete();
        DB::table('kpi_strategies')->delete();

        // Create strategies
        $strategies = [
            [
                'strategy_code' => '2.1',
                'strategy_name' => 'MEMPERKUKUHKAN PENYELIDIKAN DAN PEMBANGUNAN BERIMPAK TINGGI',
                'display_order' => 1,
            ],
            [
                'strategy_code' => '2.2',
                'strategy_name' => 'MEMPERKASAKAN INOVASI UNTUK MANFAAT KOMUNITI DAN INDUSTRI',
                'display_order' => 2,
            ],
            [
                'strategy_code' => '2.3',
                'strategy_name' => 'TRANSFORMASI PENYELIDIKAN DAN INOVASI KEPADA PENGKOMERSIALAN & KEUSAHAWANAN',
                'display_order' => 3,
            ],
            [
                'strategy_code' => '2.4',
                'strategy_name' => 'MELESTARI PENJANAAN PENDAPATAN MELALUI PENYELIDIKAN DAN INOVASI',
                'display_order' => 4,
            ],
            [
                'strategy_code' => '2.5',
                'strategy_name' => 'MEMBUDAYAKAN PENYEBARAN ILMU BAGI KESEJAHTERAAN SOSIAL',
                'display_order' => 5,
            ],
        ];

        $createdStrategies = [];
        foreach ($strategies as $strategyData) {
            $strategy = KpiStrategy::create($strategyData);
            $createdStrategies[$strategy->strategy_code] = $strategy;
        }

        // Create KPI records with real data from CSV
        $kpiData = [
            // Strategy 2.1
            [
                'strategy_code' => '2.1',
                'kpi_code' => '2.1.3.6',
                'initiative' => 'Memperkasa Penerbitan Buku Ilmiah',
                'performance_indicator' => 'Bilangan laporan teknikal berimpak kepada komuniti dan industri (dengan/tanpa ISBN)',
                'action_plan' => "• Bekerjasama dengan RICE untuk menilai laporan buku teknikal yang boleh berada di pasaran\n• Penerima geran padanan yang telah tamat diwajibkan mengemukakan laporan teknikal",
                'years' => [
                    2026 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2027 => ['target' => '12', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '14', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '16', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '18', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            // Strategy 2.2
            [
                'strategy_code' => '2.2',
                'kpi_code' => '2.2.1.1',
                'initiative' => 'Membangunkan Potensi Harta Intelek',
                'performance_indicator' => 'Bilangan paten / utility innovation',
                'action_plan' => "1) 2 BENGKEL PATEN UI\n2) 6 PROSES PENILAIAN IP\n3) KPI FAKULTI = 1 IP (PATEN ATAU UI) / FAKULTI",
                'years' => [
                    2026 => ['target' => '8', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2027 => ['target' => '12', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '16', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '20', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '24', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.2',
                'kpi_code' => '2.2.1.2',
                'initiative' => '',
                'performance_indicator' => 'Bilangan lain-lain harta intelek (cap dagangan, hakcipta, reka bentuk perindustrian, perisian yang berdaftar dengan MyIPO atau badan antarabangsa yang setaraf)',
                'action_plan' => "1) 2 BENGKEL IP (SESI PEMANTAPAN ILMU)\n2) 6 PROSES PENILAIAN IP\n3) KPI FAKULTI = 8 IP (SELAIN PATEN ATAU UI) / FAKULTI",
                'years' => [
                    2026 => ['target' => '77', 'phase1' => '6', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '7.79', 'info' => "1) LAPLACE TRANSFORM (CRLY2026M00600)\n2) FIRST ORDER DIFFERENTIAL EQUATIONS (CRLY2026M00604)\n3) PARTIAL DIFFERENTIAL EQUATIONS (CRLY2026M00601)\n4) SECOND ORDER LINEAR DIFFERENTIAL EQUATIONS (CRLY2026M00602)\n5) FOURIER SERIE (CRLY2026M00603)\n6) FERMENTED PALM OIL SURFACTANT FOR LEAVE\n7) COATING (CRLY2026M01768)"],
                    2027 => ['target' => '77', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '77', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '77', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '77', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            // Strategy 2.3
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.1.1',
                'initiative' => 'Melestarikan UTeM Start Up Centre (USC)/Spin Off/Strategic Business Unit (SBU)',
                'performance_indicator' => 'Bilangan syarikat SBU baharu yang mengkomersialkan inovasi universiti',
                'action_plan' => "1) 5 SIRI PROGRAM ROUTE TO START-UP\n2) KPI FAKULTI = 1 SYARIKAT ATAU SBU / FAKULTI",
                'years' => [
                    2026 => ['target' => '2', 'phase1' => '1', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '50.00', 'info' => '• SBU (Loopwise Solution)'],
                    2027 => ['target' => '4', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '6', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '8', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.1.2',
                'initiative' => '',
                'performance_indicator' => 'Bilangan syarikat SBU aktif dalam pengkomersialan',
                'action_plan' => "1) PEMANTAUAN PRESTASI SBU / START UP / SPIN-OFF",
                'years' => [
                    2026 => ['target' => '3', 'phase1' => '5', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '100.00', 'info' => "• STARTUP:\n1) LESTANI SOLUTION ENTERPRISE\n2) KILATHOR LABORATORIES SDN. BHD.\n3) AMD ENGINEERING SERVICES SDN. BHD.\n\n• SPINOFF:\n1) INGENIOUSCITY ENGINEERING SOLUTIONS SDN. BHD.\n2) ERADA SOLUTION SDN. BHD."],
                    2027 => ['target' => '3', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '5', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '9', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '11', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.1.3',
                'initiative' => '',
                'performance_indicator' => 'Bilangan program berstrategik untuk pengkomersialan',
                'action_plan' => "1) KONSULTASI GERAN BERSAMA MRANTI, CRADLE, MTDC",
                'years' => [
                    2026 => ['target' => '5', 'phase1' => '2', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '40.00', 'info' => "1) BENGKEL IP & SEMAKAN COPYRIGHT BERSAMA MyIPO 2026\n2) CONNECTING INNOVATION: MRANTI & UTeM ENGAGEMENT SESSION"],
                    2027 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '15', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '20', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '25', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.1.4',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Penyertaan pameran & program pengkomersialan penyelidikan',
                'action_plan' => "1) PENYERTAAN KE ITEX, MCY, IPBM, MAHA, ETC.",
                'years' => [
                    2026 => ['target' => '5', 'phase1' => '1', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '20.00', 'info' => '1) MTE 2026  (26 Apr)'],
                    2027 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '15', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '20', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '25', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.2.1',
                'initiative' => 'Pemerkasaan Galeri Penyelidikan UTeM',
                'performance_indicator' => 'Bilangan program/aktiviti KSTP/pameran',
                'action_plan' => "1) PROGRAM BERSAMA INDUSTRI / KOMUNITI / SEKOLAH\n2) KPI FAKULTI = 8 PROGRAM / FAKULTI BERDAFTAR DI RICE",
                'years' => [
                    2026 => ['target' => '60', 'phase1' => '18', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '30.00', 'info' => "1) BENGKEL IP & SEMAKAN COPYRIGHT BERSAMA MyIPO 2026\n2) CONNECTING INNOVATION: MRANTI & UTeM ENGAGEMENT SESSION\n\n• Unit Komuniti\n1) Program Latihan Transforming Productivity & Leadership with Generative AI kepada Staf Lembaga Tabung Haji\n2) Program Ekonomi MADANI di Kem Angkatan Tentera Malaysia (ATM) Fasa Kedua – Aplikasi Teknologi Terkini dalam Penanaman Cendawan Tiram)\n \n• Unit STEM\n1) Program STEM Pemindahan Kepakaran Kejuruteraan Staf FTKM kepada Pelajar SMK Munshi Abdullah, Melaka\n2) Program Jelajah UTeM STEM Centre - SMK Datuk Bendahara\n \n• Unit Projek Negeri Melaka\n1) Program Kesedaran AI Rakyat Digital kepada belia DUN Pengkalan Batu (2 Jan)\n2) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Taboh Naning (21 Jan)\n3) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Tanjong Bidara (24 Jan)\n4) Program Kesedaran AI Rakyat Digital kepada belia DUN Sungai Udang (24 Jan)\n5) Program Kesedaran AI Rakyat Digital kepada belia DUN Telok Mas (27 Jan)\n6) Program Kesedaran AI Rakyat Digital kepada belia DUN Duyong (29 Jan)\n7) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Gadek (31 Jan)\n8) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Ayer Limau (31 Jan)\n9) Program Kesedaran AI Rakyat Digital kepada belia DUN Kesidang (26 Feb)\n10) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Klebang (26 Feb)\n11) Program Kesedaran AI Rakyat Digital kepada belia DUN Rim (27 Feb)\n12) ⁠Program Kesedaran AI Rakyat Digital kepada belia DUN Lendu (3 Mac)"],
                    2027 => ['target' => '120', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '180', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '240', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '300', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.2.2',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Produk baharu dikomersialkan dalam pasaran',
                'action_plan' => "1) UTeMTECH PITCH\n2) KPI FAKULTI = 1 PRODUK BARU / FAKULTI",
                'years' => [
                    2026 => ['target' => '4', 'phase1' => '3', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '75.00', 'info' => "1) Kilat 3000\n2) Baja Microbes-Straw\n3) Baja kompos dan lilin aroma terapi"],
                    2027 => ['target' => '6', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '8', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '12', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.2.3',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Produk konsisten dikomersialkan (3 tahun)',
                'action_plan' => "1) AKTIVITI KOMERSIALAN UTeM DI BAWAH RICE\n2) KPI FAKULTI = 1 PRODUK / FAKULTI",
                'years' => [
                    2026 => ['target' => '4', 'phase1' => '7', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '100.00', 'info' => "1) Intelligent Fire Alarm System\n2) Intelligent Soil and Environment Monitoring System\n3) Operations Management In Edutourism\n4) Suci-Uris\n5) Kilat 3000\n6) Baja Microbes-Straw\n7) Baja kompos dan lilin aroma terapi"],
                    2027 => ['target' => '6', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '8', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '12', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.3',
                'kpi_code' => '2.3.2.4',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Teknologi know-how yang dilesenkan',
                'action_plan' => "1) AKTIVITI KOMERSIALAN UTeM DI BAWAH RICE\n2) KPI FAKULTI = 1 PRODUK / FAKULTI",
                'years' => [
                    2026 => ['target' => '5', 'phase1' => '4', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '80.00', 'info' => "1) Intelligent Fire Alarm System\n2) Intelligent Soil and Environment Monitoring System\n3) Operations Management In Edutourism\n4) iFOS"],
                    2027 => ['target' => '7', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '9', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '11', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '13', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            // Strategy 2.4
            [
                'strategy_code' => '2.4',
                'kpi_code' => '2.4.1.5',
                'initiative' => 'Memperhebat Ekosistem Mampan Industri dan Komuniti',
                'performance_indicator' => 'Jumlah Hasil dari teknologi know-how/jualan terus & pengkomersialan',
                'action_plan' => "1) AKTIVITI KOMERSIALAN UTeM DI BAWAH RICE",
                'years' => [
                    2026 => ['target' => 'RM 150,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2027 => ['target' => 'RM 250,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => 'RM 350,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => 'RM 450,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => 'RM 550,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.4',
                'kpi_code' => '2.4.3.1',
                'initiative' => 'Memperkasa Rangkaian Perundingan Institusi',
                'performance_indicator' => 'Jumlah Dana projek perundingan',
                'action_plan' => "1) MENAMBAHBAIK EKOSISTEM PERUNDINGAN",
                'years' => [
                    2026 => ['target' => 'RM 5,000,000', 'phase1' => 'RM 2,970,352.14', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '59.41', 'info' => "• Risiko Rendah (31)  = RM606,233.88\n• Risiko Tinggi (34) = RM2,364,119.26\n• RM2,970,352.14"],
                    2027 => ['target' => 'RM 8,000,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => 'RM 11,000,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => 'RM 14,000,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => 'RM 17,000,000', 'phase1' => 'RM 0', 'phase2' => 'RM 0', 'phase3' => 'RM 0', 'phase4' => 'RM 0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            // Strategy 2.5
            [
                'strategy_code' => '2.5',
                'kpi_code' => '2.5.1.4',
                'initiative' => 'Memacu Komitmen Untuk Keunggulan dan Keterlihatan Global',
                'performance_indicator' => 'Bilangan MoA/MoU peringkat antarabangsa',
                'action_plan' => "1) KPI FAKULTI = 1 MoA atau MoU / FAKULTI",
                'years' => [
                    2026 => ['target' => '5', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => "• Beberapa deraf bersih MoU Antarabangsa telah didaftarkan di PUU:\n1) MEMORANDUM OF UNDERSTANDING BETWEEN UTeM AND SINGAPORE POLYTECHNIC\n2) MEMORANDUM OF UNDERSTANDING BETWEEN UTeM AND BUKHARA STATE TECHNICAL UNIVERSITY\n3) MEMORANDUM OF UNDERSTANDING BETWEEN UTeM AND REVA UNIVERSITY"],
                    2027 => ['target' => '10', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '15', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '20', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '25', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.5',
                'kpi_code' => '2.5.1.5',
                'initiative' => '',
                'performance_indicator' => 'Bilangan MoA peringkat kebangsaan',
                'action_plan' => "1) KPI FAKULTI = 3 MoA / FAKULTI",
                'years' => [
                    2026 => ['target' => '20', 'phase1' => '1', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '5.00', 'info' => "1) Impressive Edge Sdn) Bhd) [Ts) Dr) Syahrul Azwan Bin Sundi @ Suandi-FTKIP]\n \n•  Beberapa deraf bersih MoA peringkat kebangsaan telah didaftarkan di PUU: \n1) MEMORANDUM OF AGREEMENT BETWEEN UTeM AND KURNIA AL RIZQ ENTERPRISE\n2) MEMORANDUM OF AGREEMENT BETWEEN UTeM AND UNIVERSITI TEKNOLOGI MARA\n3) MEMORANDUM OF AGREEMENT BETWEEN UTeM AND SAWIPAC SDN) BHD)"],
                    2027 => ['target' => '40', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '60', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '80', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '100', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.5',
                'kpi_code' => '2.5.1.6',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Projek komuniti baharu',
                'action_plan' => "1) KPI FAKULTI = 2 PROJEK BAHARU / FAKULTI (DANA / TANPA DANA)",
                'years' => [
                    2026 => ['target' => '15', 'phase1' => '16', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '100.00', 'info' => "• Unit Komuniti\n1) Program Latihan Transforming Productivity & Leadership with Generative AI kepada Staf Lembaga Tabung Haji\n2) Program Ekonomi MADANI di Kem Angkatan Tentera Malaysia (ATM) Fasa Kedua – Aplikasi Teknologi Terkini dalam Penanaman Cendawan Tiram)\n \n• Unit STEM\n1) Program STEM Pemindahan Kepakaran Kejuruteraan Staf FTKM kepada Pelajar SMK Munshi Abdullah, Melaka\n2) Program Jelajah UTeM STEM Centre - SMK Datuk Bendahara\n \n• Unit Projek Negeri Melaka\n1) Program Kesedaran AI Rakyat Digital kepada belia DUN Pengkalan Batu (2 Jan)\n2) Program Kesedaran AI Rakyat Digital kepada belia DUN Taboh Naning (21 Jan)\n3) Program Kesedaran AI Rakyat Digital kepada belia DUN Tanjong Bidara (24 Jan)\n4) Program Kesedaran AI Rakyat Digital kepada belia DUN Sungai Udang (24 Jan)\n5) Program Kesedaran AI Rakyat Digital kepada belia DUN Telok Mas (27 Jan) \n6) Program Kesedaran AI Rakyat Digital kepada belia DUN Duyong (29 Jan)\n7) Program Kesedaran AI Rakyat Digital kepada belia DUN Gadek (31 Jan)\n8) Program Kesedaran AI Rakyat Digital kepada belia DUN Ayer Limau (31 Jan)\n9) Program Kesedaran AI Rakyat Digital kepada belia DUN Kesidang (26 Feb)\n10) Program Kesedaran AI Rakyat Digital kepada belia DUN Klebang (26 Feb)\n11) Program Kesedaran AI Rakyat Digital kepada belia DUN Rim (27 Feb)\n12) Program Kesedaran AI Rakyat Digital kepada belia DUN Lendu (3 Mac)"],
                    2027 => ['target' => '30', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '45', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '60', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '75', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
            [
                'strategy_code' => '2.5',
                'kpi_code' => '2.5.1.7',
                'initiative' => '',
                'performance_indicator' => 'Bilangan Komuniti menerima faedah dari projek komuniti',
                'action_plan' => "1) KPI FAKULTI = 9 PROJEK / FAKULTI (KSTP/CSR) YANG BERDAFTAR DI RICE",
                'years' => [
                    2026 => ['target' => '70', 'phase1' => '16', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '22.86', 'info' => "• Unit Komuniti\n1) Staf Lembaga Tabung Haji wilayah selatan (Johor, Negeri Sembilan dan Melaka)\n2) Komuniti Badan Kebajikan Keluarga Angkatan Tentera (BAKAT),Kem 96 DPP, Kem Masjid Tanah, Melaka\n \n• Unit STEM\n1) Murid sekolah SMK Munshi Abdullah\n2) Murid sekolah SMK Datuk Bendahara\n \n• Unit Projek Negeri Melaka\n1) Belia DUN Pengkalan Batu (2 Jan)\n2) Belia DUN Taboh Naning (21 Jan)\n3) Belia DUN Tanjong Bidara (24 Jan)\n4) Belia DUN Sungai Udang (24 Jan)\n5) Belia DUN Telok Mas (27 Jan)\n6) Belia DUN Duyong (29 Jan)\n7) Belia DUN Gadek (31 Jan)\n8) Belia DUN Ayer Limau (31 Jan)\n9) Belia DUN Kesidang (26 Feb)\n10) Belia DUN Klebang (26 Feb)\n11) Belia DUN Rim (27 Feb)\n12) Belia DUN Lendu (3 Mac)"],
                    2027 => ['target' => '70', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2028 => ['target' => '70', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2029 => ['target' => '70', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                    2030 => ['target' => '70', 'phase1' => '0', 'phase2' => '0', 'phase3' => '0', 'phase4' => '0', 'percentage' => '0.00', 'info' => '• Tiada'],
                ],
            ],
        ];

        foreach ($kpiData as $kpiItem) {
            $strategy = $createdStrategies[$kpiItem['strategy_code']];
            
            $kpiRecord = KpiRecord::create([
                'strategy_id' => $strategy->id,
                'kpi_code' => $kpiItem['kpi_code'],
                'initiative' => $kpiItem['initiative'],
                'performance_indicator' => $kpiItem['performance_indicator'],
                'action_plan' => $kpiItem['action_plan'],
            ]);

            // Create year records for each KPI
            foreach ($kpiItem['years'] as $year => $yearData) {
                $kpiYear = KpiYear::create([
                    'kpi_id' => $kpiRecord->id,
                    'target_year' => $year,
                    'target_value' => $yearData['target'],
                    'achievement_percentage' => $yearData['percentage'],
                    'achievement_information' => $yearData['info'],
                ]);

                // Create phase records
                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 1',
                    'achievement' => $yearData['phase1'],
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 2',
                    'achievement' => $yearData['phase2'],
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 3',
                    'achievement' => $yearData['phase3'],
                ]);

                KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => 'Phase 4',
                    'achievement' => $yearData['phase4'],
                ]);
            }
        }

        $this->command->info('Sample KPI data seeded successfully.');
    }
}

