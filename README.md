# Voltpont Villanyszerelő Kft. – demó weboldal

Élő demó: https://voltpont-demo.kesug.com

Készítette: Illés Gergely (fejlesztői néven: chill), webfejlesztő, Pécs.

## Mi ez, és miért készült?

A Voltpont egy kitalált pécsi villanyszerelő cég. A cég, az adatai, a referenciái és a vélemények is mintaadatok, valódi vállalkozáshoz nem kapcsolódnak.

Az oldal portfólió-bemutató munka. Azt mutatja meg, hogy egy helyi szolgáltató cégnek milyen bemutatkozó weboldalt tudok elkészíteni: olyat, ami telefonon is jól használható, gyorsan betöltődik, és az érdeklődőből ajánlatkérés lesz. Egy kisvállalkozás számára a weboldal fő feladata, hogy bizalmat építsen és megkönnyítse a kapcsolatfelvételt, ezért az oldal minden része erre épül.

## Mit tud az oldal?

Az oldal egy villanyszerelő cég teljes online jelenlétét lefedi egyetlen oldalon:

- Bemutatja a cég szolgáltatásait, és az egyes szolgáltatásoktól közvetlenül az ajánlatkérésre lehet ugrani.
- Elmagyarázza, hogyan zajlik egy munka a megrendeléstől az átadásig, így az ügyfél tudja, mire számíthat.
- Megmutatja az elvégzett munkákat, amelyeket kategóriák szerint lehet szűrni.
- Visszaigazolásként ügyfélvéleményeket és válaszokat ad a leggyakoribb kérdésekre.
- Ajánlatkérő űrlapot kínál: ide kell megadni a nevet, az elérhetőséget, a munka leírását, és az adatok a cég adatbázisába kerülnek. Hibás kitöltésnél az űrlap a mező mellett, érthetően jelzi, mit kell javítani.
- Telefonon külön figyelmet kapott a használhatóság: a kártyasorok ujjal oldalra húzhatók, a menü egy érintéssel elérhető, és van egy gyors hívás gomb.

## Szakmai összefoglaló

**Front-end:** szemantikus HTML5, mobile first reszponzív elrendezés Bootstrap 5.3 rácsrendszerrel és saját CSS-sel (egyedi tulajdonságok, SVG-maszkos háttérminták, scroll-snap alapú húzható kártyasorok). A kliensoldali logika vanilla JavaScript: űrlapvalidáció, aszinkron beküldés `fetch`-csel, referenciaszűrő, navigáció.

**Back-end:** egyetlen PHP végpont JSON válaszokkal és megfelelő HTTP státuszkódokkal (405, 422, 500, 200). A bemenetet a szerver is ellenőrzi (hossz, formátum, engedélyezett értékek listája, típusellenőrzés), így a kliensoldali validáció megkerülése sem visz be hibás adatot. A mentés PDO-val, valódi (nem emulált) prepared statementtel történik MySQL / MariaDB adatbázisba. A rejtett mezős (honeypot) védelem kiszűri a robotokat, a kliens a szerver üzeneteit `textContent`-tel írja ki (XSS ellen), a hibák naplójába pedig nem kerül jelszó vagy hostnév. Az adatbázis-beállítások a webről nem elérhető mappában vannak, és nem szerepelnek a repóban.

**Minőség:** alap SEO (title, meta leírás, Open Graph, JSON-LD `Electrician` séma), billentyűzettel és képernyőolvasóval használható felület, `prefers-reduced-motion` támogatás, külső függőség nélküli, tiszta kód: nincs benne használatlan CSS, holt kód vagy hibakereső kiírás.

**Tesztelés:** az oldalt 320 és 1920 képpont közötti szélességeken ellenőriztem (nincs vízszintes túlcsordulás, nincs konzolhiba), az űrlapot kliens- és szerveroldalon is kipróbáltam hibás, hiányos és rosszindulatú adatokkal, valamint éles tárhelyen a mentést az adatbázisba.

## Felhasználás

Az oldal bemutató célú. A kód és a tartalom a készítő munkája, a Voltpont név és az adatok kitaláltak.

## Továbbfejlesztési irányok

Az alábbi bővítések mindegyikét magam is meg tudom valósítani, ügyfélmunkában igény szerint ezek is a csomag részei lehetnek:

- Admin felület az ajánlatkérések kezelésére (bejelentkezés, lista, státuszok)
- E-mail értesítés új kérésről
- Adatkezelési tájékoztató oldal
- Sebességkorlát az űrlapra
- És még sok más: számtalan további fejlesztés elérhető, az ügyfél igényei szerint
