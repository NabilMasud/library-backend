<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    private function renderData()
    {
        return Inertia::render('Books/Index', [
            'books' => Book::latest()->get()
        ]);
    }

    public function index()
    {
        // $books = Book::latest()->get();

        // return Inertia::render('Books/Index', [
        //     'books' => $books
        // ]);
        return $this->renderData();
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
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
            ]
        );
        // dd($validated);

        // if (!$validated) {
        //     return redirect()->back()->with('error', 'Buku gagal ditambahkan!');
        // }

        Book::create($validated);

        // return Inertia::render('Books/Index', [
        //     'books' => Book::latest()->get()
        // ])->with('success', 'Buku berhasil ditambahkan!');
        return $this->renderData()->with('success', 'Buku berhasil ditambahkan!');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate(
            [
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
            ]
        );
        // dd($validated);

        $buku = Book::find($id);

        $buku->update($validated);
        return redirect()->back()->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $buku = Book::find($id);
        $buku->delete();
        return redirect()->back()->with('success', 'Buku berhasil dihapus!');
    }
}
