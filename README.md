# Global Chain Platform

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-18.x-20232A?style=for-the-badge&logo=react&logoColor=61DAFB)
![Inertia.js](https://img.shields.io/badge/Inertia.js-Modern%20Monolith-9553E9?style=for-the-badge)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

**Global Chain** is an enterprise-scale, interactive, data-driven web application designed for global supply chain visibility, logistics tracking, and geopolitical risk intelligence. Built with a modern monolithic architecture using **Laravel 11, React (via Inertia.js), and Tailwind CSS**, this platform serves as an advanced command center for importers, exporters, and logistics administrators.

---

## Core Features

- **Real-Time Vessel Tracking**
  Interactive global maps built with Leaflet.js and OpenStreetMap. Monitor live ship movements, speed, heading, ETA, and dynamically redirect vessels across maritime trunk routes (e.g., Malacca Strait, Suez Canal, Trans-Pacific).
  
- **Macroeconomic Visualization**
  Live historical data integration via the **World Bank Open Data API**. Track GDP Growth, Inflation Rates, and Exchange Rate trends (LCU/USD) with beautiful interactive charts using Chart.js.

- **Currency Impact Dashboard**
  Real-time foreign exchange spot rates and historical trend analysis powered by the **Frankfurter API (ECB)**, enabling operational hedging and daily currency conversion.

- **Risk Scoring Engine**
  A sophisticated algorithmic engine that calculates supply chain risks for every country based on:
  - **Weather Risk:** Real-time storm and typhoon warnings (Open-Meteo API).
  - **Economic & Currency Risk:** Inflation and FX volatility.
  - **Political Risk:** News sentiment analysis.

- **Logistics & Trade Intelligence**
  A live news feed aggregating global supply chain, logistics, and macroeconomic news via **Google News RSS**, enhanced with high-resolution dynamic thumbnails generated from the **Pexels Image CDN**.

- **Enterprise Administration**
  Comprehensive admin panel for user roles, robust system configuration, and a transparent "API Integrations" health-check monitor.

---

## API Integrations

Global Chain relies on a robust ecosystem of external data providers. The platform seamlessly integrates **10 distinct APIs**:

1. **Open-Meteo API** (Weather Risk) - Free weather forecasts and historical data.
2. **World Bank API** (Intelligence) - Country GDP, macroeconomic indicators.
3. **REST Countries API** (Intelligence) - Baseline sovereign state data.
4. **Frankfurter / ExchangeRate API** (Financial) - Real-time currency exchange rates.
5. **Marine Traffic Alternative / Datalastic** (Vessel Tracking) - AIS vessel location data.
6. **OpenStreetMap** (Mapping) - Geographic data and basemap tiles.
7. **Google News RSS API** (Intelligence) - Real-time global news aggregation.
8. **Pexels Image API** (UI/UX) - Dynamic high-res thumbnail generation.
9. **World Ports Dataset [GitHub Raw]** (Dataset) - Comprehensive global port coordinates.
10. **UI Avatars API** (UI/UX) - Dynamic user profile picture generation.

---

## Architecture & Scale

- **Scale:** 15-20+ database tables, 30+ endpoints.
- **Pattern:** Adheres to the **Service-Repository Pattern** to keep controllers incredibly lean while handling complex business logic (like Risk Scoring and Route Optimization) in dedicated service classes.
- **Frontend Paradigm:** Single Page Application (SPA) feel with Server-Side Routing via **Inertia.js**, eliminating the need for complex state management APIs (like Redux or separate REST/GraphQL setups).

---

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

### Installation

1. **Clone the repository & Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your `DB_*` settings inside the `.env` file.*

3. **Database Migration & Seeding:**
   This project relies heavily on seeded data for the initial simulation (Vessels, Ports, Countries, News).
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Build Frontend Assets:**
   ```bash
   npm run build
   # Or for active development: npm run dev
   ```

5. **Serve the Application:**
   ```bash
   php artisan serve
   ```

---

## Roles & Authentication

The default seeder provides the following accounts:
- **Admin:** `admin@globalsupply.com` (password: `password`)
- **Importer:** `importer@acme.com` (password: `password`)
- **Exporter:** `exporter@zenith.com` (password: `password`)

Enjoy tracking the global supply chain! 
