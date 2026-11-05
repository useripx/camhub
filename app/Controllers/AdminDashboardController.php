<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AdminRepository;
use App\Services\AdminService;
use App\Services\ReportService;

class AdminDashboardController
{
    private AdminRepository $adminRepo;
    private AdminService $adminService;
    private ReportService $reportService;

    public function __construct()
    {
        $this->adminRepo = new AdminRepository();
        $this->adminService = new AdminService();
        $this->reportService = new ReportService();
    }

    /**
     * Tampilkan halaman utama Dashboard Administrator
     * Presisi 100% sesuai template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Admin.png
     */
    public function index(): string
    {
        $metrics = $this->adminRepo->getDashboardMetrics();
        $chartData = $this->adminRepo->getLast7DaysChartData();

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'chartData' => $chartData,
            'activeMenu' => 'beranda',
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * MODUL 1: Verifikasi KYC Pengguna
     */
    public function kyc(): string
    {
        $status = $_GET['status'] ?? null;
        $submissions = $this->adminRepo->getAllKycSubmissions($status);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('admin.kyc', [
            'submissions' => $submissions,
            'currentFilter' => $status ?? 'all',
            'activeMenu' => 'kyc',
            'success' => $success,
            'error' => $error
        ]);
    }

    public function approveKyc(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $res = $this->adminService->approveKyc($id, $adminId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/kyc'));
    }

    public function rejectKyc(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $reason = $_POST['reason'] ?? 'Foto dokumen KTP / selfie tidak jelas atau buram.';
        $res = $this->adminService->rejectKyc($id, $adminId, $reason);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/kyc'));
    }

    /**
     * MODUL 2: Transaksi, Resolusi Sengketa, & Pencairan Saldo Mitra
     */
    public function transactions(): string
    {
        $transactions = $this->adminRepo->getAllTransactions();
        $disputes = $this->adminRepo->getDisputedEscrows();
        $withdrawals = $this->adminRepo->getAllWithdrawals();

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('admin.transactions', [
            'transactions' => $transactions,
            'disputes' => $disputes,
            'withdrawals' => $withdrawals,
            'activeMenu' => 'transaksi',
            'success' => $success,
            'error' => $error
        ]);
    }

    public function resolveDispute(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $resolution = $_POST['resolution'] ?? 'release';
        $notes = trim($_POST['notes'] ?? 'Keputusan resmi Super Admin');

        $res = $this->adminService->resolveDispute($id, $resolution, $adminId, $notes);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/transaksi'));
    }

    public function processWithdrawal(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $action = $_POST['action'] ?? 'processed';

        $res = $this->adminService->processWithdrawal($id, $action, $adminId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/transaksi'));
    }

    /**
     * MODUL 3: Pengawasan Jadwal Unit Kamera Lintas Mitra
     */
    public function schedule(): string
    {
        $schedules = $this->adminRepo->getAllUnitSchedules();

        return view('admin.schedule', [
            'schedules' => $schedules,
            'activeMenu' => 'jadwal'
        ]);
    }

    /**
     * MODUL 4: Manajemen Denda & Inspeksi
     */
    public function fines(): string
    {
        $fines = $this->adminRepo->getAllFines();
        $rentalBookings = $this->adminRepo->getRecentRentalBookings(50);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('admin.fines', [
            'fines' => $fines,
            'rentalBookings' => $rentalBookings,
            'activeMenu' => 'denda',
            'success' => $success,
            'error' => $error
        ]);
    }

    public function storeFine(): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $res = $this->adminService->storeFine($_POST, $adminId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/denda'));
    }

    public function payFine(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $res = $this->adminService->settleFine($id, $adminId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/denda'));
    }

    /**
     * MODUL 5: Pengaturan Platform, Pengguna, & Audit Trail Keamanan
     */
    public function settings(): string
    {
        $users = $this->adminRepo->getAllUsers();
        $auditLogs = $this->adminRepo->getRecentAuditLogs(50);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('admin.settings', [
            'users' => $users,
            'auditLogs' => $auditLogs,
            'activeMenu' => 'pengaturan',
            'success' => $success,
            'error' => $error
        ]);
    }

    public function toggleUserStatus(int $id): void
    {
        verify_csrf();
        $adminId = auth()['id'] ?? 1;
        $res = $this->adminService->toggleUserStatus($id, $adminId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/admin/pengaturan'));
    }

    /**
     * MODUL 6: Laporan Finansial & Operasional Platform
     */
    public function reports(): string
    {
        $period = $_GET['period'] ?? 'all';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $summary = $this->reportService->getFinancialSummary($period, $startDate, $endDate);
        $categoryBreakdown = $this->reportService->getCategoryBreakdown($period, $startDate, $endDate);
        $transactions = $this->reportService->getAllTransactionsForReport($period, $startDate, $endDate, 50);

        return view('admin.reports', [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $summary,
            'categoryBreakdown' => $categoryBreakdown,
            'transactions' => $transactions,
            'activeMenu' => 'laporan',
        ]);
    }

    public function exportReport(): void
    {
        $period = $_GET['period'] ?? 'all';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $csvContent = $this->reportService->generateCsvAdminReport($period, $startDate, $endDate);
        $filename = 'Laporan_Keuangan_CamHub_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $csvContent;
        exit;
    }
}
