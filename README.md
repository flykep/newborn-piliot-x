# Database Schema — AutoAdvisor

## Creazione Database

```sql
DROP SCHEMA IF EXISTS autoadvisor;

CREATE DATABASE IF NOT EXISTS autoadvisor;
USE autoadvisor;
```

---

# Tabella `users`

```sql
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(50) NOT NULL,
    cognome         VARCHAR(50) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    compleanno      DATE NOT NULL,
    password        VARCHAR(255) NOT NULL,
    ruolo           ENUM('user', 'admin') DEFAULT 'user',

    PRIMARY KEY (id),
    INDEX idx_email (email)
);
```

---

# Tabella `auto`

```sql
CREATE TABLE IF NOT EXISTS auto (
    id                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    marca                   VARCHAR(50) NOT NULL,
    modello                 VARCHAR(100) NOT NULL,
    versione                VARCHAR(100) DEFAULT NULL,
    anno                    YEAR NOT NULL,
    prezzo                  DECIMAL(10,2) NOT NULL,

    alimentazione ENUM(
        'benzina',
        'diesel',
        'gpl',
        'ibrido',
        'elettrico'
    ) NOT NULL,

    dimensioni ENUM(
        'city',
        'util',
        'berl',
        'suv',
        'sw'
    ) NOT NULL,

    utilizzo SET(
        'citta',
        'extra',
        'auto',
        'misto'
    ) NOT NULL DEFAULT 'misto',

    neopatentato            TINYINT(1) NOT NULL DEFAULT 0,
    potenza_kw              SMALLINT UNSIGNED DEFAULT NULL,
    peso_kg                 SMALLINT UNSIGNED DEFAULT NULL,
    autonomia_elettrica     SMALLINT UNSIGNED DEFAULT NULL,

    PRIMARY KEY (id),

    -- Indici per filtri veloci
    INDEX idx_ricerca_veloce (
        prezzo,
        alimentazione,
        dimensioni
    ),

    INDEX idx_neopatentati (
        neopatentato
    )
);
```

---

# Inserimento Utenti Demo

```sql
INSERT INTO users (
    nome,
    cognome,
    email,
    compleanno,
    password,
    ruolo
) VALUES

(
    'Mario',
    'Rossi',
    'mario.rossi@email.it',
    '1990-05-12',
    '$2y$12$hashed_mario',
    'user'
),

(
    'Elena',
    'Bianchi',
    'elena.admin@autoadvisor.it',
    '1985-09-20',
    '$2y$12$hashed_elena',
    'admin'
),

(
    'Luca',
    'Verdi',
    'luca.neopatentato@email.it',
    '2006-01-15',
    '$2y$12$hashed_luca',
    'user'
);
```

---

# Inserimento Auto Demo

```sql
INSERT INTO auto (
    marca,
    modello,
    versione,
    anno,
    prezzo,
    alimentazione,
    dimensioni,
    utilizzo,
    neopatentato,
    potenza_kw,
    peso_kg,
    autonomia_elettrica
) VALUES

-- 1. Budget < 5.000€ | Citycar | Benzina
(
    'Fiat',
    'Punto',
    '1.2 Classic',
    2008,
    3500.00,
    'benzina',
    'city',
    'citta',
    1,
    44,
    950,
    NULL
),

-- 2. Budget 5.000€ - 10.000€ | GPL
(
    'Dacia',
    'Sandero',
    '1.0 Stepway GPL',
    2017,
    8900.00,
    'gpl',
    'util',
    'misto',
    1,
    66,
    1100,
    NULL
),

-- 3. Hybrid Citycar
(
    'Fiat',
    '500',
    '1.0 Hybrid Cult',
    2022,
    14500.00,
    'ibrido',
    'city',
    'citta',
    1,
    51,
    1055,
    NULL
),

-- 4. SUV Diesel
(
    'Renault',
    'Captur',
    '1.5 dCi 110CV',
    2018,
    16800.00,
    'diesel',
    'suv',
    'extra',
    0,
    81,
    1280,
    NULL
),

-- 5. Berlina Benzina
(
    'Alfa Romeo',
    'Giulietta',
    '1.4 Turbo 120CV',
    2020,
    21500.00,
    'benzina',
    'berl',
    'auto',
    0,
    88,
    1310,
    NULL
),

-- 6. Utilitaria Elettrica
(
    'Renault',
    'Zoe',
    'R135 52kWh',
    2021,
    28000.00,
    'elettrico',
    'util',
    'citta,misto',
    1,
    100,
    1500,
    390
),

-- 7. SUV Elettrico Premium
(
    'Tesla',
    'Model Y',
    'Long Range AWD',
    2023,
    49900.00,
    'elettrico',
    'suv',
    'auto',
    0,
    258,
    2000,
    533
),

-- 8. Station Wagon Diesel
(
    'Audi',
    'A4 Avant',
    '35 TDI Business',
    2022,
    42000.00,
    'diesel',
    'sw',
    'extra,auto',
    0,
    120,
    1600,
    NULL
),

-- 9. Utilitaria Benzina
(
    'Volkswagen',
    'Polo',
    '1.0 EVO 80CV',
    2021,
    17500.00,
    'benzina',
    'util',
    'misto',
    1,
    59,
    1150,
    NULL
),

-- 10. Berlina Plug-in Hybrid
(
    'BMW',
    'Serie 3',
    '330e Plug-in',
    2022,
    52000.00,
    'ibrido',
    'berl',
    'misto',
    0,
    215,
    1815,
    60
);
```
