# Panduan Setup: Laravel <-> Odoo 19 API (Purchase Order)

## 1. Buat API Key di Odoo

1. Login ke Odoo sebagai user yang mau dipakai integrasi (misal admin).
2. Klik foto profil (kanan atas) > **My Profile**.
3. Buka tab **Account Security**.
4. Di bagian **API Keys**, klik **New API Key**.
5. Beri nama (misal: "Laravel Integration"), lalu **Generate Key**.
6. **Copy key-nya sekarang juga** — Odoo hanya menampilkan sekali, tidak bisa dilihat lagi setelahnya.

> Catatan: Fitur API Key butuh mode developer/technical settings aktif di beberapa versi.
> Kalau tidak muncul opsinya, aktifkan dulu **Developer Mode** lewat Settings > General Settings > scroll ke bawah > Activate the developer mode.

## 2. Cari nama Database Odoo

- Kalau pakai Odoo Online: biasanya nama database = subdomain kamu.
  Contoh: kalau URL-nya `https://mycompany.odoo.com`, maka `ODOO_DB=mycompany`.
- Kalau self-hosted / tidak yakin: buka `https://your-odoo-url.com/web/database/selector` untuk melihat daftar database.

## 3. Copy file-file ini ke project Laravel kamu

```
config/odoo.php                                   -> taruh di config/odoo.php
app/Services/OdooService.php                      -> taruh di app/Services/OdooService.php
app/Http/Controllers/PurchaseOrderController.php  -> taruh di app/Http/Controllers/PurchaseOrderController.php
resources/views/purchase-orders/index.blade.php   -> taruh di resources/views/purchase-orders/index.blade.php
```

Isi `routes/web-additions.php` tinggal kamu **tempel ke dalam** `routes/web.php` yang sudah ada (jangan ganti file routes yang lama, cukup tambahkan baris-barisnya).

## 4. Tambahkan konfigurasi di `.env`

Bukases file `.env` di root project Laravel, tambahkan baris berikut:

```env
ODOO_URL=https://mycompany.odoo.com
ODOO_DB=mycompany
ODOO_USERNAME=admin@mycompany.com
ODOO_API_KEY=isi-dengan-api-key-yang-tadi-di-copy
ODOO_TIMEOUT=30
```

Lalu jalankan:

```bash
php artisan config:clear
```

## 5. Jalankan Laravel

```bash
php artisan serve
```

Buka di browser:

```
http://127.0.0.1:8000/purchase-orders
```

Kalau berhasil, akan muncul tabel Purchase Order dari Odoo lengkap dengan filter status (Draft, Confirmed, Done, Cancelled).

Endpoint JSON (kalau mau dites lewat Postman atau dipakai app lain):

```
GET http://127.0.0.1:8000/api/purchase-orders
```

## 6. Troubleshooting

| Masalah | Kemungkinan Penyebab |
|---|---|
| "Autentikasi Odoo gagal" | ODOO_DB salah, atau API Key salah/expired, atau ODOO_USERNAME tidak sesuai user pemilik API Key |
| "Gagal menghubungi server Odoo: HTTP 404" | ODOO_URL salah / tidak ada trailing slash issue, cek bisa diakses browser dulu |
| Data kosong padahal ada PO di Odoo | Cek hak akses (access rights) user tersebut ke model `purchase.order`, atau domain filter terlalu ketat |
| Timeout | Server Odoo lambat merespon / firewall, naikkan `ODOO_TIMEOUT` |

## 7. Langkah selanjutnya (setelah ini jalan)

Setelah "ambil data" ini berhasil, urutan pengembangan yang disarankan:

1. **Detail per PO** — buat halaman detail (ambil order lines-nya juga lewat `purchase.order.line`).
2. **Create/Update** — kirim data dari Laravel ke Odoo (buat PO baru dari Laravel), pakai method `create` dan `write` di `OdooService`.
3. **Webhook dari Odoo** — supaya Odoo bisa "push" notifikasi ke Laravel saat ada perubahan (butuh Odoo automated actions + webhook module, atau Odoo Studio kalau Enterprise).
4. **Queue/Job** — kalau datanya besar, sebaiknya sinkronisasi jalan di background job (Laravel Queue), bukan langsung saat request masuk.
5. **Caching** — supaya tidak selalu hit API Odoo tiap refresh halaman, bisa cache hasil `searchRead` beberapa menit.

Kalau langkah 1-5 di atas mau kita mulai, tinggal bilang mau mulai dari yang mana.
