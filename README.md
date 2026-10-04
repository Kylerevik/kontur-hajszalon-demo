# Kontúr Hajszalon – online időpontfoglaló

Egy kitalált pécsi fodrászat bemutató weboldala időpontfoglaló rendszerrel és admin felülettel. Portfólió demó, a cég, a címek és a telefonszámok nem valósak.

## Funkciók

- Főoldal és szolgáltatások árakkal és időtartammal
- Foglalás: szolgáltatás, nap és szabad kezdési időpont választása (a szolgáltatás időtartamát figyelembe véve), név és telefonszám
- Dupla foglalás kizárása szerveroldalon (napra szóló adatbázis-zár és újraellenőrzés mentés előtt)
- Visszaigazolás az oldalon, foglalási azonosítóval (e-mail nincs)
- Admin felület: foglalások dátum szerint csoportosítva, szűrés nézet / nap / állapot szerint, állapotváltás, törlés

## Demó admin belépés

Cím: `/admin/` (a belépő oldalra visz) vagy `/admin/login.php`

| Felhasználónév | Jelszó |
|----------------|--------------|
| `demo` | `Szalon2026!` |

Az adatok a belépő oldalon is láthatók. Éles használat előtt cseréld le a jelszót (új hash: `php -r "echo password_hash('uj-jelszo', PASSWORD_DEFAULT);"`, majd `UPDATE admins SET password_hash = '...' WHERE username = 'demo';`).

## Technológia

HTML, CSS, Bootstrap 5 (CDN), JavaScript, PHP 8 (PDO, prepared statementek) és MySQL / MariaDB. Külső függőség csak a Bootstrap, a betűtípus (Fraunces) a projekttel együtt érkezik.

## Fájlok

```
kontur-hajszalon/
├── index.php              főoldal
├── services.php           szolgáltatások és árak
├── booking.php            foglalási űrlap
├── confirmation.php       visszaigazolás foglalás után
├── api/
│   └── slots.php          szabad időpontok (JSON)
├── admin/
│   ├── login.php          belépés
│   ├── bookings.php       foglalások listája, állapotváltás, törlés
│   └── logout.php         kilépés
├── includes/              közös PHP kód (kívülről nem elérhető)
│   ├── bootstrap.php      alapbeállítások, az összes include betöltése
│   ├── database.php       PDO kapcsolat
│   ├── helpers.php        kimenet-escape, CSRF, formázók
│   ├── catalog.php        szolgáltatások és nyitvatartás lekérdezései
│   ├── scheduling.php     szabad időpontok számítása
│   ├── booking_logic.php  foglalás ellenőrzése és mentése
│   ├── admin_bookings.php admin lekérdezések és műveletek
│   ├── head.php, header.php, footer.php, admin_header.php, admin_footer.php
├── css/
│   ├── style.css          nyilvános oldal (az admin is használja)
│   ├── admin.css          admin kiegészítések
│   └── fonts/             Fraunces betűtípus (woff2)
├── js/
│   ├── booking.js         szabad időpontok betöltése a foglalási űrlapon
│   └── admin.js           törlés megerősítése
├── img/                   logó, favicon, főoldali grafika (SVG)
├── database/
│   └── szalon.sql         táblák és mintaadatok (kívülről nem elérhető)
├── config.example.php     adatbázis-beállítások mintája
├── config.php             helyi beállítások (a config.example.php másolata, nincs verziókezelésben)
├── .htaccess              mappalistázás tiltása, védett mappák és fájlok, gyorsítótár
├── .gitignore
└── README.md
```

## Futtatás XAMPP-pal

1. Másold a `kontur-hajszalon` mappát a `C:\xampp\htdocs\` könyvtárba.
2. Indítsd el az Apache-ot és a MySQL-t az XAMPP vezérlőpultjában.
3. Nyisd meg a `http://localhost/phpmyadmin` oldalt, hozz létre egy `kontur_hajszalon` nevű adatbázist (`utf8mb4_unicode_ci` karakterkészlettel), majd importáld bele a `database/szalon.sql` fájlt.
4. Másold le a `config.example.php` fájlt `config.php` néven, és írd át XAMPP-adatokra: host `localhost`, adatbázis `kontur_hajszalon`, felhasználó `root`, jelszó üres.
5. Böngészőben: `http://localhost/kontur-hajszalon/`

## Telepítés InfinityFree tárhelyre

1. A vezérlőpulton nyisd meg a **MySQL Databases** menüt, és hozz létre egy adatbázist. Jegyezd fel a kiírt adatbázisnevet, felhasználónevet, jelszót és a szerver nevét (`sqlXXX.infinityfree.com`).
2. A **phpMyAdmin**-nál válaszd ki az új adatbázist, majd az **Import** fülön töltsd fel a `database/szalon.sql` fájlt. Az import nem hoz létre adatbázist, ezért kell előtte a 1. lépés.
3. Másold le a `config.example.php` fájlt `config.php` néven, és írd be a 1. lépésben feljegyzett adatokat.
4. Töltsd fel a fájlokat FTP-vel vagy az Online File Manager segítségével a `htdocs` mappába (a `.htaccess` fájlt is, a `README.md` és az `.sql` fájl feltöltése nem szükséges).
5. A vezérlőpulton a **Select PHP Version** menüben válassz PHP 8.x verziót.
6. Nyisd meg az oldalt. A foglalási mintaadatok az import napjához igazodnak (a következő kedd–szombatra esnek), ha később frissítenéd őket, importáld újra az SQL fájlt.

## Megjegyzések

- A szabad időpontok 30 perces lépésközzel jönnek ki, a mai napon legalább 60 perc előrelátással. Foglalni 60 napra előre lehet. Ezek a `includes/scheduling.php` elején állíthatók.
- A nyitvatartás az `opening_hours` táblából jön, az oldalon és a foglalási logikában is ugyanezt használja. A hiányzó nap zárva tartást jelent.
- Lemondott foglalás felszabadítja az időpontot. A teljesített és a meg nem jelent foglalás továbbra is foglaltnak számít.
- A dupla foglalás kizárása a MySQL `GET_LOCK` függvényre épül, ami az InfinityFree adatbázisain is használható.

## Továbbfejleszthető

- Több fodrász, munkatársanként külön naptárral és választható szakemberrel
- Szabadnapok és ünnepnapok kezelése (külön tábla)
- Visszaigazoló e-mail vagy SMS (InfinityFree-n nincs `mail()`, ehhez külső SMTP kell, például fizetős tárhelyen PHPMailerrel)
- Foglalás lemondása a vendég oldalán a foglalási azonosítóval
- Admin: kézi foglalás rögzítése, naptár nézet, szolgáltatások és nyitvatartás szerkesztése, jelszócsere
- Kedvezmények, törzsvendég-nyilvántartás
