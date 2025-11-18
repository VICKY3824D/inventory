<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(){
        $jumlah_karyawan = User::count();
        $jumlah_barang = Barang::count();
        $jumlah_order = Order::count();

        $total_harga_hari_ini = Order::whereDate('created_at', Carbon::today())
            ->sum('total_harga');

        return view('admin.dashboard', compact([
            'jumlah_karyawan',
            'jumlah_barang',
            'jumlah_order',
            'total_harga_hari_ini']));
    }
}
