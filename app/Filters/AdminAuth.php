<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Periksa status login Admin.
        if (!session()->get('admin_logged_in')) {
            return redirect()
                ->to(site_url('admin/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Tidak ada proses tambahan setelah request.
    }
}