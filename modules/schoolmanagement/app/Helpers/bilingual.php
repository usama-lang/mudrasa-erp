<?php

declare(strict_types=1);

if (! function_exists('bi')) {
    /**
     * Return a bilingual label showing both English and Urdu.
     * Example: bi('Students') → "Students / طلباء"
     */
    function bi(string $english): string
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
