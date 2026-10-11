<?php
// Helper untuk validasi input server-side
function validatePositiveInt($value, $fieldName) {
    $val = filter_var($value, FILTER_VALIDATE_INT);
    if ($val === false || $val <= 0) {
        return "$fieldName harus berupa angka bulat positif lebih dari 0.";
    }
    return null;
}

function validatePrice($value) {
    if (!is_numeric($value) || $value < 0) {
        return "Harga tidak boleh bernilai negatif.";
    }
    return null;
}