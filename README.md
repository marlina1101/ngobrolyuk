# 💬 NgobrolYuk

Aplikasi chat realtime berbasis web yang dibangun dengan **Laravel 12**, **Laravel Reverb** (WebSocket), dan **Alpine.js**. Mendukung chat private antar pengguna, chat grup, kirim file, indikator mengetik, serta tracking status **Online / Offline** secara realtime.

---

## ✨ Fitur

- 🔐 Autentikasi (Register, Login, Logout) via Laravel Breeze
- 💬 Chat private antar pengguna
- 👥 Chat grup (buat grup, kirim pesan ke grup)
- 📎 Kirim file / lampiran pada pesan
- 🟢 Tracking status **Online / Offline** realtime
- ⌨️ Indikator **sedang mengetik...**
- ⚡ Pesan masuk **langsung muncul** tanpa reload halaman (WebSocket via Laravel Reverb)
- 📋 Preview pesan terakhir di sidebar

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
|---|---|---|
| PHP | ^8.2 | Backend |
| Laravel | ^12.0 | Framework utama |
| Laravel Reverb | ^1.10 | WebSocket server |
| Laravel Breeze | ^2.4 | Autentikasi |
| MySQL | - | Database |
| Alpine.js | ^3.4 | Reaktivitas UI ringan |
| Tailwind CSS | ^3.1 | Styling |
| Vite | ^6.0 | Bundler aset |
| Laravel Echo | ^2.3 | Client WebSocket |
| Pusher JS | ^8.5 | Driver WebSocket client |

---

## 📁 Struktur Folder Penting

```
ngobrolyuk/
├── app/
│   ├── Events/
│   │   ├── MessageSent.php          # Event broadcast pesan private
│   │   └── GroupMessageSent.php     # Event broadcast pesan grup
│   ├── Http/Controllers/
│   │   ├── ChatController.php       # Halaman & logika chat private
│   │   ├── MessageController.php    # Kirim pesan (return JSON untuk AJAX)
│   │   └── GroupController.php      # Buat grup, kirim pesan grup
│   └── Models/
│       ├── User.php
│       ├── Message.php              # Pesan private (sender, receiver, file)
│       ├── Group.php                # Grup chat
│       └── GroupMessage.php         # Pesan dalam grup
├── database/migrations/
│   ├── create_messages_table.php    # sender_id, receiver_id, message, file
│   ├── create_groups_table.php      # name
│   ├── create_group_user_table.php  # pivot group <-> user
│   └── create_group_messages_table.php
├── resources/
│   ├── js/
│   │   ├── app.js                   # Logic WebSocket, online tracking, AJAX
│   │   └── bootstrap.js             # Konfigurasi Echo + Axios
│   └── views/
│       ├── chat/index.blade.php     # Halaman chat utama
│       ├── groups/
│       │   ├── index.blade.php      # Daftar grup
│       │   └── show.blade.php       # Halaman chat grup
│       └── layouts/app.blade.php    # Layout utama
├── routes/
│   ├── web.php                      # Semua route aplikasi
│   └── channels.php                 # Presence channel & private channel
└── .env                             # Konfigurasi environment
```

---

## ⚙️ Skema Database

### Tabel `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Nama pengguna |
| email | varchar | Email unik |
| password | varchar | Password ter-hash |

### Tabel `messages`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| sender_id | bigint FK | Pengirim (users.id) |
| receiver_id | bigint FK | Penerima (users.id) |
| message | text | Isi pesan |
| file | varchar nullable | Path file lampiran |

### Tabel `groups`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Nama grup |

### Tabel `group_user` (pivot)
| Kolom | Tipe | Keterangan |
|---|---|---|
| group_id | bigint FK | groups.id |
| user_id | bigint FK | users.id |

### Tabel `group_messages`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| group_id | bigint FK | groups.id |
| sender_id | bigint FK | users.id |
| message | text | Isi pesan |

---

## 🚀 Cara Menjalankan Proyek

### Prasyarat

Pastikan sudah terinstall:
- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- MySQL

---

### Langkah 1 — Clone / Extract Proyek

```bash
# Jika dari ZIP, extract dulu, lalu masuk ke folder
cd ngobrolyuk
```

---

### Langkah 2 — Install Dependensi PHP

```bash
composer install
```

---

### Langkah 3 — Install Dependensi JavaScript

```bash
npm install
```

---

### Langkah 4 — Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi berikut:

