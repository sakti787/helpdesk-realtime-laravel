# Helpdesk Realtime — Ticketing + Live Chat

A fullstack helpdesk system for handling customer support with **ticket workflow** and **realtime chat**. Built as a portfolio project to demonstrate **Laravel + Inertia + Vue 3 + Naive UI + WebSockets**.

---

## ✨ Highlights

* **Role-based access**: Admin / Agent / Customer
* **Ticket workflow**: create → assign/claim → update status → resolve/close
* **Realtime chat per ticket** (WebSocket-ready)
* **Agent queue dashboard** (unassigned / assigned / filtered)
* **Audit trail ready** (ticket events)

> ✅ Designed to be easy to run locally (Laragon/MySQL). Live deployment is optional.

---

## 🧰 Tech Stack

**Backend**

* Laravel (PHP)
* MySQL/MariaDB
* Laravel Broadcasting (Pusher protocol compatible)

**Frontend**

* Inertia.js
* Vue 3
* Naive UI
* Vite

---

## 📌 Requirements

* PHP **8.2+**
* Composer
* Node.js **18+**
* MySQL / MariaDB (Laragon recommended)
* Git

Optional:

* Redis (queue / scaling)

---

## 🚀 Local Setup

### 1) Clone the repository

```bash
git clone <YOUR_REPO_URL>
cd helpdesk
```

### 2) Install dependencies

```bash
composer install
npm install
```

### 3) Environment configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Update database config inside `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=root
DB_PASSWORD=
```

### 4) Run migrations & seed demo users

```bash
php artisan migrate --seed
```

### 5) Run the app

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Open:

* [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 👤 Demo Accounts

Password for all users: `password`

| Role     | Email                                     |
| -------- | ----------------------------------------- |
| Admin    | [admin@demo.com](mailto:admin@demo.com)   |
| Agent    | [agent1@demo.com](mailto:agent1@demo.com) |
| Agent    | [agent2@demo.com](mailto:agent2@demo.com) |
| Customer | [cust1@demo.com](mailto:cust1@demo.com)   |

---

## 🔄 Ticket Status Flow

`OPEN → IN_PROGRESS → WAITING_CUSTOMER → RESOLVED → CLOSED`

---

## 📸 Screenshots / Demo

Add your screenshots here (recommended for portfolio):

* Agent Queue Dashboard
* Ticket Detail + Chat
* Customer Ticket List

Suggested folder:

* `public/projects/screenshots/`

Example (markdown):

```md
![Agent Queue](public/projects/screenshots/agent-queue.png)
![Ticket Chat](public/projects/screenshots/ticket-chat.png)
```

---

## 🗺️ Project Structure (High-level)

```txt
app/
  Events/              # Broadcasting events (MessageSent, TicketAssigned, etc.)
  Http/
    Controllers/       # Tickets, Messages, Admin pages
    Middleware/        # Role middleware
  Models/              # Ticket, TicketMessage, Category, TicketEvent
  Policies/            # TicketPolicy (RBAC)
resources/js/
  Pages/
    Admin/
    Agent/
    Customer/
    Tickets/
  Components/Chat/     # ChatBox, MessageBubble, TypingIndicator
routes/
  web.php              # Inertia pages
  api.php              # REST API (optional)
```

---

## 📡 Realtime Notes

This project is built to support realtime updates using Laravel Broadcasting (Pusher protocol compatible). If you enable a WebSocket server (e.g., soketi / laravel-websockets / Pusher), ticket chat can broadcast new messages to subscribed clients.

> If you don’t need realtime for local demo, you can keep broadcasting driver as `log`.

---

## 🧪 Testing (Optional)

```bash
php artisan test
```

---

## 🛣️ Roadmap

* [ ] Attachment upload on ticket chat
* [ ] Typing indicator
* [ ] Read receipts
* [ ] SLA countdown + breach alert
* [ ] Reports dashboard
* [ ] Notification integration (email / WhatsApp gateway)

---

## 🤝 Contributing

PRs and suggestions are welcome (portfolio project).

---

## 📄 License

MIT (or your preferred license).
