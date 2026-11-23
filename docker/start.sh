#!/bin/sh
set -e

# Railway menyuntikkan PORT; Apache harus listen di port tersebut.
PORT="${PORT:-8080}"
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Struktur tabel selalu disinkronkan (aman dijalankan berulang).
runuser -u www-data -- php spark migrate --no-interaction

# Data awal: aktifkan sekali saja lewat RUN_SEED=true.
# Seeder menolak berjalan jika tabel sudah terisi, jadi tidak menggandakan data.
if [ "${RUN_SEED}" = "true" ]; then
  for seeder in UsadaSeeder GlosariumSeeder AdminSeeder; do
    echo ">> Seeding ${seeder}"
    runuser -u www-data -- php spark db:seed "${seeder}" || echo "!! ${seeder} dilewati/gagal"
  done
fi

exec apache2-foreground
