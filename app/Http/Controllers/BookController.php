<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::latest()->get();

        return Inertia::render('Books/Index', [
            'books' => $books
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|string|max:4',
            'kategori' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0|max:1000',
        ],
        [
            'judul.required' => 'Judul buku harus diisi.',
            'pengarang.required' => 'Pengarang buku harus diisi.',
            'penerbit.required' => 'Penerbit buku harus diisi.',
            'tahun_terbit.required' => 'Tahun terbit buku harus diisi.',
            'stok.required' => 'Stok buku harus diisi.',
            'stok.integer' => 'Stok buku harus berupa angka.',
            'stok.min' => 'Stok buku tidak boleh kurang dari 0.',
            'stok.max' => 'Stok buku tidak boleh lebih dari 1000.',
        ]);
        // dd($validated);

        // if (!$validated) {
        //     return redirect()->back()->with('error', 'Buku gagal ditambahkan!');
        // }

        Book::create($validated);

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan!');
    }
}
