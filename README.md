# Google SERP Exporter

Webová aplikace vytvořená v PHP a Nette Frameworku pro získávání organických výsledků vyhledávání Google a jejich export do JSON formátu.

## Přehled

Google SERP Exporter umožňuje zadat klíčové slovo, získat organické výsledky z první stránky Google a následně je exportovat do strojově čitelného JSON souboru.

Aplikace byla vytvořena jako technické zadání a klade důraz na jednoduchou architekturu, čistý kód a přehledné uživatelské rozhraní.

## Funkce

* Vyhledávání podle klíčového slova
* Získání organických výsledků z první stránky Google
* Zobrazení výsledků v moderním uživatelském rozhraní
* Export výsledků do JSON souboru
* Historie posledních vyhledávání
* Uložení výsledků do Session pro následný export
* Ochrana formuláře pomocí CSRF
* Jednotkové testy pomocí PHPUnit
* Docker Compose pro lokální spuštění

## Použité technologie

* PHP 8.3
* Nette Framework 3.x
* Latte Templates
* PHPUnit
* Composer
* SCSS
* JavaScript (ES6)
* Docker & Docker Compose
* SerpApi

## Požadavky

* PHP 8.3 nebo novější
* Composer 2.x

## Instalace

```bash
composer install
```

## Konfigurace

Pro reálné vyhledávání je potřeba API klíč pro SerpApi.

1. Zkopírujte soubor:

```bash
cp .env.example .env
```

2. Doplňte vlastní API klíč:

```dotenv
SERPAPI_API_KEY=your_api_key
```

API klíč lze získat na:

https://serpapi.com

## Spuštění aplikace

### Lokálně

```bash
php -S localhost:8000 -t www
```

Aplikace bude dostupná na:

```text
http://localhost:8000
```

### Docker Compose

```bash
docker compose up --build
```

Po spuštění bude aplikace dostupná na stejné adrese:

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
├── FrontModule/
│   └── Presenters/
├── Model/
│   ├── Entity/
│   └── Service/

config/
tests/
www/
```

## Architektura

### HomePresenter

Zpracovává:

* vyhledávací formulář
* historii vyhledávání
* export JSON
* práci se session

### SearchService

Definuje rozhraní pro získávání výsledků vyhledávání.

### GoogleSearchService

Implementace komunikace se službou SerpApi.

### SearchResult

Datový objekt reprezentující jeden výsledek vyhledávání.

## Bezpečnost

Aplikace obsahuje:

* CSRF ochranu formuláře
* validaci vstupních dat
* oddělení prezentační a servisní vrstvy
* ukládání API klíčů mimo zdrojový kód pomocí `.env`

## Budoucí rozšíření

* Export do CSV
* Export do XLSX
* Pokročilé filtrování výsledků
* Ukládání historie do databáze
* GitHub Actions CI/CD

## Licence

MIT
