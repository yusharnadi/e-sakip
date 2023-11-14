<?php

namespace App\Http\Services;

use App\Models\Kinerja;

class KinerjaService implements KinerjaServiceInterface
{
    public function __construct(private Kinerja $model)
    {
    }

    public function getAll()
    {
        return $this->model->orderBy('tahun', 'desc')->get();
    }

    public function create($attributes)
    {
        try {
            return $this->model->create($attributes);
        } catch (\Throwable $th) {
            Log::error('KinerjaService@create Error', ['Message' => $th->getMessage()]);
        }
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function calculateBpspam(Kinerja $kinerja)
    {
        $roe_kondisi = 0;
        $roe_nilai = 0;
        $roe_bobot = 0;
        $rasio_operasi_kondisi = 0;
        $rasio_operasi_bobot = 0;
        $rasio_operasi_nilai = 0;
        $rasio_kas_kondisi = 0;
        $rasio_kas_bobot = 0;
        $rasio_kas_nilai = 0;
        $efektifitas_penagihan_kondisi = 0;
        $efektifitas_penagihan_bobot = 0;
        $efektifitas_penagihan_nilai = 0;
        $solvabilitas_kondisi = 0;
        $solvabilitas_nilai = 0;
        $solvabilitas_bobot = 0;

        $cakupan_pelayanan_kondisi = 0;
        $cakupan_pelayanan_nilai = 0;
        $cakupan_pelayanan_bobot = 0;
        $pertumbuhan_pelanggan_kondisi = 0;
        $pertumbuhan_pelanggan_nilai = 0;
        $pertumbuhan_pelanggan_bobot = 0;
        $penyelesaian_pengaduan_kondisi = 0;
        $penyelesaian_pengaduan_nilai = 0;
        $penyelesaian_pengaduan_bobot = 0;
        $kualitas_air_kondisi = 0;
        $kualitas_air_nilai = 0;
        $kualitas_air_bobot = 0;
        $air_domestik_kondisi = 0;
        $air_domestik_nilai = 0;
        $air_domestik_bobot = 0;

        $bobot_keuangan = 0;
        $bobot_pelayanan = 0;

        if (isset($kinerja) && $kinerja !== null) {

            if ($kinerja->equity !== 0) {
                $roe_kondisi = round(($kinerja->laba_bersih / $kinerja->equity) * 100, 2);
            }

            if ($roe_kondisi <= 0) {
                $roe_nilai = 1;
            } elseif ($roe_kondisi < 3) {
                $roe_nilai = 2;
            } elseif ($roe_kondisi < 7) {
                $roe_nilai = 3;
            } elseif ($roe_kondisi < 10) {
                $roe_nilai = 4;
            } else {
                $roe_nilai = 5;
            }

            $roe_bobot = 0.055 * $roe_nilai;

            // END ROE 

            $rasio_operasi_kondisi = ($kinerja->pendapatan_operasi !== 0) ? round($kinerja->biaya_operasi / $kinerja->pendapatan_operasi, 2) : 0;

            if ($rasio_operasi_kondisi > 1) {
                $rasio_operasi_nilai = 1;
            } elseif ($rasio_operasi_kondisi > 0.85) {
                $rasio_operasi_nilai = 2;
            } elseif ($rasio_operasi_kondisi > 0.65) {
                $rasio_operasi_nilai = 3;
            } elseif ($rasio_operasi_kondisi > 0.5) {
                $rasio_operasi_nilai = 4;
            } else {
                $rasio_operasi_nilai = 5;
            }

            $rasio_operasi_bobot = 0.055 * $rasio_operasi_nilai;

            // END RASIO OPERASI
            $rasio_kas_kondisi = ($kinerja->utang_lancar !== 0) ? round(($kinerja->kas / $kinerja->utang_lancar) * 100, 2) : 0;


            if ($rasio_kas_kondisi < 40) {
                $rasio_kas_nilai = 1;
            } elseif ($rasio_kas_kondisi < 60) {
                $rasio_kas_nilai = 2;
            } elseif ($rasio_kas_kondisi < 80) {
                $rasio_kas_nilai = 3;
            } elseif ($rasio_kas_kondisi < 100) {
                $rasio_kas_nilai = 4;
            } else {
                $rasio_kas_nilai = 5;
            }

            $rasio_kas_nilai = ($kinerja->utang_lancar === 0) ? 5 : $rasio_kas_nilai;

            $rasio_kas_bobot = 0.055 * $rasio_kas_nilai;

            // END RASIO KAS 

            $efektifitas_penagihan_kondisi = ($kinerja->rek_air !== 0) ? round(($kinerja->penerimaan_rek_air / $kinerja->rek_air) * 100, 2) : 0;

            if ($efektifitas_penagihan_kondisi < 75) {
                $efektifitas_penagihan_nilai = 1;
            } elseif ($efektifitas_penagihan_kondisi < 80) {
                $efektifitas_penagihan_nilai = 2;
            } elseif ($efektifitas_penagihan_kondisi < 85) {
                $efektifitas_penagihan_nilai = 3;
            } elseif ($efektifitas_penagihan_kondisi < 90) {
                $efektifitas_penagihan_nilai = 4;
            } else {
                $efektifitas_penagihan_nilai = 5;
            }

            $efektifitas_penagihan_bobot = 0.055 * $efektifitas_penagihan_nilai;

            // END EFEKTIFIRTAS PENAGIHAN 

            $solvabilitas_kondisi = ($kinerja->utang !== 0) ? round(($kinerja->aktiva / $kinerja->utang) * 100, 2) : 0;

            if ($solvabilitas_kondisi < 100) {
                $solvabilitas_nilai = 1;
            } elseif ($solvabilitas_kondisi < 135) {
                $solvabilitas_nilai = 2;
            } elseif ($solvabilitas_kondisi < 170) {
                $solvabilitas_nilai = 3;
            } elseif ($solvabilitas_kondisi < 200) {
                $solvabilitas_nilai = 4;
            } else {
                $solvabilitas_nilai = 5;
            }

            $solvabilitas_nilai = ($kinerja->utang === 0) ? 5 : $solvabilitas_nilai;

            $solvabilitas_bobot = 0.03 * $solvabilitas_nilai;

            // END SOLVABILITAS 

            $cakupan_pelayanan_kondisi = ($kinerja->penduduk_dalam_wilayah_Kerja_pdam !== 0) ? round(($kinerja->penduduk_terlayani / $kinerja->penduduk_dalam_wilayah_Kerja_pdam) * 100, 2) : 0;

            if ($cakupan_pelayanan_kondisi < 20) {
                $cakupan_pelayanan_nilai = 1;
            } elseif ($cakupan_pelayanan_kondisi < 40) {
                $cakupan_pelayanan_nilai = 2;
            } elseif ($cakupan_pelayanan_kondisi < 60) {
                $cakupan_pelayanan_nilai = 3;
            } elseif ($cakupan_pelayanan_kondisi < 80) {
                $cakupan_pelayanan_nilai = 4;
            } else {
                $cakupan_pelayanan_nilai = 5;
            }

            $cakupan_pelayanan_bobot = 0.05 * $cakupan_pelayanan_nilai;

            // END Cakupan Pelayanan

            $pertumbuhan_pelanggan_kondisi = ($kinerja->pelanggan_bulan_lalu !== 0) ? round((($kinerja->pelanggan_bulan_ini - $kinerja->pelanggan_bulan_lalu) / $kinerja->pelanggan_bulan_lalu) * 100, 2) : 0;

            if ($pertumbuhan_pelanggan_kondisi < 4) {
                $pertumbuhan_pelanggan_nilai = 1;
            } elseif ($pertumbuhan_pelanggan_kondisi < 6) {
                $pertumbuhan_pelanggan_nilai = 2;
            } elseif ($pertumbuhan_pelanggan_kondisi < 8) {
                $pertumbuhan_pelanggan_nilai = 3;
            } elseif ($pertumbuhan_pelanggan_kondisi < 10) {
                $pertumbuhan_pelanggan_nilai = 4;
            } else {
                $pertumbuhan_pelanggan_nilai = 5;
            }

            $pertumbuhan_pelanggan_bobot = 0.05 * $pertumbuhan_pelanggan_nilai;

            // END Pertunbuhan Pelanggan

            $penyelesaian_pengaduan_kondisi = ($kinerja->keluhan !== 0) ? round(($kinerja->keluhan_selesai / $kinerja->keluhan) * 100, 2) : 0;

            if ($penyelesaian_pengaduan_kondisi < 20) {
                $penyelesaian_pengaduan_nilai = 1;
            } elseif ($penyelesaian_pengaduan_kondisi < 40) {
                $penyelesaian_pengaduan_nilai = 2;
            } elseif ($penyelesaian_pengaduan_kondisi < 60) {
                $penyelesaian_pengaduan_nilai = 3;
            } elseif ($penyelesaian_pengaduan_kondisi < 80) {
                $penyelesaian_pengaduan_nilai = 4;
            } else {
                $penyelesaian_pengaduan_nilai = 5;
            }

            $penyelesaian_pengaduan_bobot = 0.025 * $penyelesaian_pengaduan_nilai;

            // END PENYELESAIN PENGADUAN

            $kualitas_air_kondisi = ($kinerja->jumlah_uji !== 0) ? round(($kinerja->uji_kualitas_memenuhi_syarat / $kinerja->jumlah_uji) * 100, 2) : 0;

            if ($kualitas_air_kondisi < 20) {
                $kualitas_air_nilai = 1;
            } elseif ($kualitas_air_kondisi < 40) {
                $kualitas_air_nilai = 2;
            } elseif ($kualitas_air_kondisi < 60) {
                $kualitas_air_nilai = 3;
            } elseif ($kualitas_air_kondisi < 80) {
                $kualitas_air_nilai = 4;
            } else {
                $kualitas_air_nilai = 5;
            }

            $kualitas_air_bobot = 0.075 * $kualitas_air_nilai;

            // END kualitas AIR

            $air_domestik_kondisi = ($kinerja->pelanggan_domestik !== 0) ? round($kinerja->air_terjual_domestik / $kinerja->pelanggan_domestik / 12, 2) : 0;

            if ($air_domestik_kondisi < 15) {
                $air_domestik_nilai = 1;
            } elseif ($air_domestik_kondisi < 20) {
                $air_domestik_nilai = 2;
            } elseif ($air_domestik_kondisi < 25) {
                $air_domestik_nilai = 3;
            } elseif ($air_domestik_kondisi < 30) {
                $air_domestik_nilai = 4;
            } else {
                $air_domestik_nilai = 5;
            }

            $air_domestik_bobot = 0.05 * $air_domestik_nilai;

            // END kualitas AIR

            $bobot_keuangan = round($roe_bobot + $rasio_kas_bobot + $rasio_operasi_bobot + $efektifitas_penagihan_bobot + $solvabilitas_bobot, 2);
            $bobot_pelayanan = round($cakupan_pelayanan_bobot + $pertumbuhan_pelanggan_bobot + $penyelesaian_pengaduan_bobot + $kualitas_air_bobot + $air_domestik_bobot, 2);
        }



        $result['roe_kondisi'] = $roe_kondisi;
        $result['roe_nilai'] = $roe_nilai;
        $result['roe_bobot'] = $roe_bobot;
        $result['rasio_operasi_kondisi'] = $rasio_operasi_kondisi;
        $result['rasio_operasi_nilai'] = $rasio_operasi_nilai;
        $result['rasio_operasi_bobot'] = $rasio_operasi_bobot;
        $result['rasio_kas_kondisi'] = $rasio_kas_kondisi;
        $result['rasio_kas_nilai'] = $rasio_kas_nilai;
        $result['rasio_kas_bobot'] = $rasio_kas_bobot;
        $result['efektifitas_penagihan_kondisi'] = $efektifitas_penagihan_kondisi;
        $result['efektifitas_penagihan_nilai'] = $efektifitas_penagihan_nilai;
        $result['efektifitas_penagihan_bobot'] = $efektifitas_penagihan_bobot;
        $result['solvabilitas_kondisi'] = $solvabilitas_kondisi;
        $result['solvabilitas_nilai'] = $solvabilitas_nilai;
        $result['solvabilitas_bobot'] = $solvabilitas_bobot;

        $result['cakupan_pelayanan_kondisi'] = $cakupan_pelayanan_kondisi;
        $result['cakupan_pelayanan_nilai'] = $cakupan_pelayanan_nilai;
        $result['cakupan_pelayanan_bobot'] = $cakupan_pelayanan_bobot;
        $result['pertumbuhan_pelanggan_kondisi'] = $pertumbuhan_pelanggan_kondisi;
        $result['pertumbuhan_pelanggan_nilai'] = $pertumbuhan_pelanggan_nilai;
        $result['pertumbuhan_pelanggan_bobot'] = $pertumbuhan_pelanggan_bobot;
        $result['penyelesaian_pengaduan_kondisi'] = $penyelesaian_pengaduan_kondisi;
        $result['penyelesaian_pengaduan_nilai'] = $penyelesaian_pengaduan_nilai;
        $result['penyelesaian_pengaduan_bobot'] = $penyelesaian_pengaduan_bobot;
        $result['kualitas_air_kondisi'] = $kualitas_air_kondisi;
        $result['kualitas_air_nilai'] = $kualitas_air_nilai;
        $result['kualitas_air_bobot'] = $kualitas_air_bobot;
        $result['air_domestik_kondisi'] = $air_domestik_kondisi;
        $result['air_domestik_nilai'] = $air_domestik_nilai;
        $result['air_domestik_bobot'] = $air_domestik_bobot;

        $result['bobot_keuangan'] = $bobot_keuangan;
        $result['bobot_pelayanan'] = $bobot_pelayanan;

        return $result;
    }
}
