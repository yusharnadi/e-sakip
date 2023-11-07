<?php

namespace App\Http\Controllers;

use App\Http\Services\KinerjaServiceInterface;
use Illuminate\Http\Request;

class KinerjaController extends Controller
{

    public function __construct(private KinerjaServiceInterface $kinerjaService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $kinerja = $this->kinerjaService->getAll();
        $kinerja = [
            ['id' => 1, 'tahun' => 2022, 'periode' => 'Tahunan'],
            ['id' => 2, 'tahun' => 2021, 'periode' => 'Tahunan'],
            ['id' => 3, 'tahun' => 2021, 'periode' => 'Triwulan I'],
            ['id' => 3, 'tahun' => 2021, 'periode' => 'Triwulan II'],
            ['id' => 3, 'tahun' => 2021, 'periode' => 'Triwulan III'],
        ];
        return view('kinerja.index', ['kinerjas' => $kinerja]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
