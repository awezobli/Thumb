# flavor-flavor-flavor — Szablon WordPress/WooCommerce dla sklepu jednoproduktowego

Motyw WordPress zaprojektowany wyłącznie dla sklepów sprzedających **jeden produkt**. Cała sprzedaż jest maksymalnie uproszczona: klient widzi produkt i od razu przechodzi do transakcji — bez koszyka, bez zbędnych kroków.

## Funkcjonalności

- **Landing page = strona produktu** — cała strona główna to lejek sprzedażowy
- **Bez koszyka** — przycisk "Kup teraz" prowadzi bezpośrednio do checkout
- **Mobile-first** — responsywny design z priorytetem na urządzenia mobilne
- **Sticky CTA** — przycisk zakupu przyklejony do dołu ekranu na mobile
- **8 konfigurowalnych sekcji** — wszystko edytowalne z Customizera WordPress
- **Uproszczony checkout** — minimalna liczba pól, trust badges
- **SEO** — JSON-LD structured data, FAQ schema
- **Szybkość** — brak jQuery, minimalne zależności, lazy loading
- **Kolory** — w pełni konfigurowalne z poziomu panelu WordPress
- **Polskie tłumaczenia** — gotowy do użycia w Polsce (BLIK, polskie etykiety)

## Sekcje landing page

1. **Hero** — zdjęcie produktu, nagłówek, cena, CTA, gwiazdki, gwarancje
2. **Wideo** — YouTube, Vimeo lub plik MP4
3. **Korzyści** — do 6 korzyści z ikonami
4. **Porównanie** — before/after (stary sposób vs. nasz produkt)
5. **Opinie klientów** — z WooCommerce lub własne
6. **Jak to działa** — 3 proste kroki
7. **FAQ** — accordion z FAQ schema (do 8 pytań)
8. **Gwarancja** — finalne CTA z trust badges

## Wymagania

- WordPress 6.0+
- WooCommerce 7.0+
- PHP 7.4+

## Instalacja

### 1. Przesłanie motywu

**Opcja A — przez panel WordPress:**
1. Spakuj katalog `flavor-flavor-flavor/` do pliku ZIP
2. Wejdź w `Wygląd → Motywy → Dodaj nowy → Wyślij motyw`
3. Wybierz plik ZIP i kliknij "Zainstaluj"
4. Aktywuj motyw

**Opcja B — przez FTP/SFTP:**
1. Prześlij katalog `flavor-flavor-flavor/` do `wp-content/themes/`
2. Wejdź w `Wygląd → Motywy`
3. Aktywuj motyw "flavor-flavor-flavor"

### 2. Konfiguracja WooCommerce

1. Upewnij się, że WooCommerce jest zainstalowane i aktywne
2. Dodaj produkt w `Produkty → Dodaj nowy`
3. Ustaw cenę, zdjęcia (główne + galeria), opis
4. Włącz opinie produktu jeśli chcesz je wyświetlać

### 3. Konfiguracja motywu

Wejdź w **Wygląd → Dostosuj → Sklep jednoproduktowy** i skonfiguruj:

| Sekcja | Co ustawić |
|---|---|
| **Produkt** | ID produktu (lub 0 dla najnowszego) |
| **Sekcja Hero** | Nagłówek, podnagłówek, tekst CTA, plakietka, gwarancje |
| **Sekcja Wideo** | URL wideo (YouTube/Vimeo/MP4) |
| **Sekcja Korzyści** | Do 6 korzyści (ikona + tytuł + opis) |
| **Sekcja Porównanie** | Wady starego sposobu vs. zalety produktu |
| **Sekcja Opinie** | Źródło (WooCommerce lub własne), do 6 opinii |
| **Sekcja "Jak to działa"** | 3 kroki (ikona + tytuł + opis) |
| **Sekcja FAQ** | Do 8 pytań i odpowiedzi |
| **Sekcja Gwarancja** | Tytuł, opis, liczba dni |
| **Checkout** | Tekst przycisku, strona "Dziękujemy", program poleceń |
| **Kolory** | Kolor główny, akcent, tekst, tło |
| **Dane firmy** | Nazwa, email, regulamin, polityka prywatności |

