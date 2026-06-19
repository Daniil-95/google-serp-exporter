# =============================================================================
# Dockerfile pro Google SERP Exporter
# =============================================================================
# Pouziva PHP 8.4 CLI jako zaklad, protoze aplikace bezi na vestavenem PHP
# serveru (php -S 0.0.0.0:8000 -t www).
# =============================================================================

FROM php:8.4-cli-alpine

# ---------------------------------------------------------------------------
# Nakladani s promennymi prostredi
# ---------------------------------------------------------------------------
ENV COMPOSER_ALLOW_SUPERUSER=1

# ---------------------------------------------------------------------------
# Instalace systemovych zavislosti a PHP rozsireni
# ---------------------------------------------------------------------------
#   pdo_sqlite + sqlite3  - pripojeni k SQLite databazi
#   intl                  - internacionalizace (vyzaduje Nette\Utils\DateTime)
#   mbstring              - prace s vicobytovymi retezci
#   zip                   - composer vyzaduje pro stahovani balicku
#   curl, git, unzip      - composer a pripadne dalsi nastroje
# ---------------------------------------------------------------------------
RUN apk add --no-cache \
        curl \
        git \
        unzip \
        sqlite \
    && docker-php-ext-install \
        pdo_sqlite \
        sqlite3 \
        intl \
        mbstring \
        zip \
        curl

# ---------------------------------------------------------------------------
# Instalace Composeru (oficialni image)
# ---------------------------------------------------------------------------
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# ---------------------------------------------------------------------------
# Pracovni adresar uvnitr kontejneru
# ---------------------------------------------------------------------------
WORKDIR /var/www/html

# ---------------------------------------------------------------------------
# Kopie souboru projektu (kompletni zdrojove kody)
# ---------------------------------------------------------------------------
COPY . .

# ---------------------------------------------------------------------------
# Vytvoreni adresaru pro bez runtime (temp, log, storage)
# ---------------------------------------------------------------------------
RUN mkdir -p /var/www/html/temp /var/www/html/log /var/www/html/storage \
    && chmod -R 777 /var/www/html/temp /var/www/html/log /var/www/html/storage

# ---------------------------------------------------------------------------
# Instalace PHP zavislosti (composer install)
# ---------------------------------------------------------------------------
RUN composer install --no-interaction --optimize-autoloader

# ---------------------------------------------------------------------------
# Vystaveni portu, na kterem aplikace bezi
# ---------------------------------------------------------------------------
EXPOSE 8000

# ---------------------------------------------------------------------------
# Pri startu kontejneru spustime vestaveny PHP server
# ---------------------------------------------------------------------------
# Poznamka: Pouzivame 0.0.0.0 misto localhost, aby byl server dostupny
#           i mimo kontejner (Docker mapuje porty na hostitele).
# ---------------------------------------------------------------------------
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html/www"]
