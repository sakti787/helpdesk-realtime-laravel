# Helpdesk Realtime Chat + Ticketing (Laravel 12 + Inertia Vue3 + Naive UI + Pusher)

Aplikasi helpdesk untuk customer service: **ticketing + realtime chat + attachment upload**.  
Stack utama: **Laravel 12**, **Inertia (Vue 3)**, **Naive UI**, **MySQL (Laragon)**, **Pusher**.

---

## ⚠️ IMPORTANT (Untuk Codex / AI Assistant)
**JANGAN melenceng dari rencana.** Ikuti urutan implementasi di bawah ini.  
Rules untuk Codex:
1. **Kerjakan per section** (MVP dulu), jangan lompat ke fitur bonus.
2. **Jangan ganti stack** (tetap Laravel 12 + Inertia Vue3 + Naive UI + Pusher + MySQL).
3. **Jangan ubah aturan bisnis** tanpa instruksi eksplisit.
4. Setiap kali selesai 1 section, pastikan **acceptance criteria** tercapai sebelum lanjut.
5. Prioritas: **Security & Role Authorization** dulu, baru UI polishing.
6. Attachment wajib sesuai aturan: **max 5 file**, **5MB/file**, tipe **jpg/png/pdf/doc/docx**.
7. Agent **boleh reply tanpa claim**, dan jika ticket masih unassigned maka **auto-assign** ke agent yang first reply (lihat Rules).

Jika Codex menyarankan solusi berbeda, **abaikan** dan kembali ke plan ini.

---

## Fitur MVP
- Auth (login/register)
- Role: `admin`, `agent`, `customer` (register default: `customer`)
- Ticketing: create, list, detail, assign/claim, status update
- Chat per ticket
- **Realtime** chat via **Pusher**
- **Attachment upload** (max 5 file, 5MB/file, tipe: jpg/png/pdf/doc/docx)
- Audit log (ticket events) basic

---

## Roles & Access
**Customer**
- Buat ticket + upload attachment saat create ticket
- Chat di ticket miliknya
- Lihat status ticket miliknya

**Agent**
- Bisa lihat **semua ticket**
- Boleh reply ticket walau belum claim
- Jika reply pertama pada ticket unassigned → **auto-assign** ke agent tersebut
- Bisa update status ticket

**Admin**
- Semua akses agent
- CRUD kategori (+ SLA fields opsional)
- Assign ticket ke agent

---

## Rules (Business Logic)
### Ticket Status
`OPEN → IN_PROGRESS → WAITING_CUSTOMER → RESOLVED → CLOSED`

### Assignment
- Ticket bisa unassigned.
- Agent boleh reply ticket apapun.
- Jika ticket unassigned dan **agent mengirim pesan pertama**:
  - set `assigned_agent_id` ke agent tersebut
  - set `first_response_at` (jika belum ada)
  - (opsional) ubah status ke `IN_PROGRESS` (direkomendasikan)

### Attachments
- Max **5 file** per submit (create ticket atau kirim chat)
- Max size **5MB** per file
- Mimes: `jpg,jpeg,png,pdf,doc,docx`
- Storage: `storage/app/tickets/{ticketId}/...`
- File record tersimpan di tabel `ticket_attachments`

---

## Tech Stack
- Backend: Laravel 12 (PHP 8.3)
- Frontend: Inertia + Vue 3 + Naive UI
- DB: MySQL (Laragon)
- Realtime: Pusher (Broadcasting + Laravel Echo)
- Storage: local filesystem + `storage:link`

---

## Local Setup

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL (Laragon)
- Git

### Install
```bash
git clone <your-repo-url>
cd helpdesk

composer install
npm install

cp .env.example .env
php artisan key:generate
