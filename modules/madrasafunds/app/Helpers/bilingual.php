<?php

declare(strict_types=1);

if (! function_exists('mf_bi')) {
    /**
     * Return a bilingual label showing both English and Urdu.
     * Example: mf_bi('Receipts') → "Receipts / رسیدیں"
     *
     * Namespaced as mf_bi() to avoid clashing with other modules that
     * define a global bi() helper.
     */
    function mf_bi(string $english): string
    {
        static $map = null;

        if ($map === null) {
            $path = __DIR__ . '/../../lang/ur.php';
            $map = file_exists($path) ? require $path : [];
        }

        $urdu = $map[$english] ?? null;

        return $urdu ? "{$english} / {$urdu}" : $english;
    }
}
