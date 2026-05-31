````md id="r7d3wm"
# RISK MASTER — CLAUDE.md

## Project Overview
Risk Master is a Laravel 11 Decision Support System (SPK) for information security risk mitigation using the EDAS (Evaluation Based on Distance from Average Solution) method.

Domain:
- Campus integrated service network security
- Information security risk assessment
- Multi-criteria decision support system

Main architecture:
- Single Laravel project for Web + REST API
- Blade frontend + Tailwind CSS v4
- Flutter mobile planned via API
- Shared business logic through Service classes

---

## File Access Rules

Unless necessary:
- do not scan vendor/
- do not scan node_modules/
- do not scan storage/logs/
- avoid unrelated Blade files
- prioritize explicitly mentioned files

# Tech Stack

Backend:
- Laravel 13
- PHP 8+
- MySQL / SQLite (development)

Frontend:
- Blade
- Tailwind CSS v4
- Vite

Authentication:
- Laravel Breeze
- Laravel Sanctum

Export:
- DomPDF
- Laravel Excel

---

# Architecture Rules

IMPORTANT:
- routes/web.php → Blade + session auth
- routes/api.php → API + Sanctum auth
- Controllers must stay thin
- Business logic belongs in Services
- Shared logic between Web and API

Do NOT:
- duplicate query logic
- place calculation logic inside controllers
- place EDAS logic inside Blade

Preferred:
- reusable scopes
- eager loading
- transactions for critical operations
- observer-based logging

---

# Existing Project Structure

## Current Main Directories

```txt
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Resources/
├── Models/
├── Observers/
├── Providers/
├── Services/
└── Traits/

resources/views/
├── assessments/
└── components/

database/
├── migrations/
└── seeders/
````

---

# Existing Models

## Assessment

Core model for EDAS workflow.

Relationships:

```php
owner()
alternatives()
alternativeValues()
edasResults()
rankedResults()
auditLogs()
```

Methods:

```php
isDraft()
isCompleted()
markAsCompleted()
isMatrixComplete()
```

Scopes:

```php
draft()
completed()
forUser()
withSummary()
withFullDetail()
withResults()
```

---

## Criteria

Constants:

```php
TYPE_BENEFIT
TYPE_COST
```

Methods:

```php
isBenefit()
```

---

## Alternative

Constants:

```php
SOURCE_ADMIN
SOURCE_USER
```

---

## EdasResult

Notes:

* uses float casts
* no timestamps
* calculated_at only

Accessors:

```php
as_score_formatted
quality_label
quality_color
```

---

## AuditLog

Uses:

```php
AuditLog::record(...)
```

Only stores:

* created_at

---

# Existing Services

## EdasService

Main file:

```txt
app/Services/EdasService.php
```

Public methods:

```php
calculate(Assessment $assessment)
calculateRaw(array $matrix, Collection $criteria)
```

Responsibilities:

* build decision matrix
* calculate AV
* calculate PDA/NDA
* calculate SP/SN
* normalize NSP/NSN
* generate AS score
* rank alternatives
* save results
* use DB transaction

IMPORTANT:

* calculateRaw() must stay database-independent for unit testing

---

# Existing API Resources

Located in:

```txt
app/Http/Resources/
```

Current resources:

* AssessmentResource
* EdasResultResource

Use API Resources instead of manual array formatting when possible.

---

# Existing Middleware

## EnsureIsAdmin

Located:

```txt
app/Http/Middleware/EnsureIsAdmin.php
```

Used for admin-only routes.

---

# Existing Observer

## AssessmentObserver

Located:

```txt
app/Observers/AssessmentObserver.php
```

Registered in:

```php
AppServiceProvider::boot()
```

Responsibilities:

* audit logging
* track create/update/delete

---

# Existing Blade Components

Located:

```txt
resources/views/components/
```

Current components:

* criteria-guide-card
* criteria-header
* matrix-cell
* row-status

IMPORTANT:

* prefer reusable Blade components
* avoid duplicated UI fragments

---

# Existing Database Tables

Core tables:

* users
* criteria
* alternatives
* assessments
* assessment_alternatives
* alternative_values
* edas_results
* audit_logs
* personal_access_tokens

Important constraint:

```php
unique([
  'assessment_id',
  'alternative_id',
  'criteria_id'
])
```

---

# EDAS Rules

## Criteria Types

Benefit:

* higher value = better

Cost:

* lower value = better

IMPORTANT:

* C4 (implementation speed) is COST, not benefit

---

# EDAS Formula Flow

1. Build decision matrix
2. Calculate Average Solution (AV)
3. Calculate PDA/NDA
4. Calculate weighted sums
5. Normalize
6. Generate appraisal score
7. Rank alternatives

Formula summary:

```txt
AV_j = (1/m) × Σx_ij

Benefit:
PDA = max(0, x_ij - AV_j) / AV_j
NDA = max(0, AV_j - x_ij) / AV_j

Cost:
PDA = max(0, AV_j - x_ij) / AV_j
NDA = max(0, x_ij - AV_j) / AV_j

AS = (NSP + NSN) / 2
```

Higher AS score = better alternative.

---

# Validation Rules

Before calculation:

* minimum 2 alternatives
* criteria must exist
* decision matrix must be complete
* total weight must equal 1.0 ±0.01

---

# API Rules

All API controllers:

```php
use App\Traits\ApiResponse;
```

Namespace:

```php
App\Http\Controllers\Api\
```

Standard response:

```json
{
  "status": "success",
  "message": "Message",
  "data": {}
}
```

Authentication:

* Sanctum Bearer Token

---

# Coding Conventions

Preferred:

* service-oriented architecture
* explicit relationships
* query scopes
* resource classes
* reusable Blade components
* transaction-safe operations

Avoid:

* fat controllers
* duplicated queries
* business logic in Blade
* inline SQL unless necessary

---

# Current Development Progress

Already implemented:

* Models
* Migrations
* Seeders
* EdasService
* API Resources
* Admin middleware
* Assessment observer
* Tailwind v4 setup
* Reusable Blade components
* Basic assessment views

Not fully implemented:

* Full CRUD API
* Full Blade dashboard
* Report export
* Flutter app
* Comprehensive testing

---

# Important Notes

* Seeders use upsert()
* EdasResult does not use timestamps
* AuditLog only stores created_at
* calculateRaw() is designed for PHPUnit testing
* Shared business logic is mandatory between API and Web
* Use eager loading whenever possible

```
```
