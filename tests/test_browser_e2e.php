<?php

/**
 * ============================================================================
 * CAMHUB AUTOMATED BROWSER / E2E TEST SUITE
 * ============================================================================
 * Proyek        : CamHub – Camera Marketplace Platform
 * Tim Pengembang: Kelompok 1
 *                 1. Yogi Ario Pratama (Project Owner & Lead Developer)
 *                 2. Muhammad Regieyanto (Backend & System Architect)
 *                 3. Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
 *                 4. Krisna Mukti (Quality Assurance & Presentation Coordinator)
 * 
 * Cakupan Uji   :
 * 1. HTTP 200 Landing Page & Navbar CTA (Masuk/Daftar)
 * 2. HTTP 200 Halaman Login, Komponen Mockup & Akses Demo 1-Klik
 * 3. HTTP 200 Halaman Register & Tab Pemilihan Peran (Customer / Partner)
 * 4. Autentikasi Form Login Pelanggan (user:user) via POST & Session Cookie
 * 5. Akses Dashboard Pelanggan Berbasis Sesi Aktif & Lencana KYC Real-time
 * 6. Akses Halaman Status Verifikasi KYC (/kyc/status)
 * 7. Akses Halaman Kelola Profil (/profil)
 * 8. Pembaruan Biodata Profil Pelanggan via POST & Flash Message
 * 9. Uji Proteksi RBAC: Pelanggan Mengakses /dashboard/admin Ditolak (HTTP 403)
 * 10. Autentikasi Administrator (admin:admin) & Akses Kontrol Admin (HTTP 200)
 * 11. Proteksi GuestMiddleware: Pengguna Login Mengakses /login Diredirect (HTTP 302)
 * 12. Siklus Logout & Pembersihan Sesi Browser
 * ============================================================================
 */

$baseUrl = 'http://localhost/camhub/public/';
$cookieJarCustomer = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'camhub_cookie_customer_' . bin2hex(random_bytes(4)) . '.txt';
$cookieJarAdmin    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'camhub_cookie_admin_' . bin2hex(random_bytes(4)) . '.txt';
$cookieJarPartner  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'camhub_cookie_partner_' . bin2hex(random_bytes(4)) . '.txt';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function runHttpTest(string $testId, string $description, callable $closure): void
{
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    try {
        $result = $closure();
        if ($result === true || (is_array($result) && $result['status'] === true)) {
            $passedTests++;
            $msg = is_array($result) && !empty($result['msg']) ? " -> " . $result['msg'] : '';
            echo " [PASS] {$testId}: {$description}{$msg}\n";
        } else {
            $failedTests++;
            $msg = is_array($result) && !empty($result['msg']) ? " -> " . $result['msg'] : '';
            echo " [FAIL] {$testId}: {$description}{$msg}\n";
        }
    } catch (\Throwable $e) {
        $failedTests++;
        echo " [ERROR] {$testId}: {$description} -> Exception: " . $e->getMessage() . "\n";
    }
}

function httpGet(string $url, ?string $cookieFile = null, bool $followRedirects = false): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CamHub-Automated-Browser-Runner/1.0');
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body ?: '', 'redirect' => $redirectUrl];
}

function httpPost(string $url, array $postData, ?string $cookieFile = null, bool $followRedirects = false): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CamHub-Automated-Browser-Runner/1.0');
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body ?: '', 'redirect' => $redirectUrl];
}

function extractCsrf(string $html): string
{
    if (preg_match('/name="_token"\s+value="([a-f0-9]+)"/i', $html, $matches)) {
        return $matches[1];
    }
    if (preg_match('/content="([a-f0-9]{64})"/i', $html, $matches)) {
        return $matches[1];
    }
    return '';
}

echo "========================================================\n";
echo "   CAMHUB AUTOMATED BROWSER / E2E TEST SUITE (12 DoD)  \n";
echo "========================================================\n\n";

// TEST 01
runHttpTest('TEST-E2E-01', 'Akses Beranda Publik & Sambungan Tombol Header CTA', function () use ($baseUrl) {
    $res = httpGet($baseUrl, null, true);
    $hasHeaderLogin = str_contains($res['body'], '/login');
    $hasHeaderRegister = str_contains($res['body'], '/register');
    $hasTeamFooter = str_contains($res['body'], 'Yogi Ario') && str_contains($res['body'], 'Muhammad Regieyanto');
    return [
        'status' => ($res['code'] === 200 && $hasHeaderLogin && $hasHeaderRegister && $hasTeamFooter),
        'msg'    => "HTTP {$res['code']}, link Masuk/Daftar & identitas Kelompok 1 terverifikasi di DOM."
    ];
});

