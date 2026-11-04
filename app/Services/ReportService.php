<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    private ReportRepository $repo;

    public function __construct(?ReportRepository $repo = null)
    {
        $this->repo = $repo ?? new ReportRepository();
    }

    public function getFinancialSummary(string $period = 'all', ?string $start = null, ?string $end = null): array
    {
        return $this->repo->getFinancialSummary($period, $start, $end);
    }

    public function getCategoryBreakdown(string $period = 'all', ?string $start = null, ?string $end = null): array
    {
        return $this->repo->getCategoryBreakdown($period, $start, $end);
    }

    public function getAllTransactionsForReport(string $period = 'all', ?string $start = null, ?string $end = null, int $limit = 100): array
    {
        return $this->repo->getAllTransactionsForReport($period, $start, $end, $limit);
    }

    public function generateCsvAdminReport(string $period = 'all', ?string $start = null, ?string $end = null): string
    {
        $transactions = $this->repo->getAllTransactionsForReport($period, $start, $end, 1000);
        $summary = $this->repo->getFinancialSummary($period, $start, $end);

        $output = fopen('php://temp', 'r+');

        // BOM for UTF-8 in Excel
        fputs($output, "\xEF\xBB\xBF");

        // Title and Metadata
        fputcsv($output, ['LAPORAN TRANSAKSI & KEUANGAN PLATFORM CAMHUB'], ",", '"', "\\");
        fputcsv($output, ['Periode Filter', strtoupper($period)], ",", '"', "\\");
        fputcsv($output, ['Waktu Unduh', date('d-m-Y H:i:s')], ",", '"', "\\");
        fputcsv($output, ['Total Nilai Transaksi Gross (GTV)', 'Rp ' . number_format($summary['gross_turnover'], 0, ',', '.')], ",", '"', "\\");
        fputcsv($output, ['Total Pendapatan Bersih Platform', 'Rp ' . number_format($summary['platform_net_revenue'], 0, ',', '.')], ",", '"', "\\");
        fputcsv($output, [], ",", '"', "\\"); // Empty row

        // Table Header
        fputcsv($output, [
            'No',
            'Kode Transaksi',
            'Tanggal & Waktu',
            'Tipe Transaksi',
            'Nama Pelanggan',
            'Toko Mitra Penyedia',
            'Nama Barang / Kamera',
            'Nominal Transaksi (Rp)',
            'Fee Platform (Rp)',
            'Status Pembayaran',
            'Status Escrow'
        ], ",", '"', "\\");

        // Table Rows
        $no = 1;
        foreach ($transactions as $tx) {
            fputcsv($output, [
                $no++,
                $tx['code'],
                $tx['tx_date'],
                $tx['tx_type'],
                $tx['customer_name'],
                $tx['store_name'],
                $tx['item_name'],
                (float)$tx['amount'],
                (float)$tx['platform_fee'],
                strtoupper((string)$tx['payment_status']),
                strtoupper((string)$tx['escrow_status']),
            ], ",", '"', "\\");
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }

    public function generateCsvPartnerReport(int $partnerId): string
    {
        $transactions = $this->repo->getPartnerTransactionHistory($partnerId);

        $output = fopen('php://temp', 'r+');

        // BOM for UTF-8 in Excel
        fputs($output, "\xEF\xBB\xBF");

        // Title & Metadata
        fputcsv($output, ['REKAPITULASI SALDO & TRANSAKSI ESCROW MITRA LAPAK'], ",", '"', "\\");
        fputcsv($output, ['Waktu Unduh', date('d-m-Y H:i:s')], ",", '"', "\\");
        fputcsv($output, [], ",", '"', "\\"); // Empty row

        // Table Header
        fputcsv($output, [
            'No',
            'Kode Pesanan',
            'Jenis Transaksi',
            'Nominal Dana (Rp)',
            'Status Escrow',
            'Waktu Masuk Rekber',
            'Waktu Pelepasan Saldo'
        ], ",", '"', "\\");

        $no = 1;
        foreach ($transactions as $tx) {
            fputcsv($output, [
                $no++,
                $tx['code'],
                $tx['tx_type'],
                (float)$tx['amount'],
                strtoupper((string)$tx['escrow_status']),
                $tx['held_at'],
                $tx['released_at'] ?? '-'
            ], ",", '"', "\\");
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }
}
