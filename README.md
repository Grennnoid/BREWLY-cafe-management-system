<<<<<<< HEAD
# Brewly - Web-Based Cafe Ordering and Management System

Sistem Pemesanan dan Manajemen Kafe Berbasis Web menggunakan **Laravel 12 (PHP 8.2+)**, dibangun secara presisi mengikuti 4 dokumen diagram:
1. **DFD Level 0 (Diagram Konteks)**
2. **DFD Level 1 (Data Flow Diagram Level 1)**
3. **ERD (Entity-Relationship Diagram)**
4. **Use Case Diagram (4 Aktor: Pelanggan, Kasir, Dapur, Admin)**

---

## 🚀 Cara Menjalankan Website

1. Buka terminal di folder project:
   ```bash
   cd c:\Users\ACER\Desktop\brewly
   ```

2. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```

3. Buka browser dan akses:
   - **Menu Pelanggan**: [http://localhost:8000](http://localhost:8000)
   - **Login Staf**: [http://localhost:8000/login](http://localhost:8000/login)

---

## 👥 Akun Login Demo (Tersedia Tombol 1-Klik di Halaman Login)

Semua akun default menggunakan password: `password123`

| Role / Aktor | Email Staf | Password | Akses & Fitur |
|---|---|---|---|
| **Admin** | `admin@brewly.com` | `password123` | Dashboard metrik, CRUD Menu & Kategori (DFD 1.0), Kelola Staf (DFD 5.0), Meja Kafe (D7), dan Laporan Penjualan (DFD 6.0) |
| **Kasir (Cashier)** | `cashier@brewly.com` | `password123` | Kelola pesanan, proses bayar tunai (kalkulator kembalian), konfirmasi QRIS/Transfer, terbitkan & cetak struk thermal |
| **Dapur (Kitchen)** | `kitchen@brewly.com` | `password123` | Kitchen Display System (KDS) live antrian, terima pesanan, siapkan menu, konfirmasi siap saji |
| **Pelanggan (Customer)** | *Tanpa Login* | - | Pilih meja/takeaway, tambah menu & catatan, checkout, lacak status live, cetak struk digital |

---

## 📐 Kesesuaian dengan Diagram

### 1. DFD Level 0 & Level 1
- **1.0 Kelola & Lihat Menu**: Pelanggan dapat melihat menu per kategori, admin dapat menambah/mengedit/menghapus menu & kategori.
- **2.0 Kelola Keranjang & Buat Pesanan**: Simpan cart per sesi, pilih Meja (Dine In) atau Take Away, hitung total harga.
- **3.0 Proses Pembayaran & Terbitkan Struk**: Kasir memverifikasi pembayaran tunai, QRIS, dan transfer, otomatis generate nomor struk unik `RCP-YYYYMMDD-XXXX`.
- **4.0 Siapkan & Lacak Pesanan**: Dapur menerima pesanan dan mengubah status (PENDING -> ACCEPTED -> PREPARING -> READY -> COMPLETED); Pelanggan melacak status secara live.
- **5.0 Kelola Staf**: Admin mengelola akun staf (Admin, Kasir, Dapur) dan status aktif/non-aktif.
- **6.0 Buat Laporan Penjualan**: Laporan omset, ranking menu terlaris, filter tanggal, dan format cetak resmi.

### 2. ERD (11 Tabel Database Sesuai Skema)
- `categories` (category_id, name, description)
- `menus` (menu_id, category_id, name, description, price, image_url, is_available)
- `cafe_tables` (table_id, table_number, capacity, status: AVAILABLE/OCCUPIED)
- `carts` (cart_id, session_id)
- `cart_items` (cart_item_id, cart_id, menu_id, quantity, note)
- `orders` (order_id, customer_name, table_id, order_type, status, total_amount, note, ordered_at, completed_at)
- `order_items` (order_item_id, order_id, menu_id, quantity, unit_price, subtotal, note)
- `staff` (staff_id, name, email, password_hash, role, is_active)
- `order_status_logs` (log_id, order_id, changed_by, old_status, new_status, changed_at)
- `payments` (payment_id, order_id, confirmed_by, method, status, amount_paid, change_amount, paid_at)
- `receipts` (receipt_id, payment_id, issued_by, receipt_number, issued_at)
=======
# BREWLY-cafe-management-system
>>>>>>> d301539da5e21cfe8ada113cf676630f65b1340e
