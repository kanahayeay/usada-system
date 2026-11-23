#!/bin/sh
set -e

# Railway menyuntikkan PORT; Apache harus listen di port tersebut.
PORT="${PORT:-8080}"
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Diagnosis (tidak menampilkan nilai rahasia)
echo ">> PORT=${PORT} CI_ENVIRONMENT=${CI_ENVIRONMENT}"
echo ">> MYSQLHOST terisi: $([ -n "${MYSQLHOST}" ] && echo ya || echo TIDAK)"
echo ">> MYSQLDATABASE terisi: $([ -n "${MYSQLDATABASE}" ] && echo ya || echo TIDAK)"
if [ -z "${MYSQLHOST}" ]; then
  echo "!! Variabel MYSQL* belum terbaca. Cek tab Variables service aplikasi lalu Deploy ulang."
  exit 1
fi

# Struktur tabel selalu disinkronkan (aman dijalankan berulang).
php spark migrate --no-interaction

# Data awal: aktifkan sekali saja lewat RUN_SEED=true.
# Seeder menolak berjalan jika tabel sudah terisi, jadi tidak menggandakan data.
if [ "${RUN_SEED}" = "true" ]; then
  for seeder in UsadaSeeder GlosariumSeeder AdminSeeder; do
    echo ">> Seeding ${seeder}"
    php spark db:seed "${seeder}" || echo "!! ${seeder} dilewati/gagal"
  done
fi

# File log/cache yang dibuat spark (root) harus bisa ditulis Apache (www-data).
chown -R www-data:www-data writable

# Pastikan hanya satu MPM (prefork) yang aktif. Dilakukan saat container jalan
# karena penghapusan di tahap build bisa "hidup lagi" di lingkungan Railway.
a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork >/dev/null 2>&1 || true
echo ">> MPM aktif: $(ls /etc/apache2/mods-enabled | grep '^mpm_' | tr '\n' ' ')"
apache2ctl configtest || true

exec apache2-foreground