Cara Menjalankan (Instalasi)
Persiapan
•	Web server lokal: XAMPP (Apache + MySQL) atau sejenisnya.
•	PHP versi 7.4 ke atas (mendukung PDO).
Langkah-langkah
1.	Clone/download repository ini ke folder htdocs XAMPP: C:\xampp\htdocs\challenge4\
2.	Buat database ecommerce beserta tabel awal di MySQL
3.	Jalankan script perbaikan skema (schema_fix.sql) lewat phpMyAdmin (tab SQL → paste isi file → Go), atau lewat command line: mysql -u root ecommerce < schema_fix.sql
4.	Buat akun pengguna dengan password ter-hash (karena kolom password kini divalidasi dengan password_verify(), bukan teks biasa). Contoh isi data lewat SQL setelah generate hash manual, atau gunakan script sementara password_hash() lalu INSERT ke tabel users.
5.	Isi data contoh pada tabel products agar endpoint dapat diuji:
    INSERT INTO products (name, price) VALUES
    ('Kaos Polos', 75000),
    ('Sepatu Sneakers', 350000),
    ('Tas Ransel', 220000);
6.	Jalankan Apache & MySQL di XAMPP Control Panel, lalu buka: http://localhost/challenge4/login_form.html (untuk menguji login. Endpoint lain (products.php, profile.php, admin_dashboard.php) dapat diakses langsung via browser setelah login (karena session tersimpan di cookie)).
7.	Endpoint order.php (method POST) dan update_profile.php (method PUT) memerlukan tool seperti Postman untuk diuji, karena keduanya sengaja menolak request GET biasa sesuai standar RESTful API.
