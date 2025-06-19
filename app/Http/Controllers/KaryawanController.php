<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $karyawan = Karyawan::with('user')->paginate(15);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|unique:karyawan,email',
            'nik' => 'required|string|max:20|unique:karyawan,nik',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:LAKI_LAKI,PEREMPUAN',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'jabatan' => 'required|string|max:50',
            'divisi' => 'required|string|max:50',
            'tanggal_masuk' => 'required|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'no_rekening' => 'nullable|string|max:50',
            'nama_bank' => 'nullable|string|max:100',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'nama' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'peran' => 'KARYAWAN',
            'status_aktif' => true,
        ]);

        Karyawan::create([
            'user_id' => $user->id,
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'divisi' => $request->divisi,
            'tanggal_masuk' => $request->tanggal_masuk,
            'gaji_pokok' => $request->gaji_pokok,
            'no_rekening' => $request->no_rekening,
            'nama_bank' => $request->nama_bank,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'npwp' => $request->npwp,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load('user', 'tunjangan', 'absensi', 'penggajian');
        return view('karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $karyawan->user_id . '|unique:karyawan,email,' . $karyawan->id,
            'nik' => 'required|string|max:20|unique:karyawan,nik,' . $karyawan->id,
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:LAKI_LAKI,PEREMPUAN',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'jabatan' => 'required|string|max:50',
            'divisi' => 'required|string|max:50',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:AKTIF,TIDAK_AKTIF,CUTI',
            'gaji_pokok' => 'required|numeric|min:0',
            'no_rekening' => 'nullable|string|max:50',
            'nama_bank' => 'nullable|string|max:100',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
        ]);

        $karyawan->user->update([
            'nama' => $request->nama_lengkap,
            'email' => $request->email,
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->user->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus');
    }
}
