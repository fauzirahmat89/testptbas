# Project testPTBAS

Proyek ini adalah aplikasi Laravel yang dikonfigurasi dengan Docker untuk lingkungan pengembangan dan produksi yang optimal.

## Arsitektur Stack
- **Framework:** Laravel
- **Web Server:** Nginx (Port 8080)
- **PHP Engine:** PHP 8.3-fpm (Dynamic Workers: 5 - 20)
- **Cache & Queue:** Redis (Port 8099)
- **Background Jobs:** Dedicated Queue Worker container
- **Database:** MySQL 8.0 (Port 3306) di dalam Docker

## Prasyarat
- Docker dan Docker Compose terpasang di mesin Anda.

## Instalasi & Menjalankan Project

1. **Clone Project & Masuk ke Direktori**
   ```bash
   cd testPTBAS
   ```

2. **Setup Environment**
   Pengaturan utama untuk Docker di file `.env`:
   - `DB_HOST=db`
   - `DB_PASSWORD=root`
   - `REDIS_HOST=redis`
   - `QUEUE_CONNECTION=redis`

3. **Build dan Jalankan Container**
   ```bash
   docker compose up -d --build
   ```

4. **Akses Aplikasi**
   - Web: [http://localhost:8080](http://localhost:8080)
   - Redis: `localhost:8099` (External port)

## Antrean & Worker (Queue)
Proyek ini menggunakan Redis untuk mengelola antrean. Layanan `worker` akan berjalan otomatis di latar belakang.
- **Cek Log Worker:**
  ```bash
  docker logs -f testptbas-worker-1
  ```
- **Restart Worker (setelah perubahan kode):**
  ```bash
  docker compose exec app php artisan optimize:clear
  docker compose restart worker
  ```

## Perintah Artisan (Inside Docker)
Gunakan `docker-compose exec app` untuk menjalankan perintah di kontainer utama:
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan tinker
docker compose exec app php artisan email:send
docker compose exec app php artisan schedule:list
```

## Konfigurasi Khusus
- **PHP-FPM:** `.docker/php/php-fpm.d/zzz-custom.conf` (Mode dynamic, max 20 workers).
- **Nginx:** `.docker/nginx/nginx.conf` (Listening port 8080).
- **Dockerfile:** Menggunakan PHP 8.3-fpm dengan otomatisasi izin folder (permissions).

## Pengembangan Lokal
Volume di-mount ke `.` sehingga perubahan kode langsung sinkron. Jika terjadi masalah izin tulis (permission denied), jalankan:
```bash
docker compose exec --user root app chown -R www-data:www-data storage bootstrap/cache
```
