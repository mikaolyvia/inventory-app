<?php
// Fungsi untuk mencegah XSS (Cross-Site Scripting)
// Mengubah karakter khusus seperti <, >, &, " menjadi entitas HTML
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}