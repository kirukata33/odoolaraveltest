<?php

return [
    // URL Odoo, contoh: https://namaperusahaan.odoo.com atau https://odoo.domainmu.com
    'url' => env('ODOO_URL', 'https://your-odoo-instance.com'),

    // Nama database Odoo (untuk Odoo Online biasanya sama dengan subdomain)
    'db' => env('ODOO_DB', 'your-db-name'),

    // Username / email login Odoo (tetap dibutuhkan meski pakai API Key)
    'username' => env('ODOO_USERNAME', 'admin@example.com'),

    // API Key yang dibuat dari: Settings > My Profile > Account Security > New API Key
    'api_key' => env('ODOO_API_KEY', ''),

    // Timeout request ke Odoo (detik)
    'timeout' => env('ODOO_TIMEOUT', 30),
];
