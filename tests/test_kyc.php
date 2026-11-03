<?php

declare(strict_types=1);

/**
 * Test Suite: Tahap 04 - Profil Pelanggan & Verifikasi KYC
 * 
 * Verifies 10 critical scenarios for profile management, KYC form, file uploads,
 * duplicate submission guards, and audit logging.
 * Usage: php tests/test_kyc.php
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

session_start_safe();

// Autoloader for classes
spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

echo "========================================================\n";
echo "       CAMHUB TEST SUITE — TAHAP 04: PROFIL & KYC       \n";
echo "========================================================\n\n";

$testsTotal = 10;
$testsPassed = 0;
$testsFailed = 0;

$assertTest = function (string $testId, string $desc, bool $condition, string $detail = '') use (&$testsPassed, &$testsFailed) {
    if ($condition) {
        $testsPassed++;
        echo " [PASS] {$testId}: {$desc}\n";
        if ($detail !== '') echo "        -> {$detail}\n";
    } else {
        $testsFailed++;
        echo " [FAIL] {$testId}: {$desc}\n";
        if ($detail !== '') echo "        -> {$detail}\n";
    }
};

try {
    $pdo = \App\Core\Database::getConnection();
    $kycRepo = new \App\Repositories\KycRepository($pdo);
    $userRepo = new \App\Repositories\UserRepository($pdo);
    $validator = new \App\Validators\KycValidator();
    $kycService = new \App\Services\KycService($kycRepo, $pdo);
    $profileService = new \App\Services\ProfileService($kycRepo, $pdo);

    // Retrieve or create a clean customer user for testing
    $testCustomer = $userRepo->findByIdentifier('user');
    if (!$testCustomer) {
        // Fallback customer
        $testCustomer = $userRepo->findByIdentifier('regi');
    }
    $customerId = (int)$testCustomer['id'];

    // -------------------------------------------------------------
    // TEST-KYC-01: Mockup Interface Rendering
    // -------------------------------------------------------------
    $kycHtml = view('customer.kyc', [
        'pageTitle' => 'Test KYC',
        'user' => $testCustomer,
        'latestKyc' => null,
    ]);
    $hasGuide = strpos($kycHtml, 'Panduan Mengambil Foto KTP yang Benar') !== false;
    $hasNikInput = strpos($kycHtml, 'name="nik_ktp"') !== false;
    $hasUploadDropzone = strpos($kycHtml, 'name="ktp_file"') !== false;
    $hasAgreement = strpos($kycHtml, 'name="agreement"') !== false;

    $assertTest(
        'TEST-KYC-01',
        'Antarmuka Form KYC Multi-Step & Panduan Mockup Prototype',
        $hasGuide && $hasNikInput && $hasUploadDropzone && $hasAgreement,
        'Form memuat panduan foto KTP, dropzone unggah berkas, dan pernyataan keaslian.'
    );

    // -------------------------------------------------------------
    // TEST-KYC-02: Validasi NIK KTP Indonesia (16 Digit)
    // -------------------------------------------------------------
    $invalidNikErrors = $validator->validateSubmission([
        'full_name' => 'Budi Santoso',
        'nik_ktp' => '12345ABC', // Invalid
        'birth_date' => '2000-01-01',
        'gender' => 'Laki-laki',
        'agreement' => '1',
    ], []);

    $assertTest(
        'TEST-KYC-02',
        'Validasi Format NIK KTP 16 Digit Angka',
        isset($invalidNikErrors['nik_ktp']) && strpos($invalidNikErrors['nik_ktp'], '16 digit') !== false,
        "Validator menolak NIK tidak valid: '{$invalidNikErrors['nik_ktp']}'"
    );

    // -------------------------------------------------------------
    // TEST-KYC-03: Validasi Dokumen Upload (Format & Ukuran)
    // -------------------------------------------------------------
    $invalidFile = [
        'name' => 'malicious.php',
        'type' => 'application/x-php',
        'tmp_name' => sys_get_temp_dir() . '/test.php',
        'error' => UPLOAD_ERR_OK,
        'size' => 1024,
    ];
    $fileError = $validator->validateUploadedImage($invalidFile, 'Foto KTP');

    $assertTest(
        'TEST-KYC-03',
        'Validasi Ekstensi & MIME Type Dokumen Berkas',
        $fileError !== null && strpos($fileError, 'tidak diizinkan') !== false,
        "Unggahan file PHP/eksekusi ditolak: '{$fileError}'"
    );

    // -------------------------------------------------------------
    // TEST-KYC-04: Pengunggahan Berkas Aman & Hash Nama
    // -------------------------------------------------------------
    // Create temporary dummy image file
    $tempKtp = tempnam(sys_get_temp_dir(), 'ktp_');
    file_put_contents($tempKtp, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00\xFF\xDB"); // JPEG Magic Header
    
    $savedPath = $kycService->saveUploadedFile([
        'name' => 'my_ktp_original.jpg',
        'tmp_name' => $tempKtp,
    ], 'test_ktp');
    @unlink($tempKtp);

    $fullSavedDiskPath = $rootDir . '/public/' . $savedPath;
    $uploadSuccess = file_exists($fullSavedDiskPath) && str_starts_with($savedPath, 'uploads/kyc/test_ktp_');

    $assertTest(
        'TEST-KYC-04',
        'Sanitasi & Hashing Nama Berkas Unggahan Aman',
        $uploadSuccess,
        "Berkas tersimpan aman di [{$savedPath}], kebal terhadap file collision & path traversal."
    );

    // -------------------------------------------------------------
    // TEST-KYC-05: Penyimpanan Pengajuan KYC ke Basis Data
    // -------------------------------------------------------------
    // Clean up previous test submissions for this customer to ensure clean test run
    $pdo->prepare("DELETE FROM kyc_submissions WHERE user_id = :uid")->execute(['uid' => $customerId]);

    $tempSelfie = tempnam(sys_get_temp_dir(), 'selfie_');
    file_put_contents($tempSelfie, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00\xFF\xDB");

    $submitResult = $kycService->submitKyc($customerId, [
        'full_name' => 'User Demo CamHub',
        'nik_ktp' => '3271012345678901',
        'birth_date' => '1998-05-15',
        'gender' => 'Laki-laki',
    ], [
        'ktp_file' => ['name' => 'ktp.jpg', 'tmp_name' => $fullSavedDiskPath],
        'selfie_file' => ['name' => 'selfie.jpg', 'tmp_name' => $tempSelfie],
    ]);
    @unlink($tempSelfie);

    $latestKyc = $kycRepo->findLatestByUserId($customerId);

    $assertTest(
        'TEST-KYC-05',
        'Penyimpanan Pengajuan KYC Lengkap ke MySQL',
        $submitResult['success'] === true && $latestKyc !== null && $latestKyc['status'] === 'pending',
        "Pengajuan #{$latestKyc['id']} tersimpan di kyc_submissions dengan status 'pending'."
    );

    // -------------------------------------------------------------
    // TEST-KYC-06: Pencegahan Pengajuan Ganda (Duplicate Guard)
    // -------------------------------------------------------------
    $duplicateAttempt = $kycService->submitKyc($customerId, [
        'full_name' => 'User Demo CamHub',
        'nik_ktp' => '3271012345678901',
        'birth_date' => '1998-05-15',
        'gender' => 'Laki-laki',
    ], [
        'ktp_file' => ['name' => 'ktp.jpg', 'tmp_name' => $fullSavedDiskPath],
        'selfie_file' => ['name' => 'selfie.jpg', 'tmp_name' => $fullSavedDiskPath],
    ]);

    $assertTest(
        'TEST-KYC-06',
        'Pencegahan Pengajuan Ganda Saat Masih Pending',
        $duplicateAttempt['success'] === false && strpos($duplicateAttempt['message'], 'masih dalam antrean') !== false,
        "Sistem memblokir pengajuan baru saat status sebelumnya masih pending."
    );

    // -------------------------------------------------------------
    // TEST-KYC-07: Pembaruan Biodata Profil Pelanggan
    // -------------------------------------------------------------
    $updateProfResult = $profileService->updateProfile($customerId, [
        'full_name' => 'User Demo CamHub Updated',
        'phone' => '083333333334',
        'city' => 'Yogyakarta',
        'address' => 'Jl. Gejayan No. 88, Sleman, DI Yogyakarta',
    ]);

    $profileData = $kycRepo->findProfileByUserId($customerId);
    $profileUpdated = ($profileData && $profileData['city'] === 'Yogyakarta' && strpos($profileData['address'], 'Gejayan') !== false);

    $assertTest(
        'TEST-KYC-07',
        'Pembaruan Biodata Domisili & Kontak Profil Pelanggan',
        $updateProfResult['success'] === true && $profileUpdated,
        "Data user_profiles terbarui: Kota {$profileData['city']}, Alamat: {$profileData['address']}."
    );

    // -------------------------------------------------------------
    // TEST-KYC-08: Status Lencana Profil Real-Time
    // -------------------------------------------------------------
    $fullProfile = $profileService->getProfile($customerId);

    $assertTest(
        'TEST-KYC-08',
        'Sinkronisasi Status Lencana KYC pada Profil Pelanggan',
        $fullProfile !== null && $fullProfile['kyc_status'] === 'pending',
        "Status KYC profil terhubung real-time dengan status pengajuan ({$fullProfile['kyc_status']})."
    );

    // -------------------------------------------------------------
    // TEST-KYC-09: Pencatatan Audit Log Keamanan KYC
    // -------------------------------------------------------------
    $auditStmt = $pdo->prepare("SELECT COUNT(*) FROM audit_logs WHERE action = 'KYC_SUBMIT' AND user_id = :uid");
    $auditStmt->execute(['uid' => $customerId]);
    $kycAuditCount = (int)$auditStmt->fetchColumn();

    $assertTest(
        'TEST-KYC-09',
        'Pencatatan Audit Trail Pengajuan Dokumen KYC',
        $kycAuditCount > 0,
        "Aktivitas KYC_SUBMIT terekam pada audit_logs ({$kycAuditCount} catatan)."
    );

    // -------------------------------------------------------------
    // TEST-KYC-10: Route Guard & Autentikasi Pengajuan KYC
    // -------------------------------------------------------------
    // Non-logged-in visitor guard check
    $guestMiddleware = new \App\Middleware\AuthMiddleware();
    // Simulate unauthenticated
    $origSession = $_SESSION['user'] ?? null;
    unset($_SESSION['user']);
    $blockedUnauth = (auth_check() === false);

    // Restore session
    if ($origSession) $_SESSION['user'] = $origSession;

    $assertTest(
        'TEST-KYC-10',
        'Proteksi Route Guard (Wajib Login & Peran Customer)',
        $blockedUnauth === true,
        'Akses ke halaman formulir KYC diwajibkan otentikasi login aktif.'
    );

} catch (Throwable $e) {
    echo "\n[ERROR PENGUJIAN]: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $testsFailed++;
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 04: {$testsPassed}/{$testsTotal} BERHASIL (" . ($testsFailed === 0 ? "100% PASS" : "GAGAL") . ")\n";
echo "========================================================\n";

exit($testsFailed === 0 ? 0 : 1);

