<?php

namespace App\Http\Controllers;

use App\Http\Services\KinerjaServiceInterface;
use Illuminate\Http\Request;

class MonevPdamController extends Controller
{
    public function __construct(private KinerjaServiceInterface $kinerjaService)
    {
    }

    public function index()
    {
        $kinerjas = $this->kinerjaService->getAll();
        return view('monev.pdam.index', ['kinerjas' => $kinerjas]);
    }

    public function detail(int $id)
    {
        $kinerja = $this->kinerjaService->getById($id);
        // dd($this->kinerjaService->calculateBpspam($kinerja));
        // $penilaian = $this->kinerjaService->calculateBpspam($kinerja);
        $penilaian = calculateBpspam($kinerja);
        dd($penilaian);
        return view('monev.pdam.detail', ['kinerja' => $kinerja, 'penilaian' => $penilaian]);
    }
}
