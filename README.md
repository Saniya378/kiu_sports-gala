# Karakoram International University (KIU) &bull; Annual Sports Gala Portal

A web application and tournament management portal built for **Karakoram International University (KIU)**. It features live match coverage, automatic points table calculation, historical archives, a prestigious athletic Hall of Fame, an interactive photo gallery with a full-screen lightbox, printable official administrative reports, and role-based access control for tournament officials and scorers.

---

## 🏆 Key Features

### 1. Public Portal
- **Home**: Dynamic banner with intelligent tournament states. Displays today's live/scheduled fixtures during a running gala, or a countdown and highlights of past champions when between seasons.
- **Today's Matches**: Live scorecard view filtering exclusively today's games for the active gala edition, with instant sport filtering.
- **Schedule**: Complete fixture timeline filterable by edition (defaults to latest), sport discipline, and date.
- **Match Detail**: Match scoreboard with scores, winner highlights, venue details, team rosters, and photo gallery.
- **Results & Standings**: Automatic dynamic points table calculation powered by `PointsTableService` (Win = 3 pts, Draw = 1 pt, Loss = 0 pts; sorted by Points, Score Difference, Score For, and Team Name).
- **Participating Teams**: Departmental squads, division categories, and player rosters.
- **Archive**: Historical records selector to view past editions with their participating sports, champions, match results, and photo archives.
- **Hall of Fame**: All-time departmental medal leaderboard (Gold & Silver counts) and historical roll of honor across every sport discipline.
- **Photo Gallery**: Action highlights and gala ceremony photos grouped by edition with an interactive, keyboard-accessible Alpine.js lightbox.
- **News & Bulletins**: Press releases, schedule revisions, and official communique with cover images and publication timestamps.
- **About & Contact**: Directorate of Sports & Physical Education office info, university venues, and contact channels.

### 2. Admin & Scorer Portal
- **Role-Based Access Control (RBAC)**:
  - **Administrator (`admin`)**: Complete management over editions, departments, venues, sports, teams, rosters, fixtures, winners, news, and official printable reports.
  - **Scorer (`scorer`)**: Strictly scoped access to live score entry and match photo uploads. Access to administrative management pages is forbidden (403).
- **Editions Management**: Multi-year tournament tracking (`upcoming`, `running`, `completed`) with banner uploads.
- **Teams & Roster Builder**: Departmental team registration, inline player roster addition/removal, and "Copy teams from previous edition" tool with duplicate prevention.
- **Fixtures & Scheduling**: Match scheduling with automatic team validation (both teams must belong to selected edition and sport; teams cannot play against themselves).
- **Score Entry**: One-click score recording with automatic winner detection (or draw) and fixture status updates.
- **Photo Processing**: Multi-image upload for match and edition photos with automatic resizing to 1200px max width via Intervention Image (GD driver).
- **Reports & Print Center**: Print-optimized pages with formal university letterheads, verified signature lines, and `@media print` CSS:
  - Winners List by Edition
  - Fixtures List by Date
  - Teams & Player Rosters by Sport

---

## 🛠️ Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **Image Processing**: `intervention/image` (v3 with GD driver)
- **Database**: MySQL 8.0+ (compatible with SQLite for testing)
- **Authentication**: Laravel Breeze (Blade stack)

---

## 🚀 Setup & Installation Guide

### Prerequisites
Ensure your local or production environment has:
- PHP >= 8.2 (with `pdo`, `mbstring`, `fileinfo`, `gd`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` extensions)
- Composer 2+
- Node.js (v18+) & npm
- MySQL / MariaDB

### 1. Clone & Install Dependencies
```bash
git clone <repository-url> kiu-sports-gala
cd kiu-sports-gala

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install and build frontend assets
npm install
npm run build
```

### 2. Configure Environment
Copy `.env.example` to `.env` (or configure your existing `.env`):
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database credentials in `.env`:
```env
APP_NAME="KIU Sports Gala"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sports_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Create Storage Link
Link the public storage disk so uploaded banners, match photos, and news images are publicly accessible:
```bash
php artisan storage:link
```

### 4. Run Migrations & Demo Seeder
```bash
# Run database migrations
php artisan migrate --force

# Seed demo data (sample past edition 2025, running edition 2026, teams, matches, and default users)
php artisan db:seed --class=DemoSeeder --force
```

---

## 🔑 Default User Credentials

| Role | Email | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kiu.test` | `password` | Full Administrative Access |
| **Scorer** | `scorer@kiu.test` | `password` | Score Entry & Match Photos |

> **Note**: Public user self-registration (`/register`) is disabled by default for security. Additional tournament scorers or administrators must be provisioned via database seeders or administrative commands.

---

## 🧪 Automated Testing

The project includes an end-to-end feature and unit test suite covering authentication, RBAC boundaries, CRUD operations, points table calculations, photo resizing, and public views:

```bash
php artisan test
```

To run a specific test suite:
```bash
php artisan test --filter=PublicPagesTest
php artisan test --filter=ReportTest
php artisan test --filter=ScoreEntryTest
```

---

## 📦 Production Deployment Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
2. Run `composer install --optimize-autoloader --no-dev`.
3. Run `npm run build` to compile production CSS & JS bundles into `public/build`.
4. Cache configuration, routes, and views for maximum performance:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Ensure web server (Nginx/Apache) document root points to `public/` and directory permissions allow writing to `storage/` and `bootstrap/cache/`.
