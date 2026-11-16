<?php

namespace App\Controllers;

use App\Models\GlosariumModel;

class Glosarium extends BaseController
{
    public function index()
    {
        $model = new GlosariumModel();

        $keyword = $this->request->getGet('keyword');
        $kategori = $this->request->getGet('kategori');

        if (!empty($keyword)) {
            $model->groupStart()
                ->like('istilah', $keyword)
                ->orLike('varian', $keyword)
                ->orLike('makna_indonesia', $keyword)
                ->groupEnd();
        }

        if (!empty($kategori)) {
            $model->where('kategori', $kategori);
        }

        $data = [
            'title' => 'Glosarium Usada Bali',
            'active' => 'glosarium',
            'glosarium' => $model->orderBy('istilah', 'ASC')->findAll(),
            'kategori' => (new GlosariumModel())->kategori(),
            'keyword' => $keyword,
            'kategoriDipilih' => $kategori,
        ];

        return view('glosarium', $data);
    }
}
