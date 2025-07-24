<?php

if (!function_exists('money_idr')) {
    function money_idr($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}
