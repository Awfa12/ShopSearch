# 🛍️ ShopSearch - High-Performance E-Commerce Search Platform

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Meilisearch](https://img.shields.io/badge/Meilisearch-Latest-FF6B35?style=for-the-badge)
![Livewire](https://img.shields.io/badge/Livewire-3.7-4E56A6?style=for-the-badge)
![Filament](https://img.shields.io/badge/Filament-4.0-F59E0B?style=for-the-badge)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**A modern, scalable e-commerce search platform built with Laravel, featuring real-time search across 50,000+ products with typo tolerance and advanced filtering.**

[Features](#-key-features) • [Tech Stack](#-tech-stack) • [Architecture](#-architecture) • [Installation](#-installation) • [Demo](#-demo)

</div>

---

## 📋 Table of Contents

-   [Overview](#-overview)
-   [Key Features](#-key-features)
-   [Tech Stack](#-tech-stack)
-   [Architecture](#-architecture)
-   [Installation](#-installation)
-   [Project Structure](#-project-structure)
-   [Key Achievements](#-key-achievements)
-   [Screenshots](#-screenshots)
-   [Future Enhancements](#-future-enhancements)

---

## 🎯 Overview

ShopSearch is a full-stack e-commerce search platform demonstrating modern web development practices. The application handles **50,000+ products** with sub-50ms search response times, featuring real-time search, advanced filtering, and a comprehensive admin panel.

### What Makes This Project Special

-   ⚡ **Lightning-fast search** with Meilisearch (sub-50ms response times)
-   🔍 **Typo-tolerant search** - finds "iPhone" when you type "iphoen"
-   🎨 **Real-time UI** with Livewire (no JavaScript needed)
-   🛠️ **Complete admin panel** with Filament
-   🐳 **Dockerized** for easy setup and deployment
-   📊 **Scalable architecture** ready for production

---

## ✨ Key Features

### 🔍 Advanced Search

-   **Real-time search** with 300ms debounce
-   **Typo tolerance** - handles spelling mistakes automatically
-   **Relevance ranking** - most relevant results appear first
-   **Full-text search** across product names and descriptions
-   **Handles 50,000+ products** efficiently

### 🎛️ Advanced Filtering

-   **Category filtering** with hierarchical support
-   **Brand filtering** with searchable dropdowns
-   **Price range filtering** (min/max)
-   **Combined filters** - all filters work together
-   **URL synchronization** - bookmarkable search results

### 🎨 Modern Frontend

-   **Livewire-powered** reactive UI
-   **Tailwind CSS** for beautiful, responsive design
-   **Real-time updates** without page refreshes
-   **Pagination** with 24 products per page
-   **Loading indicators** for better UX

### 🛠️ Admin Panel (Filament)

-   **Product Management** - Full CRUD with Meilisearch sync
-   **Category Management** - Hierarchical structure with parent/child relationships
-   **Brand Management** - Logo support and product counts
-   **Auto-sync to search index** on create/update/delete
-   **Advanced filters and search** in admin tables
-   **Beautiful, modern UI** with badges and icons

### 🐳 Docker Setup

-   **Multi-container architecture** (PHP, Nginx, MySQL, Redis, Meilisearch)
-   **One-command setup** with docker-compose
-   **Development-ready** with hot reload
-   **Production-ready** configuration

---

## 🛠️ Tech Stack

### Backend

-   **Laravel 12** - Modern PHP framework
-   **PHP 8.4** - Latest PHP version
-   **MySQL 8.0** - Relational database
-   **Redis** - Caching and session storage
-   **Meilisearch** - Fast, typo-tolerant search engine

### Frontend

-   **Livewire 3.7** - Full-stack reactive framework
-   **Tailwind CSS** - Utility-first CSS framework
-   **Alpine.js** - Lightweight JavaScript framework (via Livewire)

### Admin Panel

-   **Filament 4.0** - Modern admin panel builder
-   **Auto-generated CRUD** interfaces
-   **Custom forms and tables**

### DevOps

-   **Docker** - Containerization
-   **Docker Compose** - Multi-container orchestration
-   **Nginx** - Web server
-   **PHP-FPM** - PHP process manager

### Search & Performance

-   **Laravel Scout** - Search abstraction layer
-   **Meilisearch** - Search engine with typo tolerance
-   **Eager loading** - Optimized database queries
-   **Batch operations** - Efficient data seeding

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        User Browser                           │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│                    Nginx (Port 8000)                         │
│              - Static file serving                           │
│              - PHP-FPM proxy                                 │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              Laravel Application (PHP 8.4)                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │  Livewire    │  │   Filament    │  │   Scout       │    │
│  │  Components  │  │   Admin       │  │   Search      │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
└───────┬──────────────────┬──────────────────┬───────────────┘
        │                  │                  │
        ▼                  ▼                  ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   MySQL      │  │   Redis      │  │ Meilisearch  │
│  (Products)  │  │   (Cache)     │  │  (Search)    │
└──────────────┘  └──────────────┘  └──────────────┘
```

### Data Flow

1. **User searches** → Livewire component captures input
2. **Livewire sends** → AJAX request to Laravel
3. **Laravel queries** → Meilisearch via Scout
4. **Meilisearch returns** → Ranked, filtered results (<50ms)
5. **Laravel renders** → Updated HTML via Livewire
6. **Browser updates** → Only changed DOM elements

---

## 🚀 Installation

### Prerequisites

-   Docker Desktop installed
-   Git
-   Node.js (for building assets)

### Quick Start

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/ShopSearch.git
    cd ShopSearch
    ```

2. **Start Docker containers**

    ```bash
    docker-compose -f docker/docker-compose.yml up -d
    ```

3. **Install PHP dependencies**

    ```bash
    docker-compose -f docker/docker-compose.yml exec php composer install
    ```

4. **Set up environment**

    ```bash
    cp .env.example .env
    docker-compose -f docker/docker-compose.yml exec php php artisan key:generate
    ```

5. **Run migrations**

    ```bash
    docker-compose -f docker/docker-compose.yml exec php php artisan migrate
    ```

6. **Seed the database**

    ```bash
    docker-compose -f docker/docker-compose.yml exec php php artisan db:seed
    ```

7. **Configure Meilisearch**

    ```bash
    docker-compose -f docker/docker-compose.yml exec php php artisan meilisearch:configure
    ```

8. **Import products to search index**

    ```bash
    docker-compose -f docker/docker-compose.yml exec php php artisan scout:import "App\Models\Product"
    ```

9. **Build frontend assets**

    ```bash
    npm install
    npm run build
    ```

10. **Access the application**
    - Frontend: http://localhost:8000
    - Admin Panel: http://localhost:8000/admin
    - Meilisearch Dashboard: http://localhost:7700

### Default Admin Credentials

Create an admin user:

```bash
docker-compose -f docker/docker-compose.yml exec php php artisan make:filament-user
```

---

## 📁 Project Structure

```
ShopSearch/
├── app/
│   ├── Console/Commands/
│   │   └── ConfigureMeilisearch.php    # Meilisearch configuration
│   ├── Filament/Resources/             # Admin panel resources
│   │   ├── Products/
│   │   ├── Categories/
│   │   └── Brands/
│   ├── Http/Controllers/
│   │   └── SearchController.php         # API search endpoint
│   ├── Livewire/
│   │   └── ProductSearch.php           # Real-time search component
│   └── Models/                         # Eloquent models
├── database/
│   ├── factories/                      # Model factories
│   ├── migrations/                     # Database migrations
│   └── seeders/                        # Database seeders
├── docker/
│   ├── php/
│   │   └── Dockerfile                  # PHP 8.4-FPM with extensions
│   ├── nginx/
│   │   └── default.conf                # Nginx configuration
│   └── docker-compose.yml              # Multi-container setup
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   └── product-search.blade.php # Search UI
│   │   └── components/
│   │       └── layouts/
│   │           └── app.blade.php       # Main layout
│   └── css/app.css                     # Tailwind CSS
└── routes/
    └── web.php                          # Application routes
```

---

## 🎓 Key Achievements

### Performance Optimizations

-   ✅ **Batch database inserts** - Seeded 50,000 products efficiently
-   ✅ **Eager loading** - Prevents N+1 query problems
-   ✅ **Meilisearch indexing** - Sub-50ms search response times
-   ✅ **Optimized filters** - Native Meilisearch filter syntax

### Technical Challenges Solved

-   ✅ **Typo-tolerant search** - Handles spelling mistakes automatically
-   ✅ **Real-time UI** - Built with Livewire (no JavaScript framework needed)
-   ✅ **Hierarchical categories** - Prevents circular references
-   ✅ **Price range filtering** - Custom Meilisearch filter implementation
-   ✅ **Docker networking** - Proper service communication
-   ✅ **Nginx configuration** - Livewire routes properly handled

### Best Practices Implemented

-   ✅ **Database normalization** - Proper foreign keys and indexes
-   ✅ **Mass assignment protection** - Secure model attributes
-   ✅ **Idempotent seeders** - Safe to run multiple times
-   ✅ **Environment configuration** - Secure .env handling
-   ✅ **Code organization** - Clean architecture and separation of concerns

---

## 📸 Screenshots

### Frontend Search Interface

-   Real-time search with instant results
-   Advanced filtering sidebar
-   Responsive product grid
-   Pagination support

### Admin Panel

-   Product management with Meilisearch sync
-   Category hierarchy management
-   Brand management with logos
-   Beautiful, modern UI

---

## 🔮 Future Enhancements

-   [ ] User authentication and profiles
-   [ ] Shopping cart functionality
-   [ ] Order management system
-   [ ] Product reviews and ratings
-   [ ] Image upload for products
-   [ ] Export/import functionality
-   [ ] Analytics dashboard
-   [ ] API documentation
-   [ ] Unit and feature tests
-   [ ] CI/CD pipeline

---

## 📚 Learning Resources

This project demonstrates:

-   **Docker** containerization and multi-service architecture
-   **Laravel** best practices and modern features
-   **Meilisearch** integration and optimization
-   **Livewire** for reactive UIs without JavaScript
-   **Filament** for rapid admin panel development
-   **Database design** with relationships and indexes
-   **Performance optimization** techniques

---

## 🤝 Contributing

This is a portfolio project, but suggestions and feedback are welcome!

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

**Awfa Abou Assali**

-   LinkedIn: www.linkedin.com/in/awfa-abo-assali-1101b2366
-   Email: awfa.r1212@gmail.com

---

<div align="center">

**Built with ❤️ using Laravel, Livewire, and Meilisearch**

⭐ Star this repo if you find it helpful!

</div>
