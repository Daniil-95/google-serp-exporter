# Google SERP Exporter

Webová aplikace pro získávání, ukládání a export výsledků vyhledávání z Google SERP.

## Přehled

Google SERP Exporter umožňuje zadat klíčové slovo, získat výsledky vyhledávání a exportovat je do strukturovaného formátu. Projekt je postaven na frameworku Nette a využívá čistou architekturu s oddělenou prezentační a servisní vrstvou.

## Funkce

* Vyhledávání podle klíčového slova
* Získání organických výsledků vyhledávání
* Export výsledků do formátu JSON
* Uložení historie vyhledávání do SQLite databáze
* Jednotkové testy pomocí PHPUnit
* Připravená servisní vrstva pro integraci externích API

## Technologie

* PHP 8.4
* Nette Framework 3.x
* SQLite
* PHPUnit
* Composer
* SCSS
* JavaScript (ES6)

## Požadavky

* PHP 8.4 nebo novější
* Composer 2.x

## Instalace

```bash
composer install
```

## Spuštění aplikace

Vestavěný PHP server:

```bash
php -S localhost:8000 -t www
```

Aplikace bude dostupná na:

```text
http://localhost:8000
```

## Spuštění testů

```bash
vendor/bin/phpunit
```

## Struktura projektu

```text
app/
├── Model/
│   ├── Entity/
│   └── Service/
├── Presentation/
│   └── Home/
└── Bootstrap.php

config/
storage/
tests/

www/
├── css/
├── js/
└── scss/
```

## Architektura

Projekt využívá oddělení prezentační a aplikační vrstvy:

* **Presenter** zpracovává HTTP požadavky a formuláře.
* **SearchService** poskytuje rozhraní pro získávání výsledků vyhledávání.
* **MockSearchService** slouží pro lokální vývoj a testování.
* Datová vrstva je připravena pro ukládání výsledků do SQLite.

## Roadmap

* Integrace Google Custom Search API
* Export do CSV
* Export do XLSX
* Ukládání historie vyhledávání
* Docker Compose pro lokální vývoj
* GitHub Actions CI/CD
* Pokročilé filtrování výsledků

## Licence

MIT
