<?php

declare(strict_types=1);

$suites = [
    "test_foundation.php",
    "test_database.php",
    "test_auth.php",
    "test_kyc.php",
    "test_catalog.php",
    "test_partner_dashboard.php",
    "test_booking.php",
    "test_marketplace.php",
    "test_consignment.php",
    "test_escrow_payment.php",
    "test_admin_suite.php",
    "test_browser_e2e.php",
    "test_report_notifications.php",
    "test_hardening_production.php",
    "test_fix_admin_dispute.php"
];

$totalSuites = count($suites);
$passedSuites = 0;
$totalTests = 0;
$passedTests = 0;

echo "========================================================\n";
echo "   MASTER REGRESSION SUITE: 15 SUITE PENGUJIAN CAMHUB   \n";
echo "========================================================\n\n";

foreach ($suites as $suite) {
    $cmd = 'php ' . escapeshellarg(__DIR__ . '/' . $suite);
    $out = shell_exec($cmd);
    $passCount = substr_count($out, '[PASS]');
    $failCount = substr_count($out, '[FAIL]');
    $totalTests += ($passCount + $failCount);
    $passedTests += $passCount;

    if ($failCount === 0 && $passCount > 0) {
        $passedSuites++;
        echo sprintf(" %-32s : %2d / %2d PASS (100%%)\n", $suite, $passCount, $passCount);
    } else {
        echo sprintf(" %-32s : FAILED (%d pass, %d fail)\n", $suite, $passCount, $failCount);
        echo $out . "\n";
    }
}

echo "\n========================================================\n";
echo "HASIL AKHIR: {$passedTests} / {$totalTests} TESTS PASSED ({$passedSuites} / {$totalSuites} SUITES OK)\n";
echo "STATUS     : " . ($passedTests === $totalTests ? "SEMUA {$totalTests} PENGUJIAN 100% SUKSES HIJAU" : "ADA PENGUJIAN GAGAL") . "\n";
echo "========================================================\n";

exit($passedTests === $totalTests ? 0 : 1);
