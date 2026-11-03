# AGENTS.md — Aturan Induk Kerja AI Agent Proyek CamHub

> **PROJECT:** CamHub (Camera Marketplace Platform)  
> **PROJECT OWNER:** Yogi Ario  
> **PRINSIP UTAMA:** *No Plan, No Code. No Approval, No Code. No Evidence, No Completion.*

Seluruh AI Agent (Antigravity, Gemini, Claude, Cursor, dll.) wajib membaca dan mematuhi seluruh panduan tata kelola dan standar kerja yang telah ditetapkan pada:

1. **Pedoman Tata Kelola Universal Master:** [`Docs/aturan/universal-rule.md`](Docs/aturan/universal-rule.md)
2. **Aturan Spesifik AI Agent (Gemini):** [`Docs/aturan/GEMINI.md`](Docs/aturan/GEMINI.md)
3. **Template Aturan Teknis Modular:** [`Docs/aturan/AGENTS.md`](Docs/aturan/AGENTS.md)
4. **Rencana Induk Implementasi Backend (13 Tahap):** [`Docs/implementation_plan.md`](Docs/implementation_plan.md)

---

## Ketentuan Mutlak Sebelum Bertindak

1. **Approval Gate:** Dilarang keras membuat atau mengedit file kode aplikasi sebelum membuat rencana kerja tertulis dan memperoleh persetujuan eksplisit dari Yogi Ario (*Zero-Code before Approval*).
2. **Strict Native Technology:** Menggunakan PHP Native (PDO) dan MySQL di Laragon. Dilarang menggunakan framework (Laravel, CI, dll.) tanpa izin Project Owner.
3. **Integritas Desain:** Wajib mengikuti 9 prototype UI/UX acuan di folder `template/CamHub - Interactive UI UX Prototype/`. Folder template bersifat *READ-ONLY*.
4. **Git Flow:** Dilarang commit/push langsung ke `main`. Seluruh pekerjaan dilakukan melalui branch `develop` dan branch tahapan (`tahap/<nomor>-<nama-tahap>`).
5. **Bahasa & Komunikasi:** Gunakan Bahasa Indonesia baku dan profesional. Di akhir setiap respons, tanyakan opsi pencadangan riwayat percakapan ke `Docs/chat/riwayat<ddmmyyyy>.md`.

