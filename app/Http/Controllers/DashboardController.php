<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isCEO()) {
            return $this->ceoDashboard();
        } elseif ($user->isHRD()) {
            return $this->hrdDashboard();
        } else {
            return $this->karyawanDashboard();
        }
    }

    private function ceoDashboard()
    {
        $totalKaryawan = Karyawan::where('status', 'AKTIF')->count();
        $totalPenggajianBulanIni = Penggajian::where('periode_gaji', Carbon::now()->format('Y-m'))
            ->sum('gaji_bersih');
        
        $laporanAbsensi = Absensi::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard.ceo', compact('totalKaryawan', 'totalPenggajianBulanIni', 'laporanAbsensi'));
    }

    private function hrdDashboard()
    {
        $totalKaryawan = Karyawan::count();
        $karyawanAktif = Karyawan::where('status', 'AKTIF')->count();
        $absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())->count();
        
        $recentAbsensi = Absensi::with('karyawan')
            ->whereDate('tanggal', Carbon::today())
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.hrd', compact('totalKaryawan', 'karyawanAktif', 'absensiHariIni', 'recentAbsensi'));
    }

    private function karyawanDashboard()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan');
        }

        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        $totalKehadiranBulanIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->where('status', 'HADIR')
            ->count();

        $slipGajiBulanIni = Penggajian::where('karyawan_id', $karyawan->id)
            ->where('periode_gaji', Carbon::now()->format('Y-m'))
            ->first();

        return view('dashboard.karyawan', compact('karyawan', 'absensiHariIni', 'totalKehadiranBulanIni', 'slipGajiBulanIni'));
    }
}
