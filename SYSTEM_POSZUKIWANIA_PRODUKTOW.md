# System poszukiwania i walidacji produktów do sklepu jednoproduktowego

## Cel systemu

Zautomatyzowany pipeline do **odkrywania, oceniania i testowania produktów** pod kątem sprzedaży w modelu sklepu jednoproduktowego. System składa się z 4 faz:

1. **Odkrywanie** — automatyczne zbieranie sygnałów trendów z wielu źródeł
2. **Scoring** — algorytmiczna ocena potencjału każdego produktu
3. **Walidacja** — szybkie testy rynkowe za minimalny budżet
4. **Decyzja** — dane do podjęcia decyzji: skalować, iterować lub porzucić

---

## Spis treści

1. [Architektura systemu](#1-architektura-systemu)
2. [Faza 1: Odkrywanie produktów](#2-faza-1-odkrywanie-produktów)
3. [Faza 2: Scoring — algorytm oceny](#3-faza-2-scoring--algorytm-oceny)
4. [Faza 3: Walidacja rynkowa](#4-faza-3-walidacja-rynkowa)
5. [Faza 4: Decyzja — matryca Go/No-Go](#5-faza-4-decyzja--matryca-gono-go)
6. [Stack technologiczny](#6-stack-technologiczny)
7. [Automatyzacja i harmonogram](#7-automatyzacja-i-harmonogram)
8. [Budżet i koszty](#8-budżet-i-koszty)
9. [Baza danych produktów — schemat](#9-baza-danych-produktów--schemat)
10. [Dashboard — metryki i widoki](#10-dashboard--metryki-i-widoki)
11. [Przykładowy flow — od pomysłu do decyzji](#11-przykładowy-flow--od-pomysłu-do-decyzji)
12. [Ryzyka i ograniczenia](#12-ryzyka-i-ograniczenia)

---

## 1. Architektura systemu

```
┌──────────────────────────────────────────────────────────────────────┐
│                    SYSTEM POSZUKIWANIA PRODUKTÓW                     │
│                                                                      │
│  ┌─────────────┐   ┌─────────────┐   ┌─────────────┐   ┌─────────┐ │
│  │  FAZA 1     │   │  FAZA 2     │   │  FAZA 3     │   │ FAZA 4  │ │
│  │  ODKRYWANIE │──▶│  SCORING    │──▶│  WALIDACJA  │──▶│ DECYZJA │ │
│  │             │   │             │   │             │   │         │ │
│  │  Źródła     │   │  Algorytm   │   │  Testy      │   │ Go /    │ │
│  │  danych     │   │  oceny      │   │  rynkowe    │   │ No-Go   │ │
│  └─────────────┘   └─────────────┘   └─────────────┘   └─────────┘ │
│        │                  │                  │               │       │
│        ▼                  ▼                  ▼               ▼       │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                    BAZA DANYCH PRODUKTÓW                      │   │
│  │            (PostgreSQL / Supabase / Airtable)                │   │
│  └──────────────────────────────────────────────────────────────┘   │
│        │                                                             │
│        ▼                                                             │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                       DASHBOARD                               │   │
│  │                (ranking, alerty, raporty)                     │   │
│  └──────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 2. Faza 1: Odkrywanie produktów

### 2.1 Źródła danych — gdzie szukać

System zbiera sygnały z 4 kategorii źródeł. Każde źródło ma swój priorytet i częstotliwość odpytywania.

#### Kategoria A: Trendy wyszukiwania (popyt)

| Źródło | Co monitorujemy | Jak | Częstotliwość |
|---|---|---|---|
| **Google Trends** | Rosnące zapytania w kategoriach produktowych | Apify scraper / pytrends | Codziennie |
| **Google Keyword Planner** | Wolumen wyszukiwań, CPC, sezonowość | Google Ads API | Tygodniowo |
| **Pinterest Trends** | Rosnące piny w kategoriach dom, uroda, kuchnia | Scraper / API | Tygodniowo |
| **Amazon Movers & Shakers** | Produkty z największym skokiem sprzedaży (24h) | Scraper | Codziennie |
| **Allegro Trendy** | Rosnące wyszukiwania na polskim rynku | Scraper | Codziennie |

**Sygnał do wyłapania:** Zapytanie rosnie >100% w ciągu 30 dni i nie jest sezonowym powtórzeniem.

#### Kategoria B: Media społecznościowe (wiralowość)

| Źródło | Co monitorujemy | Jak | Częstotliwość |
|---|---|---|---|
| **TikTok Creative Center** | Reklamy z wysokim CTR, viralowe treści produktowe | Apify TikTok Ad Library Scraper | Codziennie |
| **TikTok hashtagi** | #tiktokmademebuyit, #amazonfinds, #allegrofind | Scraper | Codziennie |
| **Instagram Reels** | Treści produktowe z wysokim engagement | Scraper / manual | 3x tydzień |
| **Facebook Ad Library** | Reklamy produktowe działające >14 dni (sygnał sukcesu) | Meta Ad Library API / Apify | Codziennie |
| **Reddit** | r/shutupandtakemymoney, r/BuyItForLife | Reddit API | Codziennie |
| **YouTube** | Recenzje produktów z rosnącą liczbą wyświetleń | YouTube Data API | Tygodniowo |

**Sygnał do wyłapania:** Reklama produktu działa >14 dni na Facebooku (= opłacalna), wideo produktowe >500K wyświetleń na TikTok w <7 dni.

#### Kategoria C: Marketplace'y (sprzedaż)

| Źródło | Co monitorujemy | Jak | Częstotliwość |
|---|---|---|---|
| **AliExpress** | Produkty z rosnącą liczbą zamówień, nowe bestsellery | Apify AliExpress Scraper | Codziennie |
| **Amazon Best Sellers** | Nowe produkty w top 100 kategorii | Scraper | Codziennie |
| **Allegro Bestsellery** | Top sprzedawane produkty w kategoriach | Scraper | Codziennie |
| **Etsy Trending** | Ręcznie robione / unikalne produkty z trendem | Scraper | Tygodniowo |
| **Shopify stores (publiczne)** | Nowe sklepy z rosnącym ruchem | SimilarWeb API / manual | Tygodniowo |

**Sygnał do wyłapania:** Produkt z <6 miesięcy na rynku i >1000 zamówień, rosnąca sprzedaż MoM.

#### Kategoria D: Branżowe i niszowe

| Źródło | Co monitorujemy | Jak | Częstotliwość |
|---|---|---|---|
| **Kickstarter / Indiegogo** | Kampanie przekraczające cel >500% | API / scraper | Tygodniowo |
| **Product Hunt** | Produkty fizyczne z >200 upvotes | API | Tygodniowo |
| **Blogi branżowe** | Listy "best of", "must have" | RSS + NLP | Tygodniowo |
| **Targi (wirtualne/fizyczne)** | Nowości z CES, Canton Fair | Manual | Miesięcznie |
| **Patenty / new filings** | Innowacyjne rozwiązania produktowe | Google Patents | Miesięcznie |

**Sygnał do wyłapania:** Kampania crowdfundingowa >$500K i jeszcze niedostępna w sprzedaży detalicznej.

### 2.2 Pipeline zbierania danych

```
┌───────────────────┐
│  ŹRÓDŁA DANYCH    │
│  (API / Scraping) │
└────────┬──────────┘
         │
         ▼
┌───────────────────┐
│  NORMALIZACJA     │   Ujednolicenie formatu:
│                   │   - nazwa produktu
│                   │   - kategoria
│                   │   - cena (źródłowa + detaliczna)
│                   │   - URL źródła
│                   │   - metryki (zamówienia, views, likes)
│                   │   - data odkrycia
└────────┬──────────┘
         │
         ▼
┌───────────────────┐
│  DEDUPLIKACJA     │   Czy ten produkt już jest w bazie?
│                   │   → Fuzzy matching po nazwie + obrazku
│                   │   → Jeśli tak: aktualizuj metryki
│                   │   → Jeśli nie: dodaj nowy rekord
└────────┬──────────┘
         │
         ▼
┌───────────────────┐
│  WZBOGACENIE      │   Automatyczne uzupełnienie:
│                   │   - cena na AliExpress / dostawcy
│                   │   - kalkulacja marży
│                   │   - dane Google Trends
│                   │   - wolumen wyszukiwań
│                   │   - analiza konkurencji
└────────┬──────────┘
         │
         ▼
┌───────────────────┐
│  SCORING          │   → Faza 2
└───────────────────┘
```

### 2.3 Filtrowanie wstępne (bramy wejścia)

Zanim produkt trafi do scoringu, musi przejść filtry eliminacyjne:

| Filtr | Kryterium | Powód |
|---|---|---|
| Cena zakupu | < 150 PLN | Marża musi być opłacalna |
| Waga | < 2 kg | Koszty wysyłki |
| Legalność | Brak regulacji specjalnych | Bez suplementów, broni, leków |
| Dostępność | Dostawca z <14 dni dostawy | Czas realizacji |
| Fragilność | Odporny na transport | Minimalizacja zwrotów |
| Sezonowość | Nie jest ściśle sezonowy LUB jest teraz w sezonie | Ciągłość sprzedaży |

---

## 3. Faza 2: Scoring — algorytm oceny

### 3.1 Kryteria i wagi

Każdy produkt otrzymuje **Product Score (PS)** w skali 0-100, obliczany jako średnia ważona 10 kryteriów:

```
PS = Σ (waga_i × ocena_i) / Σ waga_i
```

| # | Kryterium | Waga | Skala 1-10 | Jak mierzymy |
|:---:|---|:---:|---|---|
| 1 | **Wzrost popytu** | 10 | 1=spadek, 10=>200% wzrost/30 dni | Google Trends, wolumen wyszukiwań |
| 2 | **Potencjał marży** | 10 | 1=<30%, 10=>80% | Cena zakupu vs. sugerowana cena detaliczna |
| 3 | **Wiralowość** | 9 | 1=brak treści, 10=miliony wyświetleń | TikTok/IG views, shares, UGC |
| 4 | **Demonstrowalność** | 8 | 1=trudno pokazać, 10=efekt „wow" w 5s | Analiza treści wideo, engagement |
| 5 | **Rozwiązanie problemu** | 9 | 1=nice-to-have, 10=must-have | Analiza sentymentu recenzji, pain points |
| 6 | **Konkurencja** | 7 | 1=rynek nasycony, 10=brak konkurencji | Liczba sprzedawców, nasycenie reklam |
| 7 | **Prostota logistyki** | 6 | 1=trudna wysyłka, 10=lekki, mały, odporny | Waga, wymiary, fragilność |
| 8 | **Potencjał powtarzalności** | 6 | 1=jednorazowy, 10=subskrypcja | Czy zużywalny / potrzeba uzupełnień |
| 9 | **Unikalność** | 7 | 1=wszędzie dostępny, 10=niszowy/niedostępny | Dostępność na Amazon PL, Allegro |
| 10 | **Engagement reklamowy** | 8 | 1=słabe reklamy, 10=CTR>5%, CPA niski | Dane z Ad Library, benchmarki branżowe |

### 3.2 Automatyzacja scoringu

Część kryteriów mierzona automatycznie, część wymaga oceny manualnej:

| Kryterium | Automatyzacja | Źródło danych |
|---|:---:|---|
| Wzrost popytu | 🟢 Pełna | Google Trends API, KW volume |
| Potencjał marży | 🟢 Pełna | Cena AliExpress vs. cena rynkowa |
| Wiralowość | 🟡 Częściowa | TikTok views + manualna ocena |
| Demonstrowalność | 🔴 Manualna | Ludzka ocena wideo |
| Rozwiązanie problemu | 🟡 Częściowa | NLP na recenzjach + manualna |
| Konkurencja | 🟢 Pełna | Liczba sprzedawców, nasycenie ads |
| Prostota logistyki | 🟢 Pełna | Waga, wymiary z karty produktu |
| Potencjał powtarzalności | 🔴 Manualna | Ocena kategorii produktu |
| Unikalność | 🟡 Częściowa | Scraping Allegro/Amazon + manualna |
| Engagement reklamowy | 🟢 Pełna | Ad Library CTR, czas trwania reklamy |

### 3.3 Progi decyzyjne

| Product Score | Kategoria | Działanie |
|:---:|---|---|
| 0-39 | ⚫ Odrzucony | Archiwizuj, nie wracaj |
| 40-59 | 🟡 Obserwacja | Dodaj do watchlist, monitoruj trendy |
| 60-74 | 🟠 Potencjał | Pogłębiona analiza manualna |
| 75-89 | 🟢 Kandydat | Przejdź do Fazy 3 — walidacja rynkowa |
| 90-100 | 🔵 Top Pick | Natychmiastowa walidacja, priorytet #1 |

### 3.4 Przykładowa karta scoringowa

```
╔══════════════════════════════════════════════════════╗
║  KARTA PRODUKTU: Lampka biurkowa z ładowarką QI     ║
╠══════════════════════════════════════════════════════╣
║                                                      ║
║  Product Score: 78/100          Kategoria: 🟢        ║
║  Data odkrycia: 2026-02-15      Źródło: TikTok       ║
║                                                      ║
║  SCORING DETAIL:                                     ║
║  ────────────────────────────────────────            ║
║  Wzrost popytu (×10)      ████████░░  8/10           ║
║  Potencjał marży (×10)    ███████░░░  7/10           ║
║  Wiralowość (×9)          █████████░  9/10           ║
║  Demonstrowalność (×8)    ████████░░  8/10           ║
║  Rozwiązanie problemu (×9)██████░░░░  6/10           ║
║  Konkurencja (×7)         ██████░░░░  6/10           ║
║  Prostota logistyki (×6)  ████████░░  8/10           ║
║  Powtarzalność (×6)       ███░░░░░░░  3/10           ║
║  Unikalność (×7)          ███████░░░  7/10           ║
║  Engagement reklam (×8)   █████████░  9/10           ║
║                                                      ║
║  DANE RYNKOWE:                                       ║
║  ────────────────────────────────────────            ║
║  Cena zakupu:      35 PLN (AliExpress)               ║
║  Sugerowana cena:  149 PLN                           ║
║  Marża brutto:     76%                               ║
║  Google Trends:    ↑ 180% (30 dni)                   ║
║  TikTok views:     2.3M (top wideo)                  ║
║  Konkurencja PL:   3 sklepy (niska)                  ║
║  Waga:             0.4 kg                            ║
║                                                      ║
║  REKOMENDACJA: → Przejdź do walidacji (Faza 3)      ║
║                                                      ║
╚══════════════════════════════════════════════════════╝
```

---

## 4. Faza 3: Walidacja rynkowa

### 4.1 Cel

Wydać **minimum pieniędzy** (300-1000 PLN), aby uzyskać **maksimum informacji** o realnym popycie, zanim zainwestujesz w zapasy i pełną kampanię.

### 4.2 Metoda A: Smoke Test Landing Page (3-7 dni)

Najszybsza i najtańsza walidacja — strona testowa bez prawdziwego produktu.

```
KROK 1: Budowa strony testowej (2-4h)
────────────────────────────────────
┌─────────────────────────────────┐
│  HERO: Zdjęcie + nagłówek       │
│  KORZYŚCI: 3-4 bullet points   │
│  CTA: "Zamów teraz — 149 PLN"  │
│  (kliknięcie → formularz email) │
│                                 │
│  "Produkt w przygotowaniu.     │
│   Zostaw email — powiadomimy   │
│   Cię jako pierwszego."        │
└─────────────────────────────────┘

Narzędzia: Carrd.co ($19/rok) lub szablon flavor-flavor-flavor
Czas budowy: 2-4 godziny
Koszt: ~0 PLN (domena z Cloudflare ~50 PLN/rok)


KROK 2: Puszczenie ruchu testowego (5-7 dni)
─────────────────────────────────────────────
Platforma:  Facebook/Instagram Ads lub TikTok Ads
Budżet:     200-500 PLN (40-100 PLN/dzień × 5 dni)
Targeting:   Zainteresowania zbliżone do produktu
Kreacja:     Wideo 15-30s (problem → rozwiązanie)
Cel:         Ruch na stronę (Traffic / Landing Page Views)


KROK 3: Pomiar wyników
──────────────────────
```

**Metryki decyzyjne — Smoke Test:**

| Metryka | Próg minimalny | Dobry wynik | Doskonały wynik |
|---|:---:|:---:|:---:|
| CTR reklamy | >1.5% | >3% | >5% |
| CPC (koszt kliknięcia) | <2 PLN | <1 PLN | <0.50 PLN |
| Konwersja CTA (klik w "Zamów") | >6% | >15% | >25% |
| Konwersja email (zostawienie) | >3% | >8% | >15% |
| CPL (koszt na lead) | <15 PLN | <8 PLN | <3 PLN |
| Koszt walidacji | 200-500 PLN | — | — |

**Interpretacja wyników:**

| Wynik | Decyzja |
|---|---|
| CTR <1% i CTA <3% | ❌ Porzuć — brak zainteresowania |
| CTR 1-3% i CTA 3-6% | 🟡 Zmień kreację/nagłówek i testuj ponownie |
| CTR >3% i CTA >6% | 🟠 Obiecujące — przejdź do Metody B |
| CTR >5% i CTA >15% | 🟢 Silny sygnał — przejdź do prawdziwej sprzedaży |

### 4.3 Metoda B: Prawdziwa sprzedaż MVP (7-14 dni)

Pełny test z prawdziwą transakcją i prawdziwym produktem.

```
KROK 1: Pozyskanie produktu (1-3 dni)
──────────────────────────────────────
Opcja A: Zamów 5-10 sztuk z AliExpress (szybka dostawa)
Opcja B: Zamów próbkę od dostawcy na Alibaba
Opcja C: Dropshipping testowy (bez zapasu)
Koszt: 200-500 PLN

KROK 2: Uruchomienie sklepu (4-8h)
───────────────────────────────────
Użyj szablonu flavor-flavor-flavor (WordPress + WooCommerce)
lub Shopify z single-product theme.
Skonfiguruj:
  - Zdjęcia produktu (własne + od dostawcy)
  - Opis z korzyściami
  - Płatności (Stripe + BLIK)
  - Darmowa dostawa
Koszt: 0-150 PLN

KROK 3: Kampania reklamowa (7-14 dni)
──────────────────────────────────────
Platforma:   Facebook/Instagram Ads
Budżet:      500-1500 PLN (50-100 PLN/dzień × 10-14 dni)
Cel:          Konwersje (Purchase)
Kreacje:      Min. 3 warianty wideo/grafik
Targeting:    Broad + 2-3 zestawy zainteresowań
Koszt: 500-1500 PLN

KROK 4: Pomiar wyników
──────────────────────
```

**Metryki decyzyjne — MVP Sale:**

| Metryka | Próg minimalny | Dobry wynik | Doskonały wynik |
|---|:---:|:---:|:---:|
| CTR reklamy | >2% | >4% | >6% |
| CPC | <1.50 PLN | <0.80 PLN | <0.40 PLN |
| Konwersja zakupu (CR) | >1.5% | >3% | >5% |
| CPA (koszt na zakup) | <50% ceny produktu | <30% ceny | <20% ceny |
| ROAS | >1.5× | >2.5× | >4× |
| Współczynnik zwrotów | <10% | <5% | <2% |
| Ocena klientów | >3.5/5 | >4/5 | >4.5/5 |
| Koszt walidacji | 700-2000 PLN | — | — |

### 4.4 Metoda C: Pre-order / Crowdfunding (14-30 dni)

Dla produktów unikatowych lub z wyższą ceną — zbieranie zamówień przed produkcją.

```
KROK 1: Strona pre-order z prawdziwą płatnością
────────────────────────────────────────────────
"Zamów w przedsprzedaży — wysyłka za 3-4 tygodnie"
Płatność 100% lub zaliczka 30-50%
Cel: Zebranie min. 50 zamówień w 14 dni

KROK 2: Kampania + organiczny marketing
────────────────────────────────────────
Budżet: 1000-3000 PLN na reklamy
+ Posty na social media, grupy FB, Reddit

KROK 3: Decyzja
───────────────
>50 zamówień w 14 dni = Go
<20 zamówień w 14 dni = No-Go (zwróć pieniądze)
```

### 4.5 Porównanie metod walidacji

| Aspekt | Metoda A: Smoke Test | Metoda B: MVP Sale | Metoda C: Pre-order |
|---|:---:|:---:|:---:|
| Koszt | 200-500 PLN | 700-2000 PLN | 1000-3000 PLN |
| Czas | 3-7 dni | 7-14 dni | 14-30 dni |
| Trafność | Średnia (65%) | Wysoka (85%) | Bardzo wysoka (90%) |
| Potrzebny produkt | Nie | Tak (5-10 szt.) | Nie |
| Prawdziwa sprzedaż | Nie | Tak | Tak (przedsprzedaż) |
| Najlepsze dla | Szybki filtr wielu pomysłów | Potwierdzenie popytu | Unikalne/drogie produkty |

**Rekomendowany flow:** Metoda A (filtruj 5-10 pomysłów) → Metoda B (testuj top 2-3) → skalowanie zwycięzcy.

---

## 5. Faza 4: Decyzja — matryca Go/No-Go

### 5.1 Matryca decyzyjna

Po walidacji, każdy produkt przechodzi przez matrycę:

```
                    ROAS walidacji
                    (przychód / wydatki na reklamy)

                    < 1.0×    1.0-2.0×    > 2.0×
                 ┌──────────┬──────────┬──────────┐
  Product   >75  │  PIVOT   │  SKALUJ  │ SKALUJ   │
  Score          │  kreację │  OSTROŻNIE│ AGRESYWNIE│
  (Faza 2)      ├──────────┼──────────┼──────────┤
            50-74│  PORZUĆ  │  PIVOT   │  SKALUJ  │
                 │  lub     │  cenę /  │ OSTROŻNIE│
                 │  PIVOT   │  ofertę  │          │
                 ├──────────┼──────────┼──────────┤
            <50  │  PORZUĆ  │  PORZUĆ  │  PIVOT   │
                 │          │          │  (scoring │
                 │          │          │   błędny?)│
                 └──────────┴──────────┴──────────┘
```

### 5.2 Scenariusze decyzyjne

#### ✅ GO — Skaluj

Kryteria spełnione jednocześnie:
- Product Score ≥ 60
- ROAS walidacji ≥ 1.5× (Metoda B)
- Konwersja zakupu ≥ 1.5%
- Współczynnik zwrotów < 10%
- Pozytywny feedback klientów

**Następne kroki:**
1. Zamów partię 50-200 sztuk u dostawcy
2. Negocjuj lepszą cenę hurtową
3. Zwiększ budżet reklamowy do 100-300 PLN/dzień
4. Uruchom retargeting i sekwencję email
5. Zbieraj opinie i UGC od klientów

#### 🔄 PIVOT — Zmień i przetestuj ponownie

Coś nie gra, ale sygnały nie są zerowe:
- Wysoki CTR, ale niska konwersja → Problem z ceną lub stroną
- Niski CTR, ale wysoka konwersja → Problem z kreacją reklamy
- ROAS 0.8-1.5× → Blisko progu — optymalizuj

**Co zmieniać (po kolei):**
1. Kreacja reklamy (nowe wideo/zdjęcia)
2. Nagłówek i copy na stronie
3. Cena (obniż o 10-20% lub dodaj bonus)
4. Targeting (inna grupa docelowa)
5. Platforma (TikTok zamiast FB lub odwrotnie)

#### ❌ NO-GO — Porzuć

- ROAS < 0.5×
- CTR < 1% przy 3+ wariantach kreacji
- Brak zainteresowania na Smoke Test
- Wysokie zwroty (>15%)
- Negatywny feedback

**Działanie:** Archiwizuj dane, wyciągnij wnioski, przejdź do następnego produktu. Nie przywiązuj się emocjonalnie.

---

## 6. Stack technologiczny

### 6.1 Architektura techniczna

```
┌─────────────────────────────────────────────────────────┐
│                    ZBIERANIE DANYCH                      │
│                                                         │
│  ┌─────────┐  ┌─────────┐  ┌──────────┐  ┌──────────┐ │
│  │ Apify   │  │ pytrends│  │ Meta Ad  │  │ Custom   │ │
│  │Scrapers │  │ (Google │  │ Library  │  │ Scrapers │ │
│  │         │  │ Trends) │  │ API      │  │ (Python) │ │
│  └────┬────┘  └────┬────┘  └────┬─────┘  └────┬─────┘ │
│       └────────────┼───────────┼──────────────┘       │
│                    ▼           ▼                        │
│              ┌──────────────────────┐                   │
│              │   n8n / Airflow      │                   │
│              │   (Orkiestracja)     │                   │
│              └──────────┬───────────┘                   │
└─────────────────────────┼───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                 PRZETWARZANIE                            │
│                                                         │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────┐ │
│  │ Normalizacja │  │ Deduplikacja  │  │ Scoring      │ │
│  │ danych       │  │ (fuzzy match) │  │ algorytm     │ │
│  │ (Python)     │  │               │  │ (Python)     │ │
│  └──────┬───────┘  └───────┬───────┘  └──────┬───────┘ │
│         └──────────────────┼─────────────────┘         │
│                            ▼                            │
│              ┌──────────────────────┐                   │
│              │   PostgreSQL         │                   │
│              │   (Supabase/Neon)    │                   │
│              └──────────┬───────────┘                   │
└─────────────────────────┼───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    PREZENTACJA                           │
│                                                         │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────┐ │
│  │ Dashboard    │  │ Alerty        │  │ Raporty      │ │
│  │ (Metabase /  │  │ (Slack/Email/ │  │ (tygodniowe) │ │
│  │  Retool)     │  │  Telegram)    │  │              │ │
│  └──────────────┘  └───────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
```

### 6.2 Stos narzędzi — rekomendacja

| Warstwa | Narzędzie | Koszt/mies. | Alternatywa |
|---|---|:---:|---|
| **Scraping** | Apify (scrapers) | ~$49 | Scrapy (self-hosted, darmowe) |
| **Google Trends** | pytrends (Python) | $0 | Apify Google Trends Scraper |
| **Ad Intelligence** | Apify Ad Library Scraper | ~$10-20 | PipiAds ($77/mies.) |
| **Orkiestracja** | n8n (self-hosted) | $0 | Airflow, Make.com ($9+) |
| **Baza danych** | Supabase (PostgreSQL) | $0 (free tier) | Neon, Airtable |
| **Scoring** | Python (pandas + scikit-learn) | $0 | Google Sheets (proste) |
| **Dashboard** | Metabase (self-hosted) | $0 | Retool ($10), Streamlit ($0) |
| **Alerty** | Telegram Bot API | $0 | Slack webhook, email |
| **Hosting** | VPS (Hetzner 4GB) | ~20 PLN | Railway, Fly.io |

**Łączny koszt:** ~100-300 PLN/mies. (wersja budżetowa)

### 6.3 Uproszczona wersja (start w 1 dzień)

Dla kogoś, kto chce zacząć natychmiast bez budowy systemu:

| Funkcja | Narzędzie | Koszt |
|---|---|:---:|
| Śledzenie trendów | Google Trends (ręcznie) + TikTok Creative Center | $0 |
| Baza produktów | Airtable lub Google Sheets | $0 |
| Scoring | Arkusz z formułami (szablon poniżej) | $0 |
| Ad spy | Facebook Ad Library (ręcznie) | $0 |
| Landing page | Carrd.co | $19/rok |
| Reklamy testowe | Meta Ads Manager | budżet |

---

## 7. Automatyzacja i harmonogram

### 7.1 Harmonogram codziennych zadań

```
06:00  ┌─ CRON: Scraping Google Trends (top rising queries)
       ├─ CRON: Scraping Amazon Movers & Shakers
       └─ CRON: Scraping AliExpress trending

08:00  ┌─ CRON: Scraping TikTok Ad Library (nowe reklamy)
       ├─ CRON: Scraping Facebook Ad Library (reklamy >14 dni)
       └─ CRON: Scraping TikTok hashtagi (#tiktokmademebuyit)

09:00  ┌─ CRON: Normalizacja + deduplikacja nowych rekordów
       ├─ CRON: Wzbogacenie danych (ceny dostawców, wolumeny)
       └─ CRON: Przeliczenie scoringu

09:30  ┌─ ALERT: Telegram/Slack — nowe produkty z Score ≥ 75
       └─ ALERT: Telegram/Slack — produkty z rosnącym trendem (>50%/tyg.)

10:00  ┌─ MANUAL: Przegląd top 5 produktów dnia (15 min)
       └─ MANUAL: Ocena manualna (demonstrowalność, problem)
```

### 7.2 Harmonogram tygodniowy

| Dzień | Zadanie | Czas |
|---|---|:---:|
| Poniedziałek | Przegląd tygodniowego rankingu, wybór kandydatów do walidacji | 30 min |
| Wtorek | Przygotowanie Smoke Test landing page (jeśli nowy kandydat) | 2-4h |
| Środa | Uruchomienie kampanii testowej | 1h |
| Czwartek-Piątek | Monitorowanie wyników, A/B testy kreacji | 30 min/dzień |
| Sobota | Analiza wyników tygodnia, decyzje Go/No-Go | 1h |
| Niedziela | Przegląd nowych trendów weekendowych (TikTok, Reddit) | 30 min |

### 7.3 Pipeline walidacji — przepustowość

```
Tydzień 1:  Smoke Test 3-5 produktów (równolegle)
            Budżet: 200 PLN × 5 = 1000 PLN

Tydzień 2:  MVP Sale top 1-2 produktów z Tygodnia 1
            Budżet: 500-1000 PLN × 2 = 1000-2000 PLN

Tydzień 3:  Skalowanie zwycięzcy LUB nowy cykl
            Budżet: 1500-3000 PLN (jeśli Go)

Przepustowość: ~15-20 produktów ocenionych / miesiąc
               ~4-6 przetestowanych rynkowo / miesiąc
               ~1-2 zwycięzców / kwartał
```

---

## 8. Budżet i koszty

### 8.1 Budżet miesięczny — warianty

#### Wariant budżetowy (1 osoba, side project)

| Pozycja | Koszt/mies. |
|---|---:|
| Narzędzia (Apify + hosting) | 150 PLN |
| Smoke Tests (3-5 × 200 PLN) | 600-1000 PLN |
| MVP Sales (1-2 × 750 PLN) | 750-1500 PLN |
| Próbki produktów | 200-500 PLN |
| **Razem** | **1 700 - 3 150 PLN** |

#### Wariant standardowy (mały zespół)

| Pozycja | Koszt/mies. |
|---|---:|
| Narzędzia (Apify + PipiAds + hosting) | 500 PLN |
| Smoke Tests (8-10 × 300 PLN) | 2400-3000 PLN |
| MVP Sales (3-4 × 1000 PLN) | 3000-4000 PLN |
| Próbki produktów | 500-1000 PLN |
| Asystent VA (częściowy etat) | 2000 PLN |
| **Razem** | **8 400 - 10 500 PLN** |

### 8.2 ROI systemu

Przy założeniu znalezienia 1 zwycięskiego produktu na kwartał:

```
Koszt systemu: ~3000 PLN/mies. × 3 mies. = 9000 PLN
Zwycięski produkt: 30 sprzedaży/dzień × 50 PLN zysku × 30 dni = 45 000 PLN/mies.
ROI w pierwszym miesiącu skalowania: 5× inwestycji
```

---

## 9. Baza danych produktów — schemat

### 9.1 Tabela główna: `products`

```sql
CREATE TABLE products (
    id              SERIAL PRIMARY KEY,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),

    -- Identyfikacja
    name            TEXT NOT NULL,
    name_pl         TEXT,
    slug            TEXT UNIQUE,
    category        TEXT,
    subcategory     TEXT,
    image_url       TEXT,
    source_url      TEXT,
    discovery_source TEXT,       -- 'tiktok', 'google_trends', 'aliexpress', etc.

    -- Ceny i marża
    cost_price      DECIMAL(10,2),      -- cena zakupu u dostawcy
    suggested_price DECIMAL(10,2),      -- sugerowana cena detaliczna
    shipping_cost   DECIMAL(10,2),
    margin_pct      DECIMAL(5,2),       -- obliczona automatycznie

    -- Logistyka
    weight_kg       DECIMAL(5,2),
    supplier_url    TEXT,
    supplier_name   TEXT,
    lead_time_days  INTEGER,

    -- Scoring
    score_demand        SMALLINT CHECK (score_demand BETWEEN 0 AND 10),
    score_margin        SMALLINT CHECK (score_margin BETWEEN 0 AND 10),
    score_virality      SMALLINT CHECK (score_virality BETWEEN 0 AND 10),
    score_demonstrable  SMALLINT CHECK (score_demonstrable BETWEEN 0 AND 10),
    score_problem       SMALLINT CHECK (score_problem BETWEEN 0 AND 10),
    score_competition   SMALLINT CHECK (score_competition BETWEEN 0 AND 10),
    score_logistics     SMALLINT CHECK (score_logistics BETWEEN 0 AND 10),
    score_repeat        SMALLINT CHECK (score_repeat BETWEEN 0 AND 10),
    score_uniqueness    SMALLINT CHECK (score_uniqueness BETWEEN 0 AND 10),
    score_ad_engagement SMALLINT CHECK (score_ad_engagement BETWEEN 0 AND 10),
    product_score       DECIMAL(5,2),   -- obliczony PS (0-100)

    -- Status
    status          TEXT DEFAULT 'discovered',
    -- 'discovered', 'scored', 'watchlist', 'validating',
    -- 'validated_go', 'validated_nogo', 'scaling', 'archived'

    -- Notatki
    notes           TEXT
);
```

### 9.2 Tabela trendów: `product_trends`

```sql
CREATE TABLE product_trends (
    id          SERIAL PRIMARY KEY,
    product_id  INTEGER REFERENCES products(id),
    recorded_at DATE DEFAULT CURRENT_DATE,

    -- Google Trends
    gt_interest         INTEGER,    -- 0-100
    gt_interest_change  DECIMAL(5,2), -- % zmiana vs. poprzedni tydzień

    -- Social media
    tiktok_views        BIGINT,
    tiktok_videos_count INTEGER,
    instagram_posts     INTEGER,

    -- Marketplace
    aliexpress_orders   INTEGER,
    amazon_rank         INTEGER,
    allegro_listings    INTEGER,

    -- Ads
    fb_active_ads       INTEGER,
    fb_longest_ad_days  INTEGER,
    tiktok_active_ads   INTEGER
);
```

### 9.3 Tabela walidacji: `validations`

```sql
CREATE TABLE validations (
    id              SERIAL PRIMARY KEY,
    product_id      INTEGER REFERENCES products(id),
    created_at      TIMESTAMP DEFAULT NOW(),

    -- Konfiguracja testu
    method          TEXT,       -- 'smoke_test', 'mvp_sale', 'preorder'
    platform        TEXT,       -- 'facebook', 'tiktok', 'google'
    budget_planned  DECIMAL(10,2),
    budget_spent    DECIMAL(10,2),
    start_date      DATE,
    end_date        DATE,
    landing_url     TEXT,

    -- Wyniki reklam
    impressions     INTEGER,
    clicks          INTEGER,
    ctr             DECIMAL(5,2),
    cpc             DECIMAL(10,2),

    -- Wyniki konwersji
    cta_clicks      INTEGER,    -- kliknięcia w "Kup teraz"
    cta_rate        DECIMAL(5,2),
    email_signups   INTEGER,    -- smoke test
    purchases       INTEGER,    -- mvp sale
    revenue         DECIMAL(10,2),
    roas            DECIMAL(5,2),
    cpa             DECIMAL(10,2),

    -- Jakość
    return_rate     DECIMAL(5,2),
    avg_rating      DECIMAL(3,1),
    feedback_notes  TEXT,

    -- Decyzja
    decision        TEXT,       -- 'go', 'pivot', 'nogo'
    decision_notes  TEXT
);
```

### 9.4 Widok rankingowy: `v_product_ranking`

```sql
CREATE VIEW v_product_ranking AS
SELECT
    p.id,
    p.name,
    p.category,
    p.product_score,
    p.margin_pct,
    p.status,
    p.discovery_source,
    p.created_at,
    t.gt_interest_change,
    t.tiktok_views,
    t.fb_longest_ad_days,
    v.decision AS last_validation_decision,
    v.roas AS last_validation_roas
FROM products p
LEFT JOIN LATERAL (
    SELECT * FROM product_trends
    WHERE product_id = p.id
    ORDER BY recorded_at DESC LIMIT 1
) t ON true
LEFT JOIN LATERAL (
    SELECT * FROM validations
    WHERE product_id = p.id
    ORDER BY created_at DESC LIMIT 1
) v ON true
WHERE p.status NOT IN ('archived')
ORDER BY p.product_score DESC;
```

---

## 10. Dashboard — metryki i widoki

### 10.1 Widok główny — ranking produktów

```
╔══════════════════════════════════════════════════════════════════════╗
║  DASHBOARD — SYSTEM POSZUKIWANIA PRODUKTÓW           🔄 Odśwież    ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║  📊 PODSUMOWANIE                                                    ║
║  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐           ║
║  │   127    │  │    23    │  │     5    │  │     1    │           ║
║  │Odkrytych │  │Na watch- │  │W walida- │  │Skalowa-  │           ║
║  │          │  │  liście  │  │  cji     │  │  nych    │           ║
║  └──────────┘  └──────────┘  └──────────┘  └──────────┘           ║
║                                                                      ║
║  📈 TOP 10 PRODUKTÓW (wg Product Score)                             ║
║  ┌────┬─────────────────────┬────────┬────────┬────────┬─────────┐ ║
║  │ #  │ Produkt             │ Score  │ Trend  │ Marża  │ Status  │ ║
║  ├────┼─────────────────────┼────────┼────────┼────────┼─────────┤ ║
║  │ 1  │ Lampka QI           │ 82     │ ↑180%  │ 76%    │ 🟢 GO   │ ║
║  │ 2  │ Masażer karkowy     │ 78     │ ↑120%  │ 72%    │ 🟠 Test │ ║
║  │ 3  │ Organizer kabli     │ 75     │ ↑95%   │ 81%    │ 🟠 Test │ ║
║  │ 4  │ Lampa projektowa    │ 73     │ ↑210%  │ 68%    │ 🟡 Watch│ ║
║  │ 5  │ Nawilżacz mini      │ 71     │ ↑65%   │ 74%    │ 🟡 Watch│ ║
║  │ ...│                     │        │        │        │         │ ║
║  └────┴─────────────────────┴────────┴────────┴────────┴─────────┘ ║
║                                                                      ║
║  🚨 ALERTY                                                          ║
║  ┌──────────────────────────────────────────────────────────────┐   ║
║  │ ⚡ Nowy produkt: "Pet Travel Bowl" — Score 79, TikTok 1.2M  │   ║
║  │ 📈 Trend spike: "Lampa LED sunset" +340% w 7 dni           │   ║
║  │ ✅ Walidacja zakończona: "Masażer karkowy" — ROAS 2.8×     │   ║
║  └──────────────────────────────────────────────────────────────┘   ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
```

### 10.2 Widok walidacji

```
╔══════════════════════════════════════════════════════════════════════╗
║  AKTYWNE WALIDACJE                                                   ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║  ┌────────────────────────────────────────────────────────────┐     ║
║  │  Masażer karkowy          Metoda: MVP Sale                 │     ║
║  │  Dzień: 5/10              Budżet: 380/750 PLN             │     ║
║  │                                                            │     ║
║  │  CTR: 3.8%    CPC: 0.72 PLN    CR: 2.1%    ROAS: 2.3×   │     ║
║  │  ████████████████░░░░  Zakupy: 7   Przychód: 1043 PLN    │     ║
║  │                                                            │     ║
║  │  Trend: 📈 Rosnący (ROAS wczoraj: 2.8×)                  │     ║
║  │  Prognoza: 🟢 Na dobrej drodze do GO                     │     ║
║  └────────────────────────────────────────────────────────────┘     ║
║                                                                      ║
║  ┌────────────────────────────────────────────────────────────┐     ║
║  │  Organizer kabli          Metoda: Smoke Test               │     ║
║  │  Dzień: 3/5               Budżet: 160/300 PLN             │     ║
║  │                                                            │     ║
║  │  CTR: 4.2%    CPC: 0.45 PLN    CTA rate: 12%             │     ║
║  │  ███████████████████░  Emaile: 34                         │     ║
║  │                                                            │     ║
║  │  Trend: 📈 Obiecujący                                    │     ║
║  │  Prognoza: 🟠 Przejdź do MVP jeśli CTA >15% na koniec   │     ║
║  └────────────────────────────────────────────────────────────┘     ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
```

### 10.3 Widok trendów

```
╔══════════════════════════════════════════════════════════════════════╗
║  MONITOR TRENDÓW — Ostatnie 7 dni                                    ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║  🔥 NAJSZYBCIEJ ROSNĄCE ZAPYTANIA (Google Trends PL)                ║
║  1. "lampa sunset projektor"    ↑ 340%    Nowe w bazie: ✓           ║
║  2. "robot mopujący"           ↑ 210%    W bazie: ✓ (Score 68)     ║
║  3. "mata akupresurowa"        ↑ 180%    W bazie: ✓ (Score 74)     ║
║  4. "bezprzewodowy endoskop"   ↑ 150%    Nowe w bazie: ✓           ║
║  5. "kubek termiczny smart"    ↑ 130%    W bazie: ✓ (Score 61)     ║
║                                                                      ║
║  🎥 VIRALOWE NA TIKTOK (>500K views, produkt fizyczny)              ║
║  1. Pet cooling mat — 4.2M views — Dodany automatycznie             ║
║  2. Magnetic phone mount — 2.1M views — W bazie (Score 72)         ║
║  3. LED book light — 1.8M views — Dodany automatycznie             ║
║                                                                      ║
║  📢 DŁUGO DZIAŁAJĄCE REKLAMY FB (>21 dni = potwierdzony sukces)     ║
║  1. "Poduszka ortopedyczna X" — 34 dni aktywna — Score 71          ║
║  2. "Czyszczarka ultradźwiękowa" — 28 dni — Score 69               ║
║  3. "Wałek do masażu 4D" — 23 dni — Score 65                       ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
```

---

## 11. Przykładowy flow — od pomysłu do decyzji

### Scenariusz: "Masażer karkowy z EMS"

```
DZIEŃ 0 — ODKRYCIE
───────────────────
System automatycznie wykrywa:
  - Google Trends PL: "masażer karkowy" +120% w 30 dni
  - TikTok: Wideo z masażerem ma 3.2M wyświetleń
  - AliExpress: Produkt ma 15,000+ zamówień, 4.7★
  - Facebook: 8 aktywnych reklam, najdłuższa działa 19 dni

→ Produkt dodany do bazy automatycznie
→ Alert na Telegram: "Nowy kandydat: Masażer karkowy EMS (Score: 78)"


DZIEŃ 1 — SCORING + OCENA MANUALNA (15 min)
────────────────────────────────────────────
Automatyczne:
  ✓ Wzrost popytu: 8/10 (Google Trends +120%)
  ✓ Marża: 7/10 (koszt 42 PLN, cena 169 PLN = 75% marży)
  ✓ Wiralowość: 9/10 (3.2M TikTok views)
  ✓ Konkurencja: 6/10 (4 polskie sklepy, ale mało agresywne)
  ✓ Logistyka: 8/10 (0.3 kg, kompaktowy)
  ✓ Engagement: 8/10 (19 dni aktywna reklama FB)

Manualne (oceniasz Ty):
  ✓ Demonstrowalność: 8/10 (efekt widoczny od razu na wideo)
  ✓ Rozwiązanie problemu: 7/10 (ból karku to powszechny problem)
  ✓ Powtarzalność: 3/10 (jednorazowy zakup)
  ✓ Unikalność: 5/10 (dostępny na Allegro, ale niewiele ofert)

→ Product Score: 78/100 — Kategoria: 🟢 Kandydat
→ Decyzja: Przejdź do walidacji


DZIEŃ 2 — SMOKE TEST (start)
─────────────────────────────
  ✓ Budowa landing page na Carrd.co (2h)
  ✓ Kreacja reklamy: 15s wideo (problem → użycie → ulga)
  ✓ Facebook Ads: 50 PLN/dzień, broad targeting 25-55 lat
  ✓ Cel: Landing Page Views


DZIEŃ 2-6 — SMOKE TEST (trwa)
──────────────────────────────
Wyniki po 5 dniach (250 PLN wydane):
  CTR: 4.1%          ✅ (>3%)
  CPC: 0.62 PLN      ✅ (<1 PLN)
  CTA rate: 14%      ✅ (>6%)
  Email signups: 22   ✅

→ Wynik: 🟢 SILNY SYGNAŁ — przejdź do MVP Sale


DZIEŃ 7 — MVP SALE (start)
───────────────────────────
  ✓ Zamówione 10 szt. z AliExpress (szybka dostawa, 420 PLN)
  ✓ Sklep na WordPress + flavor-flavor-flavor (4h setup)
  ✓ Stripe + BLIK skonfigurowane
  ✓ Własne zdjęcia produktu (po otrzymaniu)
  ✓ Facebook Ads: 75 PLN/dzień, 3 kreacje, Conversions


DZIEŃ 7-17 — MVP SALE (trwa)
─────────────────────────────
Wyniki po 10 dniach (750 PLN wydane):
  CTR: 3.8%          ✅
  CPC: 0.72 PLN      ✅
  CR: 2.1%           ✅ (>1.5%)
  Zakupy: 11
  Przychód: 1,859 PLN
  ROAS: 2.5×         ✅ (>2×)
  Zwroty: 0          ✅
  Średnia ocena: 4.6/5  ✅

→ Wynik: 🟢 GO — SKALUJ


DZIEŃ 18+ — SKALOWANIE
───────────────────────
  ✓ Zamówienie 100 szt. od dostawcy (negocjowana cena: 35 PLN/szt.)
  ✓ Budżet reklamowy: 150 PLN/dzień
  ✓ Dodanie TikTok Ads
  ✓ Sekwencja email retargetingowa
  ✓ Zbieranie UGC i opinii
  ✓ Cel: 15-30 zamówień/dzień w ciągu 30 dni
```

---

## 12. Ryzyka i ograniczenia

### 12.1 Ryzyka techniczne

| Ryzyko | Prawdopodobieństwo | Wpływ | Mitygacja |
|---|:---:|:---:|---|
| Zablokowanie scrapera | Wysokie | Średni | Rotacja proxy, Apify (managed), rate limiting |
| Zmiana API platformy | Średnie | Wysoki | Monitoring zmian, alternatywne źródła |
| False positives w scoringu | Średnie | Średni | Kalibracja wag co kwartał na podstawie wyników |
| Opóźnienia danych | Niskie | Niski | Cache + fall-back na dane z poprzedniego dnia |

### 12.2 Ryzyka biznesowe

| Ryzyko | Prawdopodobieństwo | Wpływ | Mitygacja |
|---|:---:|:---:|---|
| Produkt już nasycony w momencie testu | Wysokie | Wysoki | Monitoruj nasycenie reklam, czas od odkrycia do testu <7 dni |
| Niska jakość produktu od dostawcy | Średnie | Wysoki | Zamów próbkę przed skalowaniem, minimum 2 dostawców |
| Zmiany w polityce reklamowej FB/TikTok | Średnie | Wysoki | Dywersyfikacja kanałów, budowa organicznego ruchu |
| Problemy z reklamacjami/zwrotami | Niskie | Średni | Jasna polityka zwrotów, testuj jakość przed skalowaniem |
| Sezonowość ukryta w danych | Średnie | Średni | Porównuj dane YoY, nie tylko MoM |

### 12.3 Kluczowe zasady

1. **Szybkość > perfekcja** — lepiej przetestować 10 produktów w miesiąc niż idealnie zanalizować 1
2. **Dane > intuicja** — każda decyzja oparta na liczbach, nie przeczuciach
3. **Fail fast** — jeśli Smoke Test daje <3% CTR, nie walcz — następny produkt
4. **Nie przywiązuj się** — emocje są wrogiem dobrego product researchera
5. **Kalibruj system** — co kwartał weryfikuj wagi scoringu na podstawie wyników walidacji
6. **Dokumentuj wszystko** — każdy test, każda kreacja, każdy wynik → baza wiedzy na przyszłość

---

*System zaprojektowany do ciągłego działania. Każdy cykl odkrywanie-scoring-walidacja trwa 2-3 tygodnie. Przy regularnym używaniu, system wypracowuje coraz trafniejsze predykcje dzięki kalibracji na podstawie historycznych wyników.*
