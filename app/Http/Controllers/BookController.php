<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    private const UrlBooks = '/books';
    private function renderData()
    {
        if (!request()->user() || !request()->user()->can('view books')) {
            return Inertia::render('Errors/403', [
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman Buku',
                'url' => self::UrlBooks,
            ])->toResponse(request())->setStatusCode(403);
        }
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
        if (!request()->user() || !request()->user()->can('create books')) {
            return Inertia::render('Errors/403', [
                'message' => 'Anda tidak memiliki izin untuk menambahkan buku',
                'url' => self::UrlBooks,
            ])->toResponse(request())->setStatusCode(403);
        }

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
        if (!request()->user() || !request()->user()->can('update books')) {
            return Inertia::render('Errors/403', [
                'message' => 'Anda tidak memiliki izin untuk memperbarui buku',
                'url' => self::UrlBooks,
            ])->toResponse(request())->setStatusCode(403);
        }
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
        if (!request()->user() || !request()->user()->can('delete books')) {
            return Inertia::render('Errors/403', [
                'message' => 'Anda tidak memiliki izin untuk menghapus buku',
                'url' => self::UrlBooks,
            ])->toResponse(request())->setStatusCode(403);
        }
        $buku = Book::find($id);
        if (!$buku) {
            return back()->with('error', 'Buku tidak ditemukan!');
        }
        $buku->delete();
        return back()->with('success', 'Buku berhasil dihapus!');
    }
}
