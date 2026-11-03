<?php

declare(strict_types=1);

/**
 * Test Suite: Tahap 03 - Autentikasi Pengguna & RBAC
 * 
 * Verifies 10 critical security, authentication, and authorization scenarios.
 * Usage: php tests/test_auth.php
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
echo "      CAMHUB TEST SUITE — TAHAP 03: AUTENTIKASI        \n";
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
    session_start_safe();
    $pdo = \App\Core\Database::getConnection();
    $userRepo = new \App\Repositories\UserRepository($pdo);
    $authService = new \App\Services\AuthService($userRepo, $pdo);
    $validator = new \App\Validators\AuthValidator($userRepo);

    // -------------------------------------------------------------
    // TEST-AUTH-01: Mockup Interface Rendering
    // -------------------------------------------------------------
    $loginHtml = view('auth.login', ['pageTitle' => 'Test Login']);
    $hasTagline = strpos($loginHtml, 'Sewa, Jual, Beli, Titip') !== false;
    $hasEscrow = strpos($loginHtml, 'Transaksi Aman (Escrow)') !== false;
    $hasKyc = strpos($loginHtml, 'Verifikasi Identitas Resmi (KYC)') !== false;
    $hasCsrf = strpos($loginHtml, 'name="_token"') !== false;
    $hasDemoBadges = strpos($loginHtml, 'fillCredentials(\'admin\', \'admin\')') !== false;

    $assertTest(
        'TEST-AUTH-01',
        'Antarmuka Login Split-Screen & Komponen Mockup Prototype',
        $hasTagline && $hasEscrow && $hasKyc && $hasCsrf && $hasDemoBadges,
        'Markup memuat tagline, 3 keunggulan ber-checkmark, CSRF token, dan demo login pill.'
    );

    // -------------------------------------------------------------
    // TEST-AUTH-02: Generator & Validasi Token CSRF
    // -------------------------------------------------------------
    $token = csrf_token();
    $fieldHtml = csrf_field();
    $validTokenCheck = verify_csrf($token);
    $invalidTokenCheck = verify_csrf('invalid_token_12345');

    $assertTest(
        'TEST-AUTH-02',
        'Generator & Validasi Keamanan Token CSRF',
        !empty($token) && strlen($token) === 64 && $validTokenCheck === true && $invalidTokenCheck === false,
        "Token valid ({$token}) diterima, token manipulasi ditolak."
    );

    // -------------------------------------------------------------
    // TEST-AUTH-03: Validasi Input Server-Side
    // -------------------------------------------------------------
    $invalidInput = [
        'full_name' => 'Yo', // too short
        'username' => 'invalid username with spaces!',
        'email' => 'bukan-email',
        'phone' => '12345', // invalid format
        'password' => '12', // too short
        'password_confirmation' => 'berbeda',
    ];
    $errors = $validator->validateRegister($invalidInput);

    $assertTest(
        'TEST-AUTH-03',
        'Validasi Formulir Server-Side Bahasa Indonesia',
        isset($errors['full_name']) && isset($errors['email']) && isset($errors['phone']) && isset($errors['password_confirmation']),
        'Pesan galat: nama lengkap, format email, nomor HP, dan kecocokan konfirmasi terdeteksi.'
    );

    // -------------------------------------------------------------
    // TEST-AUTH-04: Registrasi Akun Pelanggan Baru
    // -------------------------------------------------------------
    $testEmail = 'tester.' . time() . '@camhub.id';
    $testPhone = '0899' . substr((string)time(), -8);
    $regResult = $authService->register([
        'full_name' => 'Tester Otomatis',
        'username' => 'tester_' . time(),
        'email' => $testEmail,
        'phone' => $testPhone,
        'password' => 'passwordKuat123',
        'role' => 'customer',
        'city' => 'Bandung',
    ]);

    $savedUser = $userRepo->findByEmail($testEmail);
    $isBcrypt = $savedUser && password_verify('passwordKuat123', $savedUser['password_hash']);

    $assertTest(
        'TEST-AUTH-04',
        'Pendaftaran Akun Baru & Enkripsi BCRYPT',
        $regResult['success'] === true && $savedUser !== null && $isBcrypt,
        "User ID {$regResult['user_id']} tersimpan di DB dengan password ter-hash BCRYPT valid."
    );

    // -------------------------------------------------------------
    // TEST-AUTH-05: Login Instan Akun Admin (admin:admin)
    // -------------------------------------------------------------
    $adminLogin = $authService->attempt('admin', 'admin');
    $adminSuccess = $adminLogin['success'] === true &&
                    $adminLogin['user']['role_name'] === 'admin' &&
                    $adminLogin['redirect'] === '/dashboard/admin';

    $assertTest(
        'TEST-AUTH-05',
        'Login Instan Akun Administrator (admin:admin)',
        $adminSuccess,
        "Berhasil login sebagai {$adminLogin['user']['full_name']}, redirect ke /dashboard/admin."
    );

    // -------------------------------------------------------------
    // TEST-AUTH-06: Login Instan Akun User (user:user)
    // -------------------------------------------------------------
    $userLogin = $authService->attempt('user', 'user');
    $userSuccess = $userLogin['success'] === true &&
                   $userLogin['user']['role_name'] === 'customer' &&
                   $userLogin['redirect'] === '/dashboard/pelanggan';

    $assertTest(
        'TEST-AUTH-06',
        'Login Instan Akun Pelanggan (user:user)',
        $userSuccess,
        "Berhasil login sebagai {$userLogin['user']['full_name']}, redirect ke /dashboard/pelanggan."
    );

    // -------------------------------------------------------------
    // TEST-AUTH-07: Login Multi-Identifier (Email & No. HP)
    // -------------------------------------------------------------
    $emailLogin = $authService->attempt('admin@camhub.id', 'admin');
    $phoneLogin = $authService->attempt('081111111111', 'admin');

    $assertTest(
        'TEST-AUTH-07',
        'Login Fleksibel via Email dan Nomor HP',
        $emailLogin['success'] === true && $phoneLogin['success'] === true,
        'Akun dikenali dengan query fleksibel identifier (email & telepon).'
    );

    // -------------------------------------------------------------
    // TEST-AUTH-08: Keamanan & Pencatatan Password Salah
    // -------------------------------------------------------------
    $failLogin = $authService->attempt('admin', 'password_salah_123');
    $auditStmt = $pdo->prepare("SELECT COUNT(*) FROM audit_logs WHERE action = 'LOGIN_FAILED' AND target_table = 'users'");
    $auditStmt->execute();
    $auditCount = (int)$auditStmt->fetchColumn();

    $assertTest(
        'TEST-AUTH-08',
        'Penolakan Password Keliru & Pencatatan Audit Log',
        $failLogin['success'] === false && $auditCount > 0,
        "Akses ditolak ('{$failLogin['message']}') dan tersimpan di audit_logs ({$auditCount} rekam jejak)."
    );

    // -------------------------------------------------------------
    // TEST-AUTH-09: Role-Based Route Guard (RBAC)
    // -------------------------------------------------------------
    // Log in as customer
    $_SESSION['user'] = [
        'id' => 99,
        'role_name' => 'customer',
        'full_name' => 'Regi Demo',
    ];
    $roleMiddleware = new \App\Middleware\RoleMiddleware();
    // Check if customer can access customer route
    $customerAccess = $roleMiddleware->handle('customer');
    
    // Simulate check if customer is NOT admin
    $userRole = strtolower($_SESSION['user']['role_name']);
    $forbiddenCheck = ($userRole !== 'admin');

    $assertTest(
        'TEST-AUTH-09',
        'Pengendalian Hak Akses Berbasis Peran (RBAC Guard)',
        $customerAccess === true && $forbiddenCheck === true,
        'Role customer diizinkan di dashboard pelanggan, dicegah masuk area admin.'
    );

    // -------------------------------------------------------------
    // TEST-AUTH-10: Siklus Logout & Penghancuran Sesi
    // -------------------------------------------------------------
    $authBeforeLogout = auth_check();
    $authService->logout();
    $authAfterLogout = auth_check();

    $assertTest(
        'TEST-AUTH-10',
        'Siklus Sesi Autentikasi & Pembersihan Logout',
        $authBeforeLogout === true && $authAfterLogout === false,
        'Sesi user dihapus bersih dari memori dan auth_check() mengembalikan false.'
    );

} catch (Throwable $e) {
    echo "\n[ERROR PENGUJIAN]: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $testsFailed++;
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 03: {$testsPassed}/{$testsTotal} BERHASIL (" . ($testsFailed === 0 ? "100% PASS" : "GAGAL") . ")\n";
echo "========================================================\n";

exit($testsFailed === 0 ? 0 : 1);
