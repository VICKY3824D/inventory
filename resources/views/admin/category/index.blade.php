@extends('admin.layouts.master')

@section('title', 'Daftar barang')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable.css') }}">
@endsection

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Barang</h3>
                <p class="text-subtitle text-muted">Daftar barang pada inventory toko baju</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <a href="{{ route('barangs.create') }}" class="btn btn-sm btn-primary float-end"><i
                        class="bi bi-plus fw-bold fs-5"></i> Tambah Barang</a>
            </div>
        </div>
    </div>
    <section class="section">

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Ukuran</th>
                            <th>Warna</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangs as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset('img_item_upload/'. $item->img) }}" style="width: 65px; height: 65px;" class="img-fluid rounded-2" alt="" onerror="this.onerror=null;this.src='{{ $item->img }}';">
                                </td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>
                                    @if($item->latestPrice)
                                        <i>Rp{{ number_format($item->latestPrice->harga, 0, ',', '.') }}</i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><b>{{ $item->ukuran }}</b></td>
                                <td>{{ $item->warna }}</td>
                                <td>{{ $item->stok }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td>
                                    {{ Str::limit($item->deskripsi, 50) }}
                                </td>
{{--                                <td>--}}
{{--                                    <span class="badge {{ $item->is_active == 1 ? 'bg-success' : 'text-danger' }}">--}}
{{--                                        {{ $item->is_active == 1 ? 'Tersedia' : 'Kosong' }}--}}
{{--                                    </span>--}}
{{--                                </td>--}}
                                <td>
                                    <a href="{{ route('barangs.edit', $item->id) }}" class="btn btn-sm btn-warning me-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('barangs.destroy', $item->id) }} " method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/static/js/pages/simple-datatables.js') }}"></script>
@endsection
