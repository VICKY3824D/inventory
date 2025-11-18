<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::with(['kategori', 'latestPrice']) // eager loading
            ->orderBy('nama_barang', 'desc')
            ->get();

        return view('admin.barang.index', compact('barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.barang.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBarangRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Handle upload gambar
            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Pastikan folder ada
                $uploadPath = public_path('img_item_upload');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $image->move($uploadPath, $imageName);
                $validated['img'] = $imageName;
            }

            // Tambahkan created_by jika ada auth
            if (auth()->check()) {
                $validated['created_by'] = auth()->id();
            }

            // Pisahkan price dari validated
            $price = $validated['price'];
            unset($validated['price']); // Hapus price karena tidak ada di tabel barangs

            // Simpan data barang
            $barang = Barang::create($validated);

            // Simpan harga ke tabel prices
            $barang->prices()->create([
                'harga' => $price
            ]);

            DB::commit();

            return redirect()->route('barangs.index')
                ->with('success', 'Barang berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus gambar jika sudah diupload
            if (isset($imageName)) {
                $imagePath = public_path('img_item_upload/' . $imageName);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            \Log::error('Error creating barang: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        return view('admin.barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        return view('admin.barang.edit', compact('barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBarangRequest $request, Barang $barang)
    {
        $validated = $request->validated();

        if ($request->hasFile('img')) {
            if ($barang->img && file_exists(public_path('img_item_upload/' . $barang->img))) {
                unlink(public_path('img_item_upload/' . $barang->img));
            }

            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img_item_upload'), $imageName);
            $validated['img'] = $imageName;
        }

        $barang->update($validated);

        if (isset($validated['price'])) {
            $barang->prices()->create(['harga' => $validated['price']]);
        }

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->img && file_exists(public_path('img_item_upload/' . $barang->img))) {
            unlink(public_path('img_item_upload/' . $barang->img));
        }

        $barang->delete();
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus');
    }
}
