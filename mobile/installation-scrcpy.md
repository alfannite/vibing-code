# 📱 Menghubungkan Android ke Windows Menggunakan scrcpy

## 📌 Tujuan

Dokumentasi ini menjelaskan cara menghubungkan perangkat Android ke komputer Windows menggunakan **scrcpy** sehingga layar Android dapat ditampilkan dan dikendalikan langsung dari komputer melalui kabel USB.

Dengan scrcpy, proses pengembangan aplikasi, pengujian Telegram Bot, maupun administrasi perangkat Android menjadi jauh lebih mudah karena seluruh input dapat dilakukan menggunakan keyboard dan mouse komputer.

---

# ✨ Fitur

- 📺 Mirror layar Android ke Windows.
- 🖱️ Kontrol Android menggunakan mouse.
- ⌨️ Mengetik menggunakan keyboard komputer.
- 📋 Copy & Paste antara Windows dan Android.
- ⚡ Latensi rendah melalui koneksi USB.
- 🔓 Tidak memerlukan akses Root.

---

# 📋 Persyaratan

Sebelum memulai, pastikan telah menyiapkan:

| Kebutuhan | Status |
|-----------|--------|
| Windows 11 | ✅ |
| Android 8.0 atau lebih baru | ✅ |
| Kabel USB yang mendukung transfer data | ✅ |
| USB Debugging aktif | ✅ |
| Koneksi Internet | ✅ (untuk proses instalasi) |

---

# 🚀 Instalasi scrcpy

Buka **PowerShell** kemudian jalankan:

```powershell
winget install Genymobile.scrcpy
```

Tunggu hingga proses instalasi selesai.

Apabila berhasil, lakukan pengecekan versi:

```powershell
scrcpy --version
```

Contoh output:

```text
scrcpy 4.1
```

---

# 🔧 Mengaktifkan USB Debugging

## 1. Aktifkan Developer Options

Masuk ke:

```
Settings
↓
About Phone
↓
Version
↓
Tap Build Number sebanyak 7 kali
```

Masukkan PIN apabila diminta.

---

## 2. Aktifkan USB Debugging

Masuk ke:

```
Settings
↓
Additional Settings
↓
Developer Options
↓
USB Debugging
```

Aktifkan:

- USB Debugging

Apabila tersedia, aktifkan juga:

- USB Debugging (Security)

---

# 🔌 Hubungkan Android ke Komputer

Hubungkan perangkat menggunakan kabel USB.

Pada notifikasi USB pilih:

```
File Transfer (MTP)
```

Jangan gunakan mode:

```
Charge Only
```

---

# ✅ Verifikasi Koneksi

Jalankan:

```powershell
adb devices
```

Jika berhasil:

```text
List of devices attached
2041b6ec    device
```

Artinya perangkat telah berhasil terhubung.

---

# 📱 Menjalankan scrcpy

Jalankan perintah berikut:

```powershell
scrcpy
```

Apabila berhasil, layar Android akan langsung muncul pada Windows.

---

# 🎮 Shortcut Keyboard

| Shortcut | Fungsi |
|----------|--------|
| Ctrl + F | Fullscreen |
| Alt + F4 | Menutup scrcpy |
| Ctrl + C | Copy |
| Ctrl + V | Paste ke Android |

---

# ⚙️ Perintah Berguna

## Menurunkan Resolusi

```powershell
scrcpy --max-size=1920
```

---

## Meningkatkan Bitrate Video

```powershell
scrcpy --video-bit-rate=20M
```

---

## Mematikan Layar HP (Tetap Dapat Dikontrol)

```powershell
scrcpy --turn-screen-off
```

---

## Merekam Layar

```powershell
scrcpy --record rekaman.mp4
```

---

# ❌ Troubleshooting

## Perangkat Berstatus Unauthorized

Output:

```text
2041b6ec    unauthorized
```

### Penyebab

Komputer belum mendapatkan izin USB Debugging.

### Solusi

1. Cabut kabel USB.
2. Sambungkan kembali perangkat.
3. Akan muncul popup:

```
Allow USB Debugging
```

4. Centang:

```
Always allow from this computer
```

5. Tekan:

```
Allow
```

Kemudian jalankan kembali:

```powershell
adb devices
```

---

## ADB Tidak Dikenali

Output:

```text
adb : The term 'adb' is not recognized...
```

### Solusi

Tutup PowerShell kemudian buka kembali.

Apabila masih gagal, pastikan Android Platform Tools telah terpasang atau gunakan ADB bawaan dari instalasi scrcpy.

---

## Device Not Found

Output:

```text
No devices found
```

### Penyebab

- Kabel hanya mendukung charging.
- USB Debugging belum aktif.
- Driver USB belum terpasang.
- Mode USB masih Charge Only.

### Solusi

- Gunakan kabel USB Data.
- Aktifkan USB Debugging.
- Ubah mode USB menjadi File Transfer.
- Sambungkan ulang perangkat.

---

# ✅ Hasil Akhir

Apabila seluruh langkah berhasil dilakukan:

- Layar Android tampil di Windows.
- Keyboard komputer dapat digunakan untuk mengetik.
- Mouse dapat mengontrol Android.
- Copy & Paste berjalan dengan normal.
- Siap digunakan untuk proses pengembangan aplikasi maupun pengujian Telegram Bot.

---
