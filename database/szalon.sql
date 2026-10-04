-- Kontúr Hajszalon adatbázis. Egy már létező, üres adatbázisba kell importálni.
SET NAMES utf8mb4;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS opening_hours;
DROP TABLE IF EXISTS admins;

CREATE TABLE services (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category VARCHAR(40) NOT NULL,
    name VARCHAR(80) NOT NULL,
    description VARCHAR(200) NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL,
    price INT UNSIGNED NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE opening_hours (
    weekday TINYINT UNSIGNED NOT NULL COMMENT '1 = hétfő, 7 = vasárnap',
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    PRIMARY KEY (weekday)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code CHAR(8) NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(80) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('confirmed', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'confirmed',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookings_code (code),
    KEY idx_bookings_day (booking_date, start_time),
    CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES services (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admins (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(40) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO opening_hours (weekday, open_time, close_time) VALUES
    (2, '09:00:00', '18:00:00'),
    (3, '09:00:00', '18:00:00'),
    (4, '09:00:00', '19:00:00'),
    (5, '09:00:00', '18:00:00'),
    (6, '08:00:00', '13:00:00');

INSERT INTO services (id, category, name, description, duration_minutes, price, sort_order) VALUES
    (1, 'Női', 'Női hajvágás', 'Konzultáció, hajmosás, vágás és szárítás.', 60, 7900, 10),
    (2, 'Női', 'Pontvágás, frufru', 'Gyors igazítás a meglévő formán, mosás nélkül.', 30, 3500, 20),
    (3, 'Női', 'Alkalmi frizura', 'Esküvőre, ballagásra, rendezvényre. Próbafrizura külön foglalható.', 60, 8500, 30),
    (4, 'Férfi', 'Férfi hajvágás', 'Géppel és ollóval, mosással és befejező formázással.', 30, 4500, 40),
    (5, 'Férfi', 'Szakálligazítás', 'Formázás ollóval és borotvával, meleg törölközős kezeléssel.', 30, 3000, 50),
    (6, 'Férfi', 'Hajvágás és szakáll', 'A két szolgáltatás egyben, kedvezményes áron.', 60, 7000, 60),
    (7, 'Gyerek', 'Gyerekhajvágás', '12 éves korig. Türelmesen, mesével.', 30, 3500, 70),
    (8, 'Festés és melír', 'Tőfestés', 'Lenövés eltüntetése, a meglévő árnyalat frissítése.', 90, 12500, 80),
    (9, 'Festés és melír', 'Teljes hajfestés', 'Színváltás vagy színfrissítés hajmosással és szárítással.', 150, 18900, 90),
    (10, 'Festés és melír', 'Fóliás melír', 'Természetes átmenetekkel, tónusozással. Hajhossztól függően.', 180, 24900, 100),
    (11, 'Festés és melír', 'Balayage', 'Kézzel festett, lágy színátmenet, tónusozással és szárítással.', 210, 32000, 110),
    (12, 'Ápolás', 'Hajápoló kezelés', 'Mélytáplálás sérült, festett hajra, fejmasszázzsal.', 30, 4900, 120);

-- Jelszó: Szalon2026!
INSERT INTO admins (username, password_hash) VALUES
    ('demo', '$2y$10$6CPuq4n42hQ0pj1/4lfpfeeLmWSYWb8eRoUUAJnHU17egcFAzR87W');

-- A mintafoglalások az import napjához igazodnak: a következő kedd-szombat időszakra,
-- illetve két héttel korábbra esnek, így az admin lista mindig élő képet mutat.
SET @next_tuesday = DATE_ADD(CURDATE(), INTERVAL ((1 - WEEKDAY(CURDATE()) + 6) % 7) + 1 DAY);

INSERT INTO bookings (code, service_id, customer_name, customer_phone, booking_date, start_time, end_time, status) VALUES
    ('K4M7QX2A', 1, 'Horváth Anna', '+36 30 555 0117', @next_tuesday, '09:00:00', '10:00:00', 'confirmed'),
    ('R8TN3C5W', 8, 'Szabó Edina', '+36 20 555 0184', @next_tuesday, '10:30:00', '12:00:00', 'confirmed'),
    ('B2XF6H9D', 4, 'Nagy Bence', '+36 70 555 0123', @next_tuesday, '14:00:00', '14:30:00', 'confirmed'),
    ('W7PZ4J8E', 10, 'Tóth Réka', '+36 30 555 0166', DATE_ADD(@next_tuesday, INTERVAL 1 DAY), '09:30:00', '12:30:00', 'confirmed'),
    ('T5CV9N3K', 6, 'Kiss Dániel', '+36 20 555 0149', DATE_ADD(@next_tuesday, INTERVAL 1 DAY), '15:00:00', '16:00:00', 'confirmed'),
    ('H3YD8M6S', 3, 'Farkas Lilla', '+36 70 555 0192', DATE_ADD(@next_tuesday, INTERVAL 2 DAY), '10:00:00', '11:00:00', 'confirmed'),
    ('N9QB2L7F', 11, 'Varga Petra', '+36 30 555 0108', DATE_ADD(@next_tuesday, INTERVAL 2 DAY), '13:00:00', '16:30:00', 'confirmed'),
    ('G6ZA5R4U', 7, 'Molnár Zsombor', '+36 20 555 0135', DATE_ADD(@next_tuesday, INTERVAL 3 DAY), '09:00:00', '09:30:00', 'confirmed'),
    ('E8WK3T2P', 9, 'Balogh Fruzsina', '+36 70 555 0171', DATE_ADD(@next_tuesday, INTERVAL 3 DAY), '11:00:00', '13:30:00', 'confirmed'),
    ('D4HX7V9C', 5, 'Papp Gergő', '+36 30 555 0159', DATE_ADD(@next_tuesday, INTERVAL 4 DAY), '08:00:00', '08:30:00', 'confirmed'),
    ('S2JM6Y8B', 1, 'Lakatos Mónika', '+36 20 555 0126', DATE_ADD(@next_tuesday, INTERVAL 4 DAY), '09:00:00', '10:00:00', 'confirmed'),
    ('C7RF4Q3Z', 4, 'Simon Márk', '+36 70 555 0143', DATE_ADD(@next_tuesday, INTERVAL -14 DAY), '10:00:00', '10:30:00', 'completed'),
    ('V5LN8P2X', 2, 'Hegedűs Vivien', '+36 30 555 0102', DATE_ADD(@next_tuesday, INTERVAL -14 DAY), '11:00:00', '11:30:00', 'completed'),
    ('M3TG9K6A', 8, 'Oláh Kitti', '+36 20 555 0178', DATE_ADD(@next_tuesday, INTERVAL -12 DAY), '09:00:00', '10:30:00', 'completed'),
    ('Q8BD5W7H', 12, 'Fehér Barbara', '+36 70 555 0114', DATE_ADD(@next_tuesday, INTERVAL -12 DAY), '14:00:00', '14:30:00', 'no_show'),
    ('Y4XC2N9J', 6, 'Takács Ádám', '+36 30 555 0187', DATE_ADD(@next_tuesday, INTERVAL -12 DAY), '16:00:00', '17:00:00', 'cancelled');
