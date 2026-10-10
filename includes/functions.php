<?php
// includes/functions.php - shared helpers

function e($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function fmtDate($d) {
    if ($d instanceof DateTime) {
        return $d->format('Y-m-d H:i:s');
    }
    return $d ? e($d) : '-';
}
