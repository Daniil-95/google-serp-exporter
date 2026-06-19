# Google SERP Exporter

Webová aplikace pro získávání, ukládání a export výsledků vyhledávání z Google.

## Funkce

* Vyhledávání podle klíčového slova
* Získání organických výsledků vyhledávání
* Export výsledků do formátu JSON
* Uložení historie vyhledávání do SQLite databáze
* Automatizované testy pomocí PHPUnit

## Použité technologie

* PHP 8.2
* Nette Framework
* SQLite
* PHPUnit
* Composer

## Požadavky

* PHP 8.2 nebo novější
* Composer

## Instalace

```bash
composer install
```

## Spuštění aplikace

```bash
php -S localhost:8000 -t www
```

Aplikace bude dostupná na adrese:

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
├── Services/
└── Presentation/

storage/
tests/
www/
```

## Plánovaný rozvoj

* Integrace Google Search API
* Rozšířené možnosti exportu
* Historie vyhledávání
* Docker Compose pro lokální vývoj
* CI/CD pipeline

```
```
