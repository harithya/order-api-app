# Order API

Dokumentasi lengkap endpoint dapat dilihat melalui Swagger setelah aplikasi dijalankan.

```
http://localhost:8000/api-docs
```

---

# Menjalankan Test

Project ini menyediakan script untuk menguji proses order untuk race condition

Jalankan perintah berikut:

```bash
bash test-bash.sh
```

Script akan mengirim beberapa request secara bersamaan ke API order.

## Hasil yang Diharapkan

- Order hanya berhasil sesuai jumlah stok yang tersedia.
- Request yang melebihi stok akan gagal dengan pesan **Product out of stock**.
- Stok tidak akan menjadi negatif.
- Tidak terjadi **overselling** meskipun banyak request dikirim secara bersamaan.