// TEST 02
runHttpTest('TEST-E2E-02', 'Akses Halaman Login, Desain Split-Screen & Akses Demo 1-Klik', function () use ($baseUrl) {
    $res = httpGet($baseUrl . 'login', null, true);
    $hasPillAdmin = str_contains($res['body'], "fillCredentials('admin', 'admin')");
    $hasPillCustomer = str_contains($res['body'], "fillCredentials('user', 'user')");
    $hasCsrf = !empty(extractCsrf($res['body']));
    return [
        'status' => ($res['code'] === 200 && $hasPillAdmin && $hasPillCustomer && $hasCsrf),
        'msg'    => "HTTP {$res['code']}, 3 pill demo login & token CSRF aktif."
    ];
});

// TEST 03
runHttpTest('TEST-E2E-03', 'Akses Halaman Registrasi & Komponen Pilihan Peran Akun', function () use ($baseUrl) {
    $res = httpGet($baseUrl . 'register', null, true);
    $hasRoleCustomer = str_contains($res['body'], 'value="customer"');
    $hasRolePartner = str_contains($res['body'], 'value="partner"');
    return [
        'status' => ($res['code'] === 200 && $hasRoleCustomer && $hasRolePartner),
        'msg'    => "HTTP {$res['code']}, pilihan peran Pelanggan & Mitra Lapak tersedia."
    ];
});

// TEST 04
$customerCsrf = '';
runHttpTest('TEST-E2E-04', 'Login Pelanggan (user:user) & Pembentukan Sesi Browser', function () use ($baseUrl, $cookieJarCustomer, &$customerCsrf) {
    $getRes = httpGet($baseUrl . 'login', $cookieJarCustomer, true);
    $customerCsrf = extractCsrf($getRes['body']);
    
    $postRes = httpPost($baseUrl . 'login', [
        '_token'     => $customerCsrf,
        'identifier' => 'user',
        'password'   => 'user'
    ], $cookieJarCustomer, false);
    
    $isRedirect = ($postRes['code'] === 302 && str_contains($postRes['redirect'], '/dashboard/pelanggan'));
    return [
        'status' => $isRedirect,
        'msg'    => "HTTP {$postRes['code']}, sesi tersimpan di cookie dan diarahkan ke {$postRes['redirect']}."
    ];
});

// TEST 05
runHttpTest('TEST-E2E-05', 'Akses Dashboard Pelanggan & Lencana Status KYC Dinamis', function () use ($baseUrl, $cookieJarCustomer) {
    $res = httpGet($baseUrl . 'dashboard/pelanggan', $cookieJarCustomer, true);
    $hasUser = str_contains($res['body'], 'User Demo') || str_contains($res['body'], 'PELANGGAN');
    $hasKycLink = str_contains($res['body'], '/kyc/status');
    return [
        'status' => ($res['code'] === 200 && $hasUser && $hasKycLink),
        'msg'    => "HTTP {$res['code']}, dashboard memuat nama pelanggan & lencana KYC dinamis."
    ];
});

// TEST 06
runHttpTest('TEST-E2E-06', 'Akses Pelacak Status KYC Pelanggan (/kyc/status)', function () use ($baseUrl, $cookieJarCustomer) {
    $res = httpGet($baseUrl . 'kyc/status', $cookieJarCustomer, true);
    $hasStatus = str_contains($res['body'], 'Status Verifikasi KYC') || str_contains($res['body'], 'TINJAUAN');
    return [
        'status' => ($res['code'] === 200 && $hasStatus),
        'msg'    => "HTTP {$res['code']}, halaman status pelacak verifikasi identitas tampil aktif."
    ];
});

// TEST 07
runHttpTest('TEST-E2E-07', 'Akses Halaman Pengaturan Biodata Profil Pelanggan (/profil)', function () use ($baseUrl, $cookieJarCustomer) {
    $res = httpGet($baseUrl . 'profil', $cookieJarCustomer, true);
    $hasProfileForm = str_contains($res['body'], 'Kota Domisili') && str_contains($res['body'], 'Simpan Perubahan Profil');
    return [
        'status' => ($res['code'] === 200 && $hasProfileForm),
        'msg'    => "HTTP {$res['code']}, form pembaruan biodata domisili pelanggan termuat lengkap."
    ];
});

// TEST 08
runHttpTest('TEST-E2E-08', 'Pembaruan Data Biodata Domisili Pelanggan via Form POST', function () use ($baseUrl, $cookieJarCustomer) {
    $getRes = httpGet($baseUrl . 'profil', $cookieJarCustomer, true);
    $csrf = extractCsrf($getRes['body']);
    
    $postRes = httpPost($baseUrl . 'profil/edit', [
        '_token'    => $csrf,
        'full_name' => 'User Demo CamHub Updated',
        'phone'     => '083333333334',
        'city'      => 'Sleman, DI Yogyakarta',
        'address'   => 'Jl. Gejayan No. 88, Sleman, DI Yogyakarta'
    ], $cookieJarCustomer, true);
    
    $hasSuccessFlash = str_contains($postRes['body'], 'berhasil diperbarui') || str_contains($postRes['body'], 'Sleman');
    return [
        'status' => ($postRes['code'] === 200 && $hasSuccessFlash),
        'msg'    => "Data tersimpan di basis data dan pesan flash pembaruan tampil di peramban."
    ];
});

// TEST 09
runHttpTest('TEST-E2E-09', 'Proteksi Hak Akses RBAC: Pelanggan Mengakses /dashboard/admin Dilarang', function () use ($baseUrl, $cookieJarCustomer) {
    $res = httpGet($baseUrl . 'dashboard/admin', $cookieJarCustomer, false);
    $isForbidden = ($res['code'] === 403 && str_contains($res['body'], '403'));
    return [
        'status' => $isForbidden,
        'msg'    => "HTTP {$res['code']} Forbidden, pelanggan dicegah mengakses area admin."
    ];
});

// TEST 10
runHttpTest('TEST-E2E-10', 'Autentikasi Administrator (admin:admin) & Akses Kontrol Admin', function () use ($baseUrl, $cookieJarAdmin) {
    $getRes = httpGet($baseUrl . 'login', $cookieJarAdmin, true);
    $adminCsrf = extractCsrf($getRes['body']);
    
    $postRes = httpPost($baseUrl . 'login', [
        '_token'     => $adminCsrf,
        'identifier' => 'admin',
        'password'   => 'admin'
    ], $cookieJarAdmin, false);
    
    $isRedirect = ($postRes['code'] === 302 && str_contains($postRes['redirect'], '/dashboard/admin'));
    $adminDashboard = httpGet($baseUrl . 'dashboard/admin', $cookieJarAdmin, true);
    
    return [
        'status' => ($isRedirect && $adminDashboard['code'] === 200 && str_contains($adminDashboard['body'], 'Administrator')),
        'msg'    => "Login admin sukses, akses /dashboard/admin menghasilkan HTTP 200."
    ];
});

// TEST 11
runHttpTest('TEST-E2E-11', 'Proteksi GuestMiddleware: Pengguna Terautentikasi Diredirect dari /login', function () use ($baseUrl, $cookieJarCustomer) {
    $res = httpGet($baseUrl . 'login', $cookieJarCustomer, false);
    $isRedirect = ($res['code'] === 302 && str_contains($res['redirect'], '/dashboard/pelanggan'));
    return [
        'status' => $isRedirect,
        'msg'    => "HTTP 302 redirect ke dashboard, formulir login diamankan dari sesi aktif."
    ];
});

// TEST 12
runHttpTest('TEST-E2E-12', 'Siklus Logout & Pembersihan Sesi Peramban', function () use ($baseUrl, $cookieJarCustomer) {
    $logoutRes = httpGet($baseUrl . 'logout', $cookieJarCustomer, false);
    $isLoggedOut = ($logoutRes['code'] === 302 && str_contains($logoutRes['redirect'], '/login'));
    
    // Verifikasi bahwa setelah logout, akses dashboard langsung diredirect ke login (unauthorized)
    $afterDashboard = httpGet($baseUrl . 'dashboard/pelanggan', $cookieJarCustomer, false);
    $isProtected = ($afterDashboard['code'] === 302 && str_contains($afterDashboard['redirect'], '/login'));
    
    return [
        'status' => ($isLoggedOut && $isProtected),
        'msg'    => "Logout berhasil (HTTP 302 ke /login), akses halaman terlindungi selanjutnya diblokir."
    ];
});

// TEST 13 (Tahap 06 Mitra Lapak)
runHttpTest('TEST-E2E-13', 'Autentikasi Mitra Lapak (mitra:mitra) via Form POST', function () use ($baseUrl, $cookieJarPartner) {
    $loginPage = httpGet($baseUrl . 'login', $cookieJarPartner, false);
    $csrf = extractCsrf($loginPage['body']);
    
    $postData = [
        '_token'     => $csrf,
        'identifier' => 'mitra',
        'password'   => 'mitra'
    ];
    $loginRes = httpPost($baseUrl . 'login', $postData, $cookieJarPartner, false);
    $isRedirect = ($loginRes['code'] === 302 && str_contains($loginRes['redirect'], '/dashboard/mitra'));
    
    return [
        'status' => $isRedirect,
        'msg'    => "Login mitra sukses (HTTP 302 diarahkan ke /dashboard/mitra)."
    ];
});

// TEST 14 (Tahap 06 Mitra Lapak)
runHttpTest('TEST-E2E-14', 'Akses Dashboard Mitra Lapak & Verifikasi Komponen Mockup Prototype', function () use ($baseUrl, $cookieJarPartner) {
    $dashboard = httpGet($baseUrl . 'dashboard/mitra', $cookieJarPartner, true);
    $hasTitle   = str_contains($dashboard['body'], 'Dashboard Mitra Lapak');
    $hasStore   = str_contains($dashboard['body'], 'Yogi Camera');
    $hasStats   = str_contains($dashboard['body'], 'Total Produk') && str_contains($dashboard['body'], 'Pesanan Aktif') && str_contains($dashboard['body'], 'Saldo Tersedia');
    $hasProduct = str_contains($dashboard['body'], 'Produk Saya') && str_contains($dashboard['body'], 'Tersedia');
    $isOk = ($dashboard['code'] === 200 && $hasTitle && $hasStore && $hasStats && $hasProduct);
    
    return [
        'status' => $isOk,
        'msg'    => $isOk ? "HTTP 200, identitas lapak 'Yogi Camera', 3 kartu metrik, dan daftar Produk Saya presisi prototype." : "Code: {$dashboard['code']}, Title: " . ($hasTitle?'Y':'N') . ", Store: " . ($hasStore?'Y':'N') . ", Stats: " . ($hasStats?'Y':'N') . ", Prod: " . ($hasProduct?'Y':'N') . " | Snippet: " . substr(strip_tags($dashboard['body']), 0, 150)
    ];
});

// TEST 15 (Tahap 06 Mitra Lapak)
runHttpTest('TEST-E2E-15', 'Akses Halaman Kelola Etalase Produk Lapak (/dashboard/mitra/produk)', function () use ($baseUrl, $cookieJarPartner) {
    $res = httpGet($baseUrl . 'dashboard/mitra/produk', $cookieJarPartner, true);
    $hasHeading = str_contains($res['body'], 'Kelola Inventaris Produk');
    $hasTable = str_contains($res['body'], 'Tarif Sewa Harian');
    $isOk = ($res['code'] === 200 && $hasHeading && $hasTable);
    
    return [
        'status' => $isOk,
        'msg'    => $isOk ? "HTTP 200, tabel inventaris kamera dan tombol Tambah Produk Baru termuat." : "Code: {$res['code']}, Heading: " . ($hasHeading?'Y':'N') . ", Table: " . ($hasTable?'Y':'N') . " | Snippet: " . substr(strip_tags($res['body']), 0, 150)
    ];
});

// TEST 16 (Tahap 06 Mitra Lapak)
runHttpTest('TEST-E2E-16', 'Akses Halaman Saldo Dompet & Pencairan Lapak (/dashboard/mitra/saldo)', function () use ($baseUrl, $cookieJarPartner) {
    $res = httpGet($baseUrl . 'dashboard/mitra/saldo', $cookieJarPartner, true);
    $hasWallet = str_contains($res['body'], 'Saldo Dompet') || str_contains($res['body'], 'Tarik Saldo');
    
    return [
        'status' => ($res['code'] === 200 && $hasWallet),
        'msg'    => "HTTP {$res['code']}, saldo dompet toko mitra dan tombol pencairan dana tampil aktif."
    ];
});

// TEST 17 (Tahap 06 Mitra Lapak)
runHttpTest('TEST-E2E-17', 'Proteksi Hak Akses RBAC: Mitra Dilarang Mengakses Area Admin', function () use ($baseUrl, $cookieJarPartner) {
    $adminRes = httpGet($baseUrl . 'dashboard/admin', $cookieJarPartner, false);
    
    return [
        'status' => ($adminRes['code'] === 403),
        'msg'    => "HTTP 403 Forbidden, akses akun mitra lapak ke area super admin diblokir."
    ];
});

// Bersihkan berkas cookie sementara
@unlink($cookieJarCustomer);
@unlink($cookieJarAdmin);
@unlink($cookieJarPartner);

echo "\n========================================================\n";
echo "HASIL PENGUJIAN BROWSER E2E: {$passedTests}/{$totalTests} BERHASIL (" . round(($passedTests / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($failedTests > 0) {
    exit(1);
}
exit(0);
