USE fbalestr;

DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS utente_registrato;
DROP TABLE IF EXISTS amministratore;
DROP TABLE IF EXISTS famiglia;
DROP TABLE IF EXISTS pesce;
DROP TABLE IF EXISTS indirizzo;
DROP TABLE IF EXISTS utente_indirizzo;
DROP TABLE IF EXISTS ordine;
DROP TABLE IF EXISTS dettaglio_ordine;

CREATE TABLE utente(
	email VARCHAR(255) PRIMARY KEY
);

CREATE TABLE utente_registrato(
	email VARCHAR(255) PRIMARY KEY,
	username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
	FOREIGN KEY (email) REFERENCES utente(email)
);

CREATE TABLE amministratore (
    email VARCHAR(255) PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
	FOREIGN KEY (email) REFERENCES utente(email)
);

CREATE TABLE famiglia (
    famiglia_latino VARCHAR(100) PRIMARY KEY,
    famiglia_comune VARCHAR(100) NOT NULL,
    tipo_acqua ENUM('dolce', 'marina') NOT NULL
);

CREATE TABLE pesce (
    nome_latino VARCHAR(150) PRIMARY KEY,
    nome_comune VARCHAR(150) NOT NULL UNIQUE,
    famiglia VARCHAR(100) NOT NULL,
    dimensione DECIMAL(5,2),
    volume_minimo INT NOT NULL,
    colori VARCHAR(100) NOT NULL,
    prezzo DECIMAL(8,2) NOT NULL,
    sconto_percentuale TINYINT UNSIGNED DEFAULT 0,
    disponibilita INT NOT NULL,
    descrizione TEXT NOT NULL,
    immagine VARCHAR(255) NOT NULL,
	FOREIGN KEY (famiglia) REFERENCES famiglia(famiglia_latino)
);

CREATE TABLE indirizzo (
    id_indirizzo INT AUTO_INCREMENT PRIMARY KEY,
    provincia VARCHAR(50) NOT NULL,
    citta VARCHAR(50) NOT NULL,
    cap CHAR(5) NOT NULL,
    via VARCHAR(150) NOT NULL
);

CREATE TABLE utente_indirizzo (
    email VARCHAR(255),
    id_indirizzo INT,
    predefinito BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (email, id_indirizzo),
	FOREIGN KEY (email) REFERENCES utente(email),
	FOREIGN KEY (id_indirizzo) REFERENCES indirizzo(id_indirizzo)
);

CREATE TABLE ordine (
    id_ordine INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    id_indirizzo INT NOT NULL,
    data_ora DATETIME NOT NULL,
	FOREIGN KEY (email) REFERENCES utente(email),
	FOREIGN KEY (id_indirizzo) REFERENCES indirizzo(id_indirizzo)
);

CREATE TABLE dettaglio_ordine (
    id_ordine INT,
    nome_latino VARCHAR(150),
    quantita INT NOT NULL,
    prezzo_unitario DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (id_ordine, nome_latino),
	FOREIGN KEY (id_ordine) REFERENCES ordine(id_ordine),
	FOREIGN KEY (nome_latino) REFERENCES pesce(nome_latino)
);