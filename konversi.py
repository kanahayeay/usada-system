"""Ubah data usada (.xlsx atau .csv) menjadi app/Data/usada.json untuk data contoh UI.
Pakai: python konversi.py <file data> app\\Data\\usada.json
"""
import json, re, sys
import pandas as pd

KOLOM = ["wuku", "hari", "pancawara", "gejala", "nama_penyakit",
         "penyebab", "ritual", "pengobatan", "teks_asli"]

def baca(src):
    if src.lower().endswith(".csv"):
        try:
            df = pd.read_csv(src, sep=None, engine="python", encoding="utf-8-sig")
        except UnicodeDecodeError:
            df = pd.read_csv(src, sep=None, engine="python", encoding="cp1252")
    else:
        df = pd.read_excel(src, sheet_name="Usada")
    df.columns = [str(c).strip().lower() for c in df.columns]
    hilang = [k for k in KOLOM if k not in df.columns]
    if hilang:
        sys.exit(f"Kolom tidak ditemukan: {hilang}\nKolom yang ada: {list(df.columns)}")
    return df[KOLOM].dropna(how="all")

def bersih(v):
    if pd.isna(v):
        return None
    v = str(v).strip()
    return None if v in ("", "-") else v

def parse_pengobatan(teks):
    hasil = []
    for baris in (teks or "").split("\n"):
        baris = baris.strip()
        if not baris or baris == "-" or ":" not in baris:
            continue
        jenis, sisa = baris.split(":", 1)
        bagian = [b.strip() for b in sisa.split("|")]
        bagian += [""] * (4 - len(bagian))
        hasil.append({
            "jenis": jenis.strip().lower(),
            "bahan": bagian[0] or None,
            "bagian_tubuh": bersih(bagian[1]),
            "keterangan": bersih(bagian[2]),
            "cara": bersih(bagian[3]),
        })
    return hasil

def main(src, dst):
    df = baca(src)
    wuku_urut = list(dict.fromkeys(df["wuku"]))
    data, lembar_terakhir = [], None
    for i, r in enumerate(df.itertuples(index=False)):
        teks = bersih(r.teks_asli) or ""
        penanda = re.findall(r"/(\d+[ab])/", teks)
        awal = lembar_terakhir
        if penanda:
            lembar_terakhir = penanda[-1]
        data.append({
            "id": i + 1,
            "hari_ke": i + 1,
            "wuku": r.wuku.strip(),
            "wuku_no": wuku_urut.index(r.wuku) + 1,
            "hari": r.hari.strip(),
            "pancawara": r.pancawara.strip(),
            "nama_penyakit": bersih(r.nama_penyakit),
            "gejala": bersih(r.gejala),
            "penyebab": bersih(r.penyebab),
            "ritual": bersih(r.ritual),
            "teks_asli": teks,
            "lembar_awal": awal,
            "lembar_akhir": lembar_terakhir,
            "pengobatan": parse_pengobatan(r.pengobatan if isinstance(r.pengobatan, str) else ""),
        })
    with open(dst, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=1)
    print(f"{len(data)} entri ditulis ke {dst}")

if __name__ == "__main__":
    main(sys.argv[1], sys.argv[2])