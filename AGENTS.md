# AGENTS.md

## Project Overview

Laravel 12 organoleptic assessment system for BPPMHKP (Badan Pemeriksaan Mutu Hasil Kelautan dan Perikanan). Entire codebase (code, comments, routes, DB columns, UI) is in **Bahasa Indonesia**. Match this language in all new code and views.

## Quick Commands

```bash
composer setup          # install + migrate + seed + npm build (first-time setup)
composer dev            # starts artisan serve + queue + pail + vite concurrently
composer test           # clears config cache, then runs `php artisan test`
php artisan migrate     # run pending migrations
php artisan db:seed     # run DatabaseSeeder (order-dependent, see below)
npm run build           # production Vite build
npm run dev             # Vite dev server with HMR
```

There is no linter or typechecker configured. No CI pipeline exists.

## Testing

- Framework: **Pest 3** (`tests/Pest.php` configures base TestCase).
- Tests use **in-memory SQLite** (configured in `phpunit.xml`).
- Run a single test file: `php artisan test --filter=tests/Feature/ProfileTest.php`
- Tests are Breeze-generated auth/chrome tests (login, registration, password reset, profile) plus `ExampleTest`. Most business logic (assessments, sessions, imports) is untested.
- Auth uses **Laravel Breeze** (blade stack, `routes/auth.php`, `resources/views/auth/`). Registering a new user is possible but routes groups require specific roles.

## Architecture

### Role-Based Access

Three roles enforced by `App\Http\Middleware\RoleMiddleware` via `role:admin`, `role:panelis`, `role:penyelia` in route groups. Role stored as string column `role` on `users` table. Role `pimpinan` sudah dihapus/digabung menjadi `penyelia` (migration `2026_09_08_000001_update_pimpinan_role_ke_penyelia`); nilai lama `pimpinan` masih tersisa di enum migration lama (tidak digunakan).

| Role     | Prefix     | Capabilities |
|----------|------------|-------------|
| admin    | `/admin`   | CRUD users, products, samples, test sessions; import datasets; export results |
| panelis  | `/panelis` | Dashboard + submit assessment scores for assigned test sessions |
| penyelia | `/penyelia` | View test session results, monitoring, PDF/Excel export |

### Domain Model Flow

```
Product → Sample → TestSession → Assessment (per panelis) → AssessmentDetail (per criteria)
                ↗ SessionUser (links panelis/penyelia to a test session)
AssessmentTemplate → AssessmentSection → Criteria → CriteriaOption (scoring scale)
```

- `Product` → `Sample` → `TestSession` → `Assessment` → `AssessmentDetail`
- `AssessmentTemplate` → `AssessmentSection` → `Criteria` → `CriteriaOption` (scoring scale: 1–9)
- `SessionUser` links panelis/penyelia users to test sessions (polymorphic-like via `role` column)

### Seeders Must Run in Order

`DatabaseSeeder` calls these sequentially. Each depends on the previous:

1. `UserSeeder` → 2. `ProductSeeder` → 3. `AssessmentTemplateSeeder` → 4. `AssessmentSectionSeeder` → 5. `CriteriaSeeder` → 6. `CriteriaOptionSeeder`

### Key Directories

- `app/Http/Controllers/Admin/` — admin CRUD controllers
- `app/Http/Controllers/Panelis/` — panelis assessment flow
- `app/Http/Controllers/Penyelia/` — penyelia viewing/exports
- `app/Exports/` — Maatwebsite Excel exports + `Pdf/` subdirectory for DomPDF classes
- `app/Imports/` — dataset import classes (regular + organoleptic)
- `resources/views/` — Blade templates organized by role (`admin/`, `panelis/`, `penyelia/`)
- `database/migrations/` — 24 migrations, latest dated 2026-09-08

### Dev Environment

- Local dev assumes **Laragon** on Windows (project lives in `C:\laragon\www\`).
- Default DB: **SQLite** (`database/database.sqlite`). MySQL config exists but commented out.
- Queue connection: `database` in `.env.example`, `sync` in test.
- `composer dev` runs 4 processes concurrently via `npx concurrently`: artisan serve, queue:listen, pail, vite.

### Important Quirks

- `RoleMiddleware` calls `dd()` on role mismatch instead of redirecting — this is a debug leftover, not a 403 page.
- `UserController.php.bak` exists in `Admin/` — stale backup file, ignore it.
- `Criteria` model explicitly sets `$table = 'criteria'` (Laravel would default to `criterias`).
- Assessment scoring uses a 1–9 scale (not 1–5 or 1–10). Values: 1,3,5,6,7,8,9 (no 2 or 4).
- Statistik hasil pengujian dihitung satu tempat: `App\Support\OrganolepticStatistics::calculate($testSession)` — P = rata-rata `nilai_akhir`, s² = VAR.S (pembagi n-1), s = √s², P min/max = P ± 1.96·s/√n, Konstanta = 1.96, `p_bulat` = P dibulatkan ke kelipatan 0.5 (dipakai untuk NILAI AKHIR MUTU di Excel/PDF/admin/penyelia). Dipakai konsisten di Excel & PDF admin/penyelia serta halaman web (jangan hitung manual ulang per view).
- `App\Imports\DatasetImport` membaca score sheet produk yang diupload admin: deteksi kolom bernilai dari header `Nilai` (bisa kolom B pada format kompak atau kolom C pada format lebar), mengenali group/section (kolom A huruf) dan kriteria (kolom A angka, nama bisa di kolom A atau B). Blok keterangan sel yang digabung vertikal (merged cells) dibagi baris-per-baris ke masing-masing score. Saat import, section/kriteria/opsi lama yang belum dipakai hasil penilaian dihapus agar data selalu sesuai dataset terbaru; kriteria yang sudah direferensikan `assessment_details` dibiarkan utuh.
- Status `TestSession` otomatis menjadi `selesai` saat semua panelis (sessionUsers role=`panelis`) sudah mengirim penilaian. Dipicu saat panelis menyimpan (`Panelis\AssessmentController::store` via `TestSession::selesaikanOtomatisJikaLengkap()`), dan direkonsiliasi di banyak view admin & penyelia (`Admin\TestSessionController` `index`/`results`, `Penyelia\TestSessionController::show`, `Penyelia\MonitoringController::index`, `Penyelia\DashboardController::index`).
- `.editorconfig`: 4-space indent, LF line endings, UTF-8.
- No `.env` file committed — copy from `.env.example` for setup.
- `public/build/` (Vite output) is gitignored.
- Export PDFs use A4 landscape orientation via DomPDF.
