<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{
    /**
     * Display a listing of bidang.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $search = $request->search;

        $bidangs = Bidang::query()
            ->withCount('users')
            ->when($search, function ($query, $find) {
                return $query->where('nama_bidang', 'LIKE', "%$find%");
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('pages.bidang.index', [
            'data' => $bidangs,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created bidang.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'nama_bidang' => 'required|string|max:255|unique:bidangs,nama_bidang',
            ], [
                'nama_bidang.required' => 'Nama bidang harus diisi',
                'nama_bidang.unique' => 'Nama bidang sudah ada',
                'nama_bidang.max' => 'Nama bidang maksimal 255 karakter',
            ]);

            Bidang::create($validated);

            return back()->with('success', 'Bidang berhasil ditambahkan');
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified bidang.
     *
     * @param Request $request
     * @param Bidang $bidang
     * @return RedirectResponse
     */
    public function update(Request $request, Bidang $bidang): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'nama_bidang' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('bidangs', 'nama_bidang')->ignore($bidang->id),
                ],
            ], [
                'nama_bidang.required' => 'Nama bidang harus diisi',
                'nama_bidang.unique' => 'Nama bidang sudah ada',
                'nama_bidang.max' => 'Nama bidang maksimal 255 karakter',
            ]);

            $bidang->update($validated);

            return back()->with('success', 'Bidang berhasil diperbarui');
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified bidang.
     *
     * @param Bidang $bidang
     * @return RedirectResponse
     */
    public function destroy(Bidang $bidang): RedirectResponse
    {
        try {
            // Cek apakah ada user yang terhubung dengan bidang ini
            if ($bidang->users()->count() > 0) {
                return back()->with('error', 'Bidang tidak dapat dihapus karena masih memiliki pengguna terkait');
            }

            $bidang->delete();

            return back()->with('success', 'Bidang berhasil dihapus');
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
