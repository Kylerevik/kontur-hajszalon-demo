# Kontúr Hajszalon – demó weboldal

Élő demó: https://kontur-hajszalon-demo.kesug.com

Készítette: Illés Gergely (fejlesztői néven: chill), webfejlesztő, Pécs.

## Mi ez, és miért készült?

A Kontúr Hajszalon kitalált pécsi fodrászat, a nevek, címek, telefonszámok, árak és foglalások mintaadatok. Az oldal portfólió-bemutató munka: azt mutatja meg, hogyan lehet egy kisebb helyi szolgáltatónak működő online időpontfoglalást adni.

Egy fodrászatnál a legtöbb időt a telefonálgatás és az időpont-egyeztetés viszi el, miközben a vendég gyakran este vagy hétvégén akar foglalni. Az oldal feladata, hogy a vendég ránézésre lássa, mit mennyiért kap, és pár kattintással le tudja foglalni a szabad időpontot. A szalon pedig egy helyen látja a foglalásait, és nem fordulhat elő, hogy ugyanarra az időpontra ketten kerüljenek be.

## Mit tud az oldal?

- Főoldal a szalon bemutatásával, a szolgáltatás-kategóriákkal és a foglalás lépéseivel.
- Szolgáltatások és árak oldal kategóriákra bontva, minden tételnél időtartammal és árral.
- Időpontfoglalás: szolgáltatás- és napválasztás után a vendég csak a valóban szabad kezdési időpontokat látja. Ezeket a szolgáltatás hossza, a nyitvatartás és a meglévő foglalások alapján számolja ki az oldal.
- Dupla foglalás nem lehetséges: ha két vendég egyszerre ugyanazt az időpontot választja, a második vendég üzenetet kap, hogy válasszon másikat.
- Hibás kitöltésnél az űrlap tetején összefoglaló, a mezők mellett pedig érthető magyar üzenet jelzi, mit kell javítani, a már beírt adatok megmaradnak.
- Foglalás után az oldalon megjelenik a visszaigazolás foglalási azonosítóval. E-mail nem megy ki.
- Admin felület: a foglalások napok szerint csoportosítva, szűrhetők nézet (közelgő, korábbi, összes), nap és állapot szerint. Látszik a mai, a heti és a közelgő foglalások száma. Az állapot (visszaigazolt, teljesült, nem jelent meg, lemondva) átállítható, a foglalás törölhető. A lemondott foglalás felszabadítja az időpontot.
- Telefonon is kényelmes: a főoldalon és a szolgáltatások oldalon alul végig látszik egy "Foglalás" és egy "Hívás" gomb, a gombok és a hivatkozások ujjal könnyen megérinthetők, a foglalásnál a kiválasztott szolgáltatás jelölővel látszik.
- Az admin felület a `/admin/` címen érhető el, a demó belépési adatok (`demo` / `Szalon2026!`) a belépő oldalon is látszanak.

## Szakmai összefoglaló

**Front-end**
- Szemantikus HTML5, mobile first elrendezés Bootstrap 5.3 rácsrendszerrel, saját CSS-sel kiegészítve, a stíluslapok és a szkriptek külön fájlokban, inline stílus és szkript nélkül.
- A hátterek SVG-ből és CSS-átmenetekből készültek: a főoldali hero, az oldalak fejlécei és a lábléc sötét, hajszálakat idéző vonalmintás felület, a közbeeső szekciók világosak és letisztultak. Fényképek nélkül készült, így kis méretű és gyorsan tölt.
- Vanilla JavaScript: a szabad időpontokat a foglalási oldal `fetch`-csel kéri le egy JSON végpontról, a megkezdett, de már elavult kérést megszakítja (`AbortController`). Ha a lekérés nem sikerül, a vendég magyar üzenetet kap.
- Saját tárhelyről kiszolgált betűtípus (Fraunces, woff2) előtöltéssel, a CSS és JS fájlokhoz módosítási időn alapuló verziószám a gyorsítótár frissítéséhez, SVG logó, favicon és főoldali grafika.

