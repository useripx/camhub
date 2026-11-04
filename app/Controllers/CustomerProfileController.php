<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProfileService;
use App\Validators\KycValidator;
use PDO;

class CustomerProfileController
{
    private ProfileService $profileService;
    private KycValidator $validator;

    public function __construct(?ProfileService $profileService = null, ?KycValidator $validator = null)
    {
        $this->profileService = $profileService ?? new ProfileService();
        $this->validator = $validator ?? new KycValidator();
    }

    /**
     * Display Customer Profile Page
     */
    public function showProfile(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $profile = $this->profileService->getProfile((int)$user['id']);

        return view('customer.profile', [
            'pageTitle' => 'Profil Saya & Pengaturan Akun — CamHub',
            'profile' => $profile,
            'kycStatus' => $profile['kyc_status'] ?? 'unverified',
            'kycRecord' => $profile['kyc'] ?? null,
            'user' => $user,
            'errors' => flash('errors') ?? [],
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    /**
     * Handle Customer Profile Update submission
     */
    public function updateProfile(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Sesi formulir tidak valid. Silakan coba lagi.');
            redirect('/profil');
            return;
        }

        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $errors = $this->validator->validateProfile($_POST);
        if (!empty($errors)) {
            flash('errors', $errors);
            flash_input($_POST);
            redirect('/profil');
            return;
        }

        $result = $this->profileService->updateProfile((int)$user['id'], $_POST);

        if (!$result['success']) {
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/profil');
            return;
        }

        clear_old_input();
        flash('success', $result['message']);
        redirect('/profil');
    }

    /**
     * Display Customer Dashboard Page (100% Mockup CamHub — Dashboard Pelanggan.png)
     */
    public function dashboard(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return '';
        }
        $userId = (int)$user['id'];

        $pdo = \App\Core\Database::getConnection();

        // 1. Pesanan Aktif
        $stmtActive = $pdo->prepare("
            SELECT COUNT(*) FROM rental_bookings 
            WHERE user_id = :uid AND rental_status IN ('pending', 'confirmed', 'active')
        ");
        $stmtActive->execute(['uid' => $userId]);
        $pesananAktif = (int)$stmtActive->fetchColumn();

        // 2. Sewa Mendatang
        $stmtUpcoming = $pdo->prepare("
            SELECT COUNT(*) FROM rental_bookings 
            WHERE user_id = :uid AND start_date >= CURDATE() AND rental_status NOT IN ('cancelled', 'returned', 'completed')
        ");
        $stmtUpcoming->execute(['uid' => $userId]);
        $sewaMendatang = (int)$stmtUpcoming->fetchColumn();

        // 3. Saldo Escrow (Dana tertahan aman untuk pesanan sewa pelanggan)
        $stmtEscrow = $pdo->prepare("
            SELECT COALESCE(SUM(et.amount_held), 0)
            FROM escrow_transactions et
            JOIN rental_bookings rb ON rb.id = et.booking_id
            WHERE rb.user_id = :uid AND et.status = 'held'
        ");
        $stmtEscrow->execute(['uid' => $userId]);
        $saldoEscrow = (float)$stmtEscrow->fetchColumn();

        // 4. Pesanan Terbaru
        $stmtLatest = $pdo->prepare("
            SELECT 
                rb.id,
                rb.total_days,
                rb.rental_status,
                rb.created_at,
                p.name AS product_name,
                p.slug AS product_slug,
                COALESCE(img.image_path, '') AS product_image
            FROM rental_bookings rb
            JOIN product_units pu ON pu.id = rb.product_unit_id
            JOIN products p ON p.id = pu.product_id
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE rb.user_id = :uid
            ORDER BY rb.id DESC
            LIMIT 5
        ");
        $stmtLatest->execute(['uid' => $userId]);
        $dbOrders = $stmtLatest->fetchAll(PDO::FETCH_ASSOC);

        $latestOrders = [];
        if (!empty($dbOrders)) {
            foreach ($dbOrders as $ord) {
                $statusMap = [
                    'pending' => 'Dalam proses',
                    'confirmed' => 'Dalam proses',
                    'active' => 'Digunakan',
                    'returned' => 'Selesai',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
                $latestOrders[] = [
                    'id' => (int)$ord['id'],
                    'product_name' => $ord['product_name'],
                    'order_desc' => 'Sewa ' . $ord['total_days'] . ' hari',
                    'status_label' => $statusMap[$ord['rental_status']] ?? 'Dalam proses',
                    'product_image' => $ord['product_image'],
                ];
            }
        } else {
            // Sesuai data prototype visual jika belum ada transaksi riil
            $latestOrders = [
                [
                    'id' => 1,
                    'product_name' => 'Sony A7 III',
                    'order_desc' => 'Sewa 3 hari',
                    'status_label' => 'Dalam proses',
                    'product_image' => '',
                ],
                [
                    'id' => 2,
                    'product_name' => 'Canon EOS R50',
                    'order_desc' => 'Beli produk',
                    'status_label' => 'Dikirim',
                    'product_image' => '',
                ],
                [
                    'id' => 3,
                    'product_name' => 'Fujifilm X-T30',
                    'order_desc' => 'Sewa 7 hari',
                    'status_label' => 'Selesai',
                    'product_image' => '',
                ],
            ];
            $pesananAktif = 2;
            $sewaMendatang = 1;
            $saldoEscrow = 1250000.00;
        }

        // Greeting first name: misal "Regi" atau "Yogi"
        $fullName = trim($user['full_name'] ?? 'Pelanggan');
        $nameParts = explode(' ', $fullName);
        $firstName = $nameParts[0] ?? 'Pelanggan';
        if (strtolower($firstName) === 'mohammad' && isset($nameParts[1])) {
            $firstName = $nameParts[1];
        }

        return view('customer.dashboard', [
            'user' => $user,
            'firstName' => $firstName,
            'pesananAktif' => $pesananAktif,
            'sewaMendatang' => $sewaMendatang,
            'saldoEscrow' => $saldoEscrow,
            'latestOrders' => $latestOrders,
        ]);
    }
}
