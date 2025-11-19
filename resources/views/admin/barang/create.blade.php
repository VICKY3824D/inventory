@extends('admin.layouts.master')
@section('title', 'Tambah Barang')

@section('content')

    <div class="page-title mt-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Data barang</h3>
                <p class="text-subtitle text-muted">Silahkan isi data barang yang ingin ditambahkan</p>
            </div>
        </div>
    </div>

    <div class="card">
            <div class="card-body">
                <form class="form" action="{{ route('barangs.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="nama_barang">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" id="nama_barang"
                                           placeholder="Masukkan nama barang" value="{{ old('nama_barang') }}" required>
                                    @error('nama_barang')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="deskripsi">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3"
                                              placeholder="Masukkan deskripsi barang"
                                              required>{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="price">Harga</label>
                                    <input type="number" name="price" class="form-control" id="price"
                                           placeholder="Masukkan harga menu" value="{{ old('price') }}" required>
                                    @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="category_id">Kategori</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option
                                                value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="img">Gambar Barang</label>
                                    <input type="file" name="img" class="form-control" id="img" accept="image/*"
                                           required>
                                    <span class="text-muted text-sm">File harus berupa gambar (jpeg/png/jpg/gif/svg) dengan ukuran maksimal 2MB</span>
                                    @error('img')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="ukuran">Ukuran</label>
                                    <input type="text" name="ukuran" class="form-control" id="ukuran"
                                           placeholder="Masukkan ukuran barang" value="{{ old('ukuran') }}" required>
                                    @error('ukuran')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="warna">Warna</label>
                                    <input type="text" name="warna" class="form-control" id="warna"
                                           placeholder="Masukkan ukuran barang" value="{{ old('warna') }}" required>
                                    @error('warna')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="mb-2 mt-2" for="stok">Stok</label>
                                    <input type="number" name="stok" class="form-control" id="stok"
                                           placeholder="Masukkan jumlah stok" value="{{ old('stok') }}" required>
                                    @error('stok')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary rounded-2 me-2 mb-1 mt-2">Simpan</button>
                                    <button type="reset" class="btn btn-danger rounded-2 me-2 mb-1 mt-2">Reset</button>
                                    <a href="{{ route('barangs.index') }}" type="submit"
                                       class="btn btn-light-secondary rounded-2 me-2 mb-1 mt-2">Batal</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

@endsection
