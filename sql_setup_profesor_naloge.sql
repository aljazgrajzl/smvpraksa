-- Tabela za naloge (assignments), ki jih razpisuje profesor
CREATE TABLE IF NOT EXISTS naloge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    predmet_id INT NOT NULL,
    razred VARCHAR(10) NOT NULL,
    naziv VARCHAR(255) NOT NULL,
    opis TEXT,
    rok_oddaje DATE,
    created_by_profesor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (predmet_id) REFERENCES predmeti(id),
    FOREIGN KEY (created_by_profesor_id) REFERENCES uporabniki(id)
);

-- Tabela za oddaje nalog (student submissions)
CREATE TABLE IF NOT EXISTS oddaje_nalog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naloga_id INT NOT NULL,
    dijak_id INT NOT NULL,
    datoteka_pot VARCHAR(500),
    oddano_datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ocena VARCHAR(10),
    komentar TEXT,
    FOREIGN KEY (naloga_id) REFERENCES naloge(id) ON DELETE CASCADE,
    FOREIGN KEY (dijak_id) REFERENCES uporabniki(id)
);
