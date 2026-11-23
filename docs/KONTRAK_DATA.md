# Kontrak Data UI

UI hanya membaca data lewat `App\Models\UsadaModel`. Saat ini model itu membaca
`app/Data/usada.json` (data contoh dari Excel). Backend cukup mengganti ISI method
model dengan query database, TANPA mengubah nama method atau bentuk data.

## Method yang harus ada
| Method | Mengembalikan |
|---|---|
| `semua()` | array of Usada |
| `total()` | int, jumlah entri |
| `cari(int $id)` | Usada atau null |
| `berdasarkanHariKe(int $hariKe)` | Usada atau null (hari pawukon 1..210) |
| `filter(array $f)` | array of Usada. Kunci filter: `wuku`, `hari`, `pancawara`, `q` (semua opsional) |
| `daftarWuku()` | array nama wuku berurutan (30) |

## Bentuk satu Usada
```
id            int
hari_ke       int 1..210
wuku          string        (mis. "Sinta")
wuku_no       int 1..30
hari          string        ("Minggu".."Sabtu")
pancawara     string        ("Umanis","Pahing","Pwon","Wage","Kliwon")
nama_penyakit string|null   (null jika tidak ada; UI menampilkan "-")
gejala        string|null
penyebab      string|null
ritual        string|null
teks_asli     string        (boleh memuat penanda lembar seperti /2a/)
lembar_awal   string|null   (mis. "2a")
lembar_akhir  string|null
pengobatan    array of { jenis, bahan, bagian_tubuh|null, keterangan|null, cara|null }
```

## Aturan
- Nilai kosong HARUS `null`, bukan string "-" atau "".
- Jangan menyambung input pengguna langsung ke SQL (pakai query builder).
- Tanggal -> hari pawukon dihitung `App\Libraries\Pawukon::hariKe()`, bukan tabel.
- Fungsi bantu tampilan ada di `app/Helpers/usada_helper.php`.
- Sumber data: Excel `data_usada2_1.xlsx`, dikonversi dengan `konversi.py`.