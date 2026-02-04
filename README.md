# Helpdesk Realtime (Ticketing + Chat)


![alt text](image.png)


A fullstack helpdesk platform with **ticket workflow** and **realtime chat** to help teams manage customer support efficiently.  
Built with **Laravel**, **Inertia.js**, **Vue 3**, **Naive UI**, **REST API**, and **WebSockets**.

---

## ✨ Features

### Core
- 🔐 Authentication (Laravel Breeze) + role-based access (**Admin / Agent / Customer**)
- 🎫 Ticket management: create, list, detail, status updates
- 🧑‍💻 Agent queue: **unassigned** / **assigned to me** / filtered tickets
- 💬 Ticket chat (message thread per ticket)

### Realtime
- ⚡ Realtime messages via WebSockets (Broadcasting + Echo)
- ⌨️ Typing indicator *(optional / in progress)*
- ✅ Read receipt *(optional / in progress)*

### Admin (optional / in progress)
- 🗂️ Categories & SLA configuration
- 📊 Basic reports (ticket volume, SLA breaches)

---

## 🧰 Tech Stack

**Backend**
- Laravel (PHP)
- MySQL (Laragon)
- Laravel Broadcasting (Pusher protocol compatible)
- Queue (Database / Redis optional)

**Frontend**
- Inertia.js
- Vue 3
- Naive UI
- Vite

---

## 📌 Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB (Laragon)
- Git

Optional:
- Redis (queue / realtime scaling)

---

## 🚀 Getting Started (Local Setup)

### 1) Clone repository
```bash
git clone <YOUR_REPO_URL>
cd helpdesk
2) Install dependencies
composer install
npm install
3) Setup environment
Copy .env.example to .env:

cp .env.example .env
php artisan key:generate
Update database config in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=root
DB_PASSWORD=
4) Run migrations & seed demo users
php artisan migrate --seed
5) Run the app
Terminal 1:

php artisan serve
Terminal 2:

npm run dev
App will be available at:

http://127.0.0.1:8000

👤 Demo Accounts
Password for all users: password

Role	Email
Admin	admin@demo.com
Agent	agent1@demo.com
Agent	agent2@demo.com
Customer	cust1@demo.com
🔄 Ticket Status Flow
OPEN → IN_PROGRESS → WAITING_CUSTOMER → RESOLVED → CLOSED

🧩 Project Structure (High-level)
app/
  Events/              # broadcasting events (MessageSent, TicketAssigned, etc.)
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
🧪 Testing (Optional)
Run feature tests:

php artisan test
📡 Realtime (WebSocket) Setup
This project uses Laravel Broadcasting (Pusher protocol compatible).

Option A — Local WebSocket (recommended)
You can use one of:

laravel-websockets package

soketi (Pusher-compatible server)

Add your broadcasting credentials in .env based on your chosen server.

Option B — Pusher Cloud
If you use Pusher:

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxx
PUSHER_APP_KEY=xxx
PUSHER_APP_SECRET=xxx
PUSHER_APP_CLUSTER=ap1
🗺️ Roadmap
 Attachments upload on ticket chat

 Typing indicator

 Read receipts

 SLA countdown + breach alert

 Reports dashboard (charts)

 Notifications (email/WhatsApp gateway integration)


Agent claims ticket → chat realtime → resolve ticket.