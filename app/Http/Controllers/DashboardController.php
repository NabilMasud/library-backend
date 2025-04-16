<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Book::count();
        $totalStokKosong = Book::where('stok', 0)->count();
        $bukuTerbaru = Book::latest()->take(5)->get();
        $semingguTerakhir = Book::where('created_at', '>=', now()->subWeek())->count();

        $bukuPerKategori = Book::select('kategori', DB::raw('count(*) as total'))
        ->groupBy('kategori')
        ->get();

        $bukuStokSedikit = Book::orderBy('stok', 'asc')->take(5)->get();

        return Inertia::render('Dashboard', [
            'totalBuku' => $totalBuku,
            'stokKosong' => $totalStokKosong,
            'bukuTerbaru' => $bukuTerbaru,
            'semingguTerakhir' => $semingguTerakhir,
            'bukuPerKategori' => $bukuPerKategori,
            'bukuStokSedikit' => $bukuStokSedikit,
        ]);
    }
}
