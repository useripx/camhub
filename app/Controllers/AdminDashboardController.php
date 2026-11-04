<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AdminRepository;

class AdminDashboardController
{
    private AdminRepository $adminRepo;

    public function __construct()
    {
        $this->adminRepo = new AdminRepository();
    }

    /**
     * Tampilkan halaman utama Dashboard Administrator
     * Presisi 100% sesuai template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Admin.png
     */
    public function index(): string
    {
        $metrics = $this->adminRepo->getDashboardMetrics();
        $chartData = $this->adminRepo->getLast7DaysChartData();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'chartData' => $chartData
        ]);
    }
}

