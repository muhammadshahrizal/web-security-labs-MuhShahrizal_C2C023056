# web-security-labs-MuhShahrizal_C2C023056

Analisis Kerentanan

Aplikasi ini mendemonstrasikan empat (ditambah satu bonus) kerentanan umum keamanan web. Setiap modul memiliki versi "Rentan" (Vulnerable) untuk eksploitasi dan versi "Aman" (Secure) sebagai perbandingan dan praktik terbaik.

1. Modul Login (SQL Injection)

Kerentanan ini terjadi ketika input pengguna (seperti username dan password) digabungkan langsung ke dalam query SQL, memungkinkan penyerang untuk memanipulasi logika query.

Versi Rentan

File: login.php (saat logic_mode == 'rentan')

Kode Rentan:

// Input pengguna ($username, $password) langsung dimasukkan ke string query
$sql = "SELECT id, username, password FROM sqli_users_vul WHERE username = '$username' AND password = '$password'";


Eksploitasi: Penyerang dapat memasukkan ' OR '1'='1' --  di field username untuk bypass otentikasi. Query tersebut akan menjadi:
... WHERE username = '' OR '1'='1' -- ' AND password = '...'
Bagian OR '1'='1' selalu benar, dan -- mengomentari sisa query, sehingga login berhasil tanpa password yang valid.

Versi Aman

File: login.php (saat logic_mode == 'aman')

Kode Aman (Prepared Statements):

// Query dipersiapkan dengan placeholder (?)
$stmt = $conn->prepare("SELECT id, username, password FROM sqli_users_safe WHERE username = ?");
// Data pengguna dikirim terpisah, tidak pernah menyentuh string query
$stmt->bind_param("s", $username);


Penjelasan: Prepared statements memisahkan logika query SQL dari data. Database memperlakukan input pengguna murni sebagai data, bukan sebagai perintah, sehingga manipulasi query tidak mungkin terjadi.

2. Form Komentar (Cross-Site Scripting - XSS)

Kerentanan ini terjadi ketika input pengguna yang tidak aman (seperti skrip jahat) disimpan dan kemudian ditampilkan kembali di halaman web tanpa sanitasi, sehingga dieksekusi di browser korban.

Versi Rentan

File: Artikel/Artikel1.php (saat $_SESSION['login_mode'] == 'rentan')

Kode Rentan:

// Data komentar langsung di-echo ke HTML
echo $comment['comment_text'];


Eksploitasi: Penyerang dapat mem-posting komentar berisi skrip, seperti <script>alert("XSS")</script>. Saat halaman dimuat, browser akan mengeksekusi skrip ini.

Versi Aman

File: Artikel/Artikel1.php (saat $_SESSION['login_mode'] == 'aman')

Kode Aman (Sanitasi Output):

// Fungsi htmlspecialchars() mengubah karakter khusus HTML
echo nl2br(htmlspecialchars($comment['comment_text']));


Penjelasan: Fungsi htmlspecialchars() mengubah karakter seperti < dan > menjadi entitas HTML (&lt; dan &gt;). Browser akan menampilkannya sebagai teks, bukan mengeksekusinya sebagai tag HTML, sehingga serangan XSS berhasil dicegah.

3. Form Upload File (Unrestricted File Upload)

Kerentanan ini terjadi ketika server mengizinkan pengguna meng-upload file tanpa validasi yang ketat terhadap tipe, ukuran, atau konten file, sehingga memungkinkan upload file berbahaya (misalnya, web shell).

Versi Rentan

File: post_rentan.php

Kode Rentan:

// File dipindahkan langsung tanpa memeriksa ekstensi atau tipe
$file_name = basename($_FILES['gambar']['name']);
$new_file_name = uniqid('vuln_') . '-' . $file_name;
$target_file = $upload_dir . $new_file_name;

if (move_uploaded_file($file_tmp_name, $target_file)) {
    // ... sukses
}


Eksploitasi: Penyerang dapat meng-upload file shell.php. Setelah di-upload, penyerang dapat mengakses file ini melalui URL untuk mengeksekusi perintah di server.

Versi Aman

File: post_safe.php

Kode Aman (Validasi Ketat):

// 1. Validasi Ekstensi (Whitelist)
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

if (in_array($file_ext, $allowed_ext)) {
    // 2. Validasi Tipe Konten (Memastikan file benar-benar gambar)
    $image_info = @getimagesize($file_tmp_name);

    if ($image_info !== false) {
        // ... baru pindahkan file
        move_uploaded_file($file_tmp_name, $target_file);
    }
}


Penjelasan: Versi aman menerapkan dua lapisan pertahanan:

Whitelist Ekstensi: Hanya mengizinkan ekstensi yang aman (jpg, png).

Verifikasi Konten: Menggunakan getimagesize() untuk memastikan file tersebut adalah gambar asli, bukan skrip yang diubah namanya menjadi .jpg.

4. Halaman Profil (Broken Access Control - BAC)

Kerentanan ini terjadi ketika aplikasi gagal memverifikasi apakah pengguna yang sedang login memiliki hak akses untuk melihat atau memodifikasi data yang diminta. Ini sering disebut Insecure Direct Object Reference (IDOR).

Versi Rentan

File: invoice_view_vul.php

Kode Rentan:

// Mengambil invoice HANYA berdasarkan 'id' dari URL
$invoice_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->bind_param("i", $invoice_id);


Eksploitasi: Pengguna A (ID 1) yang login dapat melihat invoice miliknya di ...view.php?id=1. Dengan mengubah URL menjadi ...view.php?id=2, ia dapat melihat invoice milik Pengguna B (ID 2), meskipun itu bukan miliknya.

Versi Aman

File: invoice_view_safe.php

Kode Aman (Pengecekan Kepemilikan):

// Mengambil invoice berdasarkan 'id' DARI URL DAN 'user_id' DARI SESSION
$invoice_id = $_GET['id'] ?? 0;
$user_id_session = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id_session);


Penjelasan: Versi aman menambahkan klausa AND user_id = ? ke query SQL. Ini memastikan bahwa query hanya akan mengembalikan hasil jika ID invoice cocok DAN ID pengguna pemilik invoice tersebut sama dengan ID pengguna yang sedang login (disimpan di $_SESSION).

Bonus: Pencarian (SQL Injection & XSS)

Halaman pencarian juga menjadi contoh yang baik untuk SQLi dan XSS.

File Rentan: search_vul.php

SQLi: Menggunakan string concatenation (... LIKE '%$q%' ...).

Reflected XSS: Menampilkan input pencarian q langsung ke halaman (echo $r['result_title']).

File Aman: search_safe.php

SQLi Dicegah: Menggunakan prepared statements (... LIKE ?).

XSS Dicegah: Menggunakan htmlspecialchars() pada output (echo safe_highlight(...)).