```env
APP_NAME=NgobrolYuk
APP_URL=http://127.0.0.1:8000

# Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_cating        # nama database kamu
DB_USERNAME=root
DB_PASSWORD=                  # isi password MySQL kamu

# Session — WAJIB pakai 'file' agar WebSocket auth berfungsi
SESSION_DRIVER=file

# Broadcasting & Queue
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

# Reverb WebSocket Server
REVERB_APP_ID=498196
REVERB_APP_KEY=your_reverb_key
REVERB_APP_SECRET=your_reverb_secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

> ⚠️ **Penting:** `SESSION_DRIVER` harus `file` (bukan `database`), karena Laravel Reverb membaca session saat autentikasi WebSocket. Jika pakai `database`, auth channel akan selalu gagal dan status selalu tampil Offline.

---

### Langkah 5 — Buat Database

Buat database MySQL dengan nama sesuai `DB_DATABASE` di `.env`:

```sql
CREATE DATABASE app_cating;
```

---

### Langkah 6 — Jalankan Migrasi

```bash
php artisan migrate
```

Perintah ini akan membuat semua tabel: `users`, `messages`, `groups`, `group_user`, `group_messages`, `jobs`, `cache`.

---

### Langkah 7 — Buat Symlink Storage (untuk file upload)

```bash
php artisan storage:link
```

---

### Langkah 8 — Build Aset Frontend

```bash
npm run build
```

Atau untuk mode development (auto-reload saat file berubah):

```bash
npm run dev
```

---

### Langkah 9 — Jalankan Semua Service

Buka **4 terminal terpisah** dan jalankan masing-masing:

**Terminal 1 — Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 — Reverb WebSocket Server:**
```bash
php artisan reverb:start
```

**Terminal 3 — Queue Worker (untuk broadcast event):**
```bash
php artisan queue:work
```

**Terminal 4 — Vite (jika pakai mode dev):**
```bash
npm run dev
```

> 💡 **Shortcut:** Kamu bisa menjalankan semuanya sekaligus dengan satu perintah:
> ```bash
> composer run dev
> ```
> Perintah ini menjalankan Laravel server, queue, log monitor, dan Vite secara bersamaan menggunakan `concurrently`.

---

### Langkah 10 — Buka Aplikasi

Akses aplikasi di browser:

```
http://127.0.0.1:8000
```

Daftarkan minimal **2 akun** untuk mencoba fitur chat dan tracking online/offline.

---

## 🧪 Cara Tes Tracking Online / Offline

1. Buka **2 browser berbeda** (misalnya Chrome dan Firefox, atau Chrome + mode Incognito)
2. Login dengan akun berbeda di masing-masing browser
3. Buka halaman Chat di keduanya
4. Status **● Online** akan muncul secara otomatis di sidebar dan header chat
5. Tutup salah satu tab → status berubah menjadi **● Offline** secara realtime

---

## 🔗 Daftar Route

| Method | URL | Nama | Keterangan |
|---|---|---|---|
| GET | `/` | - | Redirect ke login |
| GET | `/login` | login | Halaman login |
| POST | `/login` | - | Proses login |
| GET | `/register` | register | Halaman register |
| POST | `/register` | - | Proses register |
| POST | `/logout` | logout | Logout |
| GET | `/dashboard` | dashboard | Dashboard |
| GET | `/chat/{id?}` | chat.index | Halaman chat private |
| POST | `/message` | message.store | Kirim pesan private |
| GET | `/groups` | groups.index | Daftar grup |
| POST | `/groups` | groups.store | Buat grup baru |
| GET | `/groups/{id}` | groups.show | Halaman chat grup |
| POST | `/groups/{id}/message` | groups.message | Kirim pesan grup |
| GET | `/profile` | profile.edit | Edit profil |
| PATCH | `/profile` | profile.update | Update profil |
| DELETE | `/profile` | profile.destroy | Hapus akun |

---

## ❗ Troubleshooting

**Status selalu Offline:**
- Pastikan `SESSION_DRIVER=file` di `.env`
- Pastikan `php artisan reverb:start` sedang berjalan
- Pastikan `php artisan queue:work` sedang berjalan
- Jalankan `php artisan config:clear && php artisan cache:clear` lalu build ulang dengan `npm run build`
- Cek Console browser (F12) untuk melihat error WebSocket

**Pesan tidak terkirim realtime:**
- Pastikan queue worker berjalan: `php artisan queue:work`
- Cek `QUEUE_CONNECTION=database` di `.env`

**Error 500 saat kirim pesan:**
- Pastikan sudah menjalankan `php artisan storage:link`
- Pastikan folder `storage/app/public` writable

**WebSocket gagal connect:**
- Pastikan port 8080 tidak dipakai aplikasi lain
- Cek `REVERB_HOST` dan `REVERB_PORT` sudah sesuai di `.env`

---

## 👨‍💻 Dibuat Dengan

- [Laravel](https://laravel.com)
- [Laravel Reverb](https://reverb.laravel.com)
- [Laravel Breeze](https://github.com/laravel/breeze)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Laravel Echo](https://github.com/laravel/echo)