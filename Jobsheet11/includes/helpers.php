<?php
// Fungsi bantu umum. Di-require dari includes/header.php.

/**
 * Escape output sebelum dicetak ke HTML (mencegah XSS).
 * Pakai untuk SEMUA data yang berasal dari database, $_GET, $_POST, atau $_SESSION.
 */
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
