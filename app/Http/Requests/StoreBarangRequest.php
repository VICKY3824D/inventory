<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'ukuran' => ['required', 'string', 'max:10'],
            'stok' => ['required', 'integer', 'min:0'],
            'warna' => ['required', 'string'],
            'img' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'deskripsi' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'nama_barang.required' => 'Nama barang harus diisi',
            'nama_barang.string' => 'Nama barang harus berupa teks',
            'nama_barang.max' => 'Nama barang maksimal 255 karakter',
            'ukuran.required' => 'Ukuran harus diisi',
            'ukuran.string' => 'Ukuran harus berupa teks',
            'ukuran.max' => 'Ukuran maksimal 10 karakter',
            'stok.required' => 'Stok harus diisi',
            'stok.integer' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal 0',
            'warna.required' => 'Warna harus diisi',
            'img.required' => 'Gambar harus diunggah',
            'img.image' => 'File harus berupa gambar',
            'img.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'img.max' => 'Ukuran gambar maksimal 2MB',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'deskripsi.string' => 'Deskripsi harus berupa teks',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ];
    }
}
