<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BagianPetugasService;
use App\Services\CabangService;
use App\Services\DocumentService;
use App\Services\HakAksesPetugasService;
use App\Services\HakAksesService;
use App\Services\JabatanService;
use App\Services\PetugasService;
use App\Services\ProgramKerjaService;
use App\Services\UserService;
use Illuminate\Http\Request;

class PetugasController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PetugasService();
        $this->viewPrefix = 'app.master.petugas';
        $this->itemVariable = 'petugas';

        $cabangService = new CabangService();
        view()->share('cabang_options', $cabangService->dropdown());
        $jabatanService = new JabatanService();
        view()->share('jabatan_options', $jabatanService->dropdown());
        $bagianPetugasService = new BagianPetugasService();
        view()->share('bagian_options', $bagianPetugasService->dropdown());
        $programKerjaService = new ProgramKerjaService();
        view()->share('program_kerja_options', $programKerjaService->dropdown());
        $hakAksesService = new HakAksesService();
        view()->share('hak_akses_options', $hakAksesService->dropdown());
    }

    public function store(Request $request)
    {
        $filename = DocumentService::save_file($request, 'file_tanda_tangan', 'tanda_tangan');
        if ($filename !== '') $request->merge(['tanda_tangan' => $filename]);

        $request->merge(['role' => 'Admin', 'email' => $request->input('kode')]);
        $userService = new UserService();
        $user = $userService->store($request->all());
        $request->merge(['user_id' => $user->id]);

        $petugas = parent::store($request);

        $hakAksesPetugasService = new HakAksesPetugasService();
        $hakAksesPetugasService->store([
            'petugas_id' => $petugas->id,
            'hak_akses_id' => $request->input('hak_akses_id')
        ]);

        return $petugas;
    }

    public function update(Request $request, $id)
    {
        $filename = DocumentService::save_file($request, 'file_tanda_tangan', 'tanda_tangan');
        if ($filename !== '') $request->merge(['tanda_tangan' => $filename]);

        $petugas = parent::update($request, $id);

        $request->merge(['email' => $request->input('kode')]);
        $userService = new UserService();
        $userService->update($request->all(), $petugas->user_id);

        $hakAksesPetugasService = new HakAksesPetugasService();
        if (empty($petugas->hakAkses)) {
            $hakAksesPetugasService->store(['petugas_id' => $petugas->id, 'hak_akses_id' => $request->input('hak_akses_id')]);
        } else {
            $hakAksesPetugasService->update(['hak_akses_id' => $request->input('hak_akses_id')], $petugas->hakAkses->id);
        }

        return $petugas;
    }
}
