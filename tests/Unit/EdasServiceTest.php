<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\EdasService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class EdasServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    private const DELTA = 0.0001;

    private EdasService $service;

    private const ALT = [
        'A1' => 1, // Prepared Statements
        'A2' => 2, // WAF Deployment
        'A3' => 3, // Input Validation Library
        'A4' => 4, // Database Firewall
        'A5' => 5, // ORM Strict Mode
    ];

    private const CRIT = [
        'C1' => 1, // Efektivitas (benefit, w=0.35)
        'C2' => 2, // Biaya (cost,    w=0.25)
        'C3' => 3, // Kompleksitas (cost, w=0.15)
        'C4' => 4, // Kecepatan (cost,    w=0.10)
        'C5' => 5, // Kepatuhan (benefit, w=0.15)
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EdasService();
    }

     private function makeCriteria(): Collection
    {
        $data = [
            ['id' => 1, 'name' => 'Efektivitas',   'type' => 'benefit', 'weight' => 0.35],
            ['id' => 2, 'name' => 'Biaya',          'type' => 'cost',    'weight' => 0.25],
            ['id' => 3, 'name' => 'Kompleksitas',   'type' => 'cost',    'weight' => 0.15],
            ['id' => 4, 'name' => 'Kecepatan',      'type' => 'cost',    'weight' => 0.10],
            ['id' => 5, 'name' => 'Kepatuhan',      'type' => 'benefit', 'weight' => 0.15],
        ];

        return collect(array_map(function ($d) {
            $obj         = new \stdClass();
            $obj->id     = $d['id'];
            $obj->name   = $d['name'];
            $obj->type   = $d['type'];
            $obj->weight = $d['weight'];
            // Simulasi method isBenefit() dari model Criteria
            $obj->isBenefit = fn() => $obj->type === 'benefit';
            return $obj;
        }, $data));
    }

    private function makeMatrix(): array
    {
        // $matrix[$altId][$critId] = value
        return [
            1 => [1 => 90.0, 2 => 20.0,  3 => 30.0, 4 =>  7.0, 5 => 85.0], // A1
            2 => [1 => 75.0, 2 => 80.0,  3 => 50.0, 4 => 30.0, 5 => 70.0], // A2
            3 => [1 => 80.0, 2 => 35.0,  3 => 25.0, 4 => 14.0, 5 => 80.0], // A3
            4 => [1 => 85.0, 2 => 120.0, 3 => 70.0, 4 => 60.0, 5 => 90.0], // A4
            5 => [1 => 70.0, 2 => 15.0,  3 => 20.0, 4 =>  5.0, 5 => 65.0], // A5
        ];
    }

     #[Test]
    public function langkah2_average_solution_dihitung_benar(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $av     = $result['av'];

        // AV_C1 = (90+75+80+85+70)/5 = 400/5 = 80.0
        $this->assertEqualsWithDelta(80.0, $av[1], self::DELTA, 'AV C1 (Efektivitas)');

        // AV_C2 = (20+80+35+120+15)/5 = 270/5 = 54.0
        $this->assertEqualsWithDelta(54.0, $av[2], self::DELTA, 'AV C2 (Biaya)');

        // AV_C3 = (30+50+25+70+20)/5 = 195/5 = 39.0
        $this->assertEqualsWithDelta(39.0, $av[3], self::DELTA, 'AV C3 (Kompleksitas)');

        // AV_C4 = (7+30+14+60+5)/5 = 116/5 = 23.2
        $this->assertEqualsWithDelta(23.2, $av[4], self::DELTA, 'AV C4 (Kecepatan)');

        // AV_C5 = (85+70+80+90+65)/5 = 390/5 = 78.0
        $this->assertEqualsWithDelta(78.0, $av[5], self::DELTA, 'AV C5 (Kepatuhan)');
    }

    #[Test]
    public function langkah3_pda_benefit_dihitung_benar(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $pda    = $result['pda'];

        // A1, C1 benefit: PDA = max(0, 90−80)/80 = 10/80 = 0.1250
        $this->assertEqualsWithDelta(0.1250, $pda[1][1], self::DELTA, 'PDA A1-C1');

        // A2, C1 benefit: PDA = max(0, 75−80)/80 = max(0, −5)/80 = 0
        $this->assertEqualsWithDelta(0.0000, $pda[2][1], self::DELTA, 'PDA A2-C1 (di bawah AV)');

        // A4, C5 benefit: PDA = max(0, 90−78)/78 = 12/78 ≈ 0.1538
        $this->assertEqualsWithDelta(0.1538, $pda[4][5], self::DELTA, 'PDA A4-C5');

        // A5, C5 benefit: PDA = max(0, 65−78)/78 = 0 (di bawah AV)
        $this->assertEqualsWithDelta(0.0000, $pda[5][5], self::DELTA, 'PDA A5-C5 (di bawah AV)');
    }

    #[Test]
    public function langkah3_pda_cost_dihitung_benar(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $pda    = $result['pda'];

        // A1, C2 cost: PDA = max(0, 54−20)/54 = 34/54 ≈ 0.6296
        $this->assertEqualsWithDelta(0.6296, $pda[1][2], self::DELTA, 'PDA A1-C2 (cost, di bawah AV)');

        // A2, C2 cost: PDA = max(0, 54−80)/54 = max(0, −26)/54 = 0 (di atas AV)
        $this->assertEqualsWithDelta(0.0000, $pda[2][2], self::DELTA, 'PDA A2-C2 (cost, di atas AV)');

        // A5, C2 cost: PDA = max(0, 54−15)/54 = 39/54 ≈ 0.7222
        $this->assertEqualsWithDelta(0.7222, $pda[5][2], self::DELTA, 'PDA A5-C2 (cost, sangat rendah)');
    }

    #[Test]
    public function langkah3_nda_benefit_dihitung_benar(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $nda    = $result['nda'];

        // A1, C1 benefit: NDA = max(0, 80−90)/80 = 0 (di atas AV)
        $this->assertEqualsWithDelta(0.0000, $nda[1][1], self::DELTA, 'NDA A1-C1');

        // A2, C1 benefit: NDA = max(0, 80−75)/80 = 5/80 = 0.0625
        $this->assertEqualsWithDelta(0.0625, $nda[2][1], self::DELTA, 'NDA A2-C1');

        // A5, C1 benefit: NDA = max(0, 80−70)/80 = 10/80 = 0.1250
        $this->assertEqualsWithDelta(0.1250, $nda[5][1], self::DELTA, 'NDA A5-C1');
    }

    #[Test]
    public function langkah3_nda_cost_dihitung_benar(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $nda    = $result['nda'];

        // A1, C2 cost: NDA = max(0, 20−54)/54 = 0 (sudah PDA)
        $this->assertEqualsWithDelta(0.0000, $nda[1][2], self::DELTA, 'NDA A1-C2');

        // A2, C2 cost: NDA = max(0, 80−54)/54 = 26/54 ≈ 0.4815
        $this->assertEqualsWithDelta(0.4815, $nda[2][2], self::DELTA, 'NDA A2-C2');

        // A4, C2 cost: NDA = max(0, 120−54)/54 = 66/54 ≈ 1.2222
        $this->assertEqualsWithDelta(1.2222, $nda[4][2], self::DELTA, 'NDA A4-C2');
    }

    #[Test]
    public function langkah4_sp_positif_atau_nol(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        // SP tidak boleh negatif (karena PDA selalu ≥ 0 dan weight > 0)
        foreach ($result['sp'] as $altId => $sp) {
            $this->assertGreaterThanOrEqual(0.0, $sp, "SP alternatif #{$altId} tidak boleh negatif");
        }
    }

    #[Test]
    public function langkah4_sn_positif_atau_nol(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        foreach ($result['sn'] as $altId => $sn) {
            $this->assertGreaterThanOrEqual(0.0, $sn, "SN alternatif #{$altId} tidak boleh negatif");
        }
    }

    #[Test]
    public function langkah4_alternatif_terbaik_memiliki_sp_tertinggi(): void
    {
        // A1 harus punya SP tertinggi — paling banyak di atas rata-rata
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $sp     = $result['sp'];
        $maxSp  = max($sp);

        $this->assertEqualsWithDelta($sp[1], $maxSp, self::DELTA,
            'A1 (Prepared Statements) harus memiliki SP tertinggi');
    }

    #[Test]
    public function langkah4_alternatif_terburuk_memiliki_sn_tertinggi(): void
    {
        // A2 harus punya SN tertinggi — paling jauh di bawah rata-rata
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $sn     = $result['sn'];
        $maxSn  = max($sn);

        $this->assertEqualsWithDelta($sn[2], $maxSn, self::DELTA,
            'A2 (WAF Deployment) harus memiliki SN tertinggi');
    }

     #[Test]
    public function langkah5_nsp_range_0_sampai_1(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        foreach ($result['nsp'] as $altId => $nsp) {
            $this->assertGreaterThanOrEqual(0.0, $nsp, "NSP #{$altId} < 0");
            $this->assertLessThanOrEqual(1.0, $nsp,    "NSP #{$altId} > 1");
        }
    }

    #[Test]
    public function langkah5_nsn_range_0_sampai_1(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        foreach ($result['nsn'] as $altId => $nsn) {
            $this->assertGreaterThanOrEqual(0.0, $nsn, "NSN #{$altId} < 0");
            $this->assertLessThanOrEqual(1.0, $nsn,    "NSN #{$altId} > 1");
        }
    }

    #[Test]
    public function langkah5_nsp_tertinggi_adalah_1(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $this->assertEqualsWithDelta(1.0, max($result['nsp']), self::DELTA,
            'NSP tertinggi harus = 1.0');
    }

    #[Test]
    public function langkah5_nsp_a1_sama_dengan_1(): void
    {
        // A1 punya SP tertinggi → NSP_A1 = SP_A1/max(SP) = 1.0
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $this->assertEqualsWithDelta(1.0, $result['nsp'][1], self::DELTA,
            'NSP A1 harus = 1.0 (A1 memiliki SP tertinggi)');
    }

    #[Test]
    public function langkah5_nsn_a3_sama_dengan_1(): void
    {
        // A3 harus punya SN = 0 (tidak ada jarak negatif signifikan)
        // → NSN_A3 = 1 − (0/max(SN)) = 1.0
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        // Cari alternatif dengan SN terkecil (≈0) — harus NSN = 1.0
        $minSn    = min($result['sn']);
        $minAltId = array_search($minSn, $result['sn']);
        $this->assertEqualsWithDelta(1.0, $result['nsn'][$minAltId], self::DELTA,
            'Alternatif dengan SN=0 harus NSN=1.0');
    }

    #[Test]
    public function langkah6_as_range_0_sampai_1(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        foreach ($result['ranked'] as $altId => $entry) {
            $as = $entry['as'];
            $this->assertGreaterThanOrEqual(0.0, $as, "AS #{$altId} < 0");
            $this->assertLessThanOrEqual(1.0,   $as, "AS #{$altId} > 1");
        }
    }

    #[Test]
    public function langkah6_a1_rank_1_sesuai_laporan(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        // Sesuai laporan: A1 (Prepared Statements) AS = 0.9286, Rank 1
        $this->assertEquals(1, $result['ranked'][1]['rank'],
            'A1 (Prepared Statements) harus rank 1');
    }

    #[Test]
    public function langkah6_a2_rank_5_sesuai_laporan(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        // Sesuai laporan: A2 (WAF Deployment) AS = 0.1295, Rank 5
        $this->assertEquals(5, $result['ranked'][2]['rank'],
            'A2 (WAF Deployment) harus rank 5');
    }

    #[Test]
    public function langkah6_a3_rank_2_sesuai_laporan(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());

        // Sesuai laporan: A3 (Input Validation Library) AS = 0.8516, Rank 2
        $this->assertEquals(2, $result['ranked'][3]['rank'],
            'A3 (Input Validation Library) harus rank 2');
    }

    #[Test]
    public function langkah6_as_a1_tertinggi(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $scores = array_column($result['ranked'], 'as');
        $maxAs  = max($scores);

        $this->assertEqualsWithDelta($maxAs, $result['ranked'][1]['as'], self::DELTA,
            'AS A1 harus tertinggi dari semua alternatif');
    }

    #[Test]
    public function langkah6_as_a2_terendah(): void
    {
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $scores = array_column($result['ranked'], 'as');
        $minAs  = min($scores);

        $this->assertEqualsWithDelta($minAs, $result['ranked'][2]['as'], self::DELTA,
            'AS A2 harus terendah dari semua alternatif');
    }

    #[Test]
    public function langkah6_as_a1_mendekati_nilai_laporan(): void
    {
        // Laporan menyatakan AS_A1 = 0.9286
        // Toleransi 0.05 karena nilai SP/SN di laporan sudah dibulatkan
        $result = $this->service->calculateRaw($this->makeMatrix(), $this->makeCriteria());
        $this->assertEqualsWithDelta(0.9286, $result['ranked'][1]['as'], 0.05,
            'AS A1 harus mendekati 0.9286 sesuai laporan');
    }

    #[Test]
    public function edge_case_dua_alternatif_identik(): void
    {
        // Jika semua nilai sama, AV = x, PDA = NDA = 0 untuk semua
        // SP = SN = 0, max(SP) = 0, NSP = 0, NSN = 1, AS = 0.5 untuk semua
        $matrix = [
            1 => [1 => 80.0, 2 => 50.0, 3 => 40.0, 4 => 20.0, 5 => 70.0],
            2 => [1 => 80.0, 2 => 50.0, 3 => 40.0, 4 => 20.0, 5 => 70.0],
        ];
        $result = $this->service->calculateRaw($matrix, $this->makeCriteria());

        // Kedua alternatif harus punya AS = 0.5 (NSP=0, NSN=1)
        foreach ($result['ranked'] as $altId => $entry) {
            $this->assertEqualsWithDelta(0.5, $entry['as'], self::DELTA,
                "Alternatif identik harus AS = 0.5, got {$entry['as']}");
        }
    }

    #[Test]
    public function edge_case_satu_alternatif_jauh_lebih_baik(): void
    {
        // A_terbaik = nilai sempurna di semua kriteria
        // A_buruk   = nilai jelek di semua kriteria
        $matrix = [
            1 => [1 => 100.0, 2 => 1.0,   3 => 1.0,   4 => 1.0,   5 => 100.0], // sangat baik
            2 => [1 => 1.0,   2 => 100.0, 3 => 100.0, 4 => 100.0, 5 => 1.0  ], // sangat buruk
            3 => [1 => 50.0,  2 => 50.0,  3 => 50.0,  4 => 50.0,  5 => 50.0  ], // rata-rata
        ];
        $result = $this->service->calculateRaw($matrix, $this->makeCriteria());

        // Alternatif 1 harus rank 1
        $this->assertEquals(1, $result['ranked'][1]['rank'], 'Alternatif terbaik harus rank 1');
        // Alternatif 2 harus rank terakhir (3)
        $this->assertEquals(3, $result['ranked'][2]['rank'], 'Alternatif terburuk harus rank 3');
        // AS alternatif 1 lebih tinggi dari alternatif 2
        $this->assertGreaterThan($result['ranked'][2]['as'], $result['ranked'][1]['as']);
    }

    #[Test]
    public function edge_case_kriteria_tunggal_benefit(): void
    {
        // Hanya 1 kriteria benefit — alternatif dengan nilai tertinggi harus rank 1
        $singleCrit = collect([[
            'id' => 1, 'name' => 'Efektivitas', 'type' => 'benefit', 'weight' => 1.0,
            'isBenefit' => fn() => true,
        ]])->map(function ($d) {
            $obj = (object) $d;
            return $obj;
        });

        $matrix = [
            1 => [1 => 90.0],
            2 => [1 => 60.0],
            3 => [1 => 75.0],
        ];

        $result = $this->service->calculateRaw($matrix, $singleCrit);

        // Alternatif 1 (nilai 90) harus rank 1
        $this->assertEquals(1, $result['ranked'][1]['rank']);
        // Alternatif 2 (nilai 60) harus rank 3
        $this->assertEquals(3, $result['ranked'][2]['rank']);
    }

}