### 4. Konfiguracja płatności

1. Wejdź w `WooCommerce → Ustawienia → Płatności`
2. Włącz metody płatności (Stripe, Przelewy24, PayU, itp.)
3. Dla polskiego rynku zalecamy: **BLIK + Karta + Przelew online**

### 5. Ustawienia WooCommerce (zalecane)

Motyw automatycznie wymusza te ustawienia:
- ✅ Checkout jako gość (bez rejestracji)
- ✅ Przekierowanie z koszyka na checkout
- ✅ Przekierowanie ze strony produktu na stronę główną
- ✅ Przekierowanie ze strony sklepu na stronę główną

Ręcznie ustaw:
- `WooCommerce → Ustawienia → Wysyłka` — skonfiguruj darmową dostawę
- `WooCommerce → Ustawienia → Ogólne` — waluta PLN, lokalizacja polska

## Struktura plików

```
flavor-flavor-flavor/
├── style.css                          # Metadane motywu
├── functions.php                      # Setup, AJAX, helpers
├── header.php                         # Nagłówek + sticky header
├── footer.php                         # Stopka + sticky mobile CTA
├── front-page.php                     # Landing page (strona główna)
├── index.php                          # Fallback template
├── page.php                           # Szablon stron (regulamin, etc.)
├── assets/
│   ├── css/
│   │   ├── theme.css                  # Główny CSS (mobile-first)
│   │   └── checkout.css               # Style checkout
│   └── js/
│       └── theme.js                   # JS (AJAX, accordion, sticky)
├── inc/
│   ├── customizer.php                 # Ustawienia Customizera
│   └── woocommerce.php                # Integracja WooCommerce
├── template-parts/
│   ├── hero.php                       # Sekcja hero
│   ├── video.php                      # Sekcja wideo
│   ├── benefits.php                   # Sekcja korzyści
│   ├── testimonials.php               # Sekcja opinii
│   ├── comparison.php                 # Sekcja porównania
│   ├── how-it-works.php               # Sekcja "Jak to działa"
│   ├── faq.php                        # Sekcja FAQ
│   └── guarantee.php                  # Sekcja gwarancji
├── woocommerce/
│   └── checkout/
│       └── review-order.php           # Uproszczone podsumowanie checkout
└── README.md
```

## Kompatybilność z hostingami

Motyw działa na każdym hostingu obsługującym WordPress + WooCommerce:

| Hosting | Kompatybilność |
|---|:---:|
| nazwa.pl | ✅ |
| home.pl | ✅ |
| LH.pl | ✅ |
| cyberfolks.pl | ✅ |
| OVH | ✅ |
| SiteGround | ✅ |
| Bluehost | ✅ |
| DigitalOcean (WordPress droplet) | ✅ |

**Minimalne wymagania hostingu:**
- PHP 7.4+ (zalecane 8.1+)
- MySQL 5.7+ lub MariaDB 10.3+
- HTTPS (SSL) — wymagane dla płatności
- Pamięć PHP: min. 128 MB (zalecane 256 MB)

## Optymalizacja wydajności

Motyw jest zoptymalizowany pod szybkość:
- Zero zależności od jQuery (vanilla JS)
- CSS: ~15 KB (minified)
- JS: ~3 KB (minified)
- Lazy loading obrazów
- IntersectionObserver zamiast scroll events
- Minimalna liczba HTTP requests

**Zalecane dodatkowe optymalizacje:**
- Zainstaluj plugin cache (WP Super Cache, LiteSpeed Cache, W3 Total Cache)
- Użyj CDN (CloudFlare — darmowy plan wystarczy)
- Optymalizuj obrazy (ShortPixel, Imagify)
- Włącz GZIP/Brotli na serwerze

## Licencja

GPL v2 or later