**Back-end**
- PHP 8 `strict_types` módban, PDO-val, MySQL / MariaDB adatbázissal. Az adatbázis-kapcsolat a `config.php`-ból jön, a mintája a `config.example.php`, maga a `config.php` nincs a tárolóban.
- A szabad időpontok számítása külön modulban van (30 perces lépésköz, legalább 60 perces előrelátás, 60 napra előre foglalható), a nyitvatartás az adatbázisból jön.
- A dupla foglalás kizárása szerveroldali: a mentés napra szóló `GET_LOCK` zár alatt fut, a szabad időpont újraellenőrzése után, a zárat `finally` ágban engedi el.
- Az admin belépés `password_hash` / `password_verify` párossal, a munkamenet azonosítója belépéskor megújul, sikertelen belépés után késleltetés van.
- Az egyes végpontok csak a megfelelő HTTP-módszert fogadják el (különben 405 `Allow` fejléccel), a hibák a helyes státuszkóddal térnek vissza (403 lejárt űrlap, 422 hibás adat, 409 foglalt időpont, 401 hibás belépés, 503 adatbázishiba).
- Az adatbázis és a közös kód mappái, valamint a konfigurációs fájlok `.htaccess`-szel védettek. Adatbázishiba esetén a hibanaplóba csak a hibakód kerül, a gazdagép és a felhasználónév nem.

**Minőség**
- Biztonság: minden SQL-lekérdezés prepared statement (kikapcsolt emulációval), a kimenet escape-elve van (XSS ellen), minden űrlapon CSRF-token van, a foglalási űrlapon rejtett csalimező (honeypot), a munkamenet-süti `HttpOnly` és `SameSite=Lax`. A válaszokban `X-Content-Type-Options`, `X-Frame-Options` és `Referrer-Policy` fejléc van, a CDN-ről betöltött Bootstrapnél SRI hash.
- Validáció: az adatokat a szerver ellenőrzi (név, telefonszám, dátum, időpont, szolgáltatás), a böngésző saját ellenőrzése (`required`, `minlength`, `maxlength`) ezt csak kiegészíti. A tömbként küldött paraméterek sem okoznak hibát.
- SEO: oldalanként egyedi `title` és `meta description`, Open Graph alapadatok, `lang="hu"`, egy `h1` oldalanként, a főoldalon `HairSalon` strukturált adat, az admin oldalak `noindex` jelölésűek.
- Akadálymentesség: ugrás a tartalomra link, címkézett űrlapmezők, `aria-live` a szabad időpontok üzeneténél, `aria-current` a menüben, billentyűzettel is használható időpontválasztó és fejlesztői névbuborék, `prefers-reduced-motion` figyelembevétele.
- Tesztelés (PHP 8.3, MariaDB 10.11, Apache, Chromium):
  - Minden PHP-fájl `php -l`, minden JS-fájl `node --check` ellenőrzésen átment; a kódban nincs TODO, hibakereső kiírás, kommentelt kód és inline stílus.
  - A HTML-ellenőrzés egyedi azonosítókat, működő belső hivatkozásokat, meglévő fájlhivatkozásokat, címkézett mezőket, oldalanként egy `h1`-et és minden képen `alt` szöveget igazolt.
  - Playwright böngészős teszt, 92 ellenőrzés 320, 390, 768, 1280 és 1920 px szélességen (mobil nézet emulálva): nincs vízszintes görgetés, konzolhiba és sikertelen kérés, a menü, az időpontválasztó és az űrlapok (üres, hibás, helyes kitöltés) végigpróbálva, a teljes foglalás és az admin műveletek is.
  - Végpontteszt 113 ellenőrzéssel: hibás módszer, üres és hiányos kérés, tömb paraméter, XSS- és SQL-injekciós szövegek, csalimező, CSRF nélküli és hibás kérések, admin belépés és műveletek, védett fájlok elérhetetlensége. Mind a helyes státuszkódot és magyar üzenetet adta.
  - 12 egyidejű foglalási kérés ugyanarra az időpontra: egy sikeres, tizenegy elutasított, az adatbázisban egy sor.
  - Szimulált adatbázishiba: az oldal magyar hibaüzenetet ad, a hibanapló nem tartalmaz jelszót és gazdagépnevet.

## Felhasználás

Az oldal bemutató célú. A kód és a tartalom a készítő munkája, a cég, a szövegek és az adatok kitaláltak.

## Továbbfejlesztési irányok

Az alábbi bővítések mindegyikét magam is meg tudom valósítani:

- Több fodrász külön naptárral, a vendég által választható szakemberrel.
- Szabadnapok és ünnepnapok kezelése külön táblában, a szabad időpontok számításába beépítve.
- Visszaigazoló e-mail vagy SMS küldése külső szolgáltatóval, emlékeztetővel a foglalás előtt.
- A foglalás lemondása és átfoglalása a vendég oldalán, a foglalási azonosítóval.
- Admin: kézi foglalás rögzítése, naptár nézet, a szolgáltatások és a nyitvatartás szerkesztése, jelszócsere.

És még sok más: számtalan további fejlesztés elérhető, az ügyfél igényei szerint.
