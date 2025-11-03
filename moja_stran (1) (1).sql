-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gostitelj: localhost:8889
-- Čas nastanka: 03. nov 2025 ob 10.24
-- Različica strežnika: 8.0.40
-- Različica PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `moja_stran`
--

-- --------------------------------------------------------

--
-- Struktura tabele `dijak_predmeti`
--

CREATE TABLE `dijak_predmeti` (
  `id` int NOT NULL,
  `dijak_id` int DEFAULT NULL,
  `predmet_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `dijak_predmeti`
--

INSERT INTO `dijak_predmeti` (`id`, `dijak_id`, `predmet_id`) VALUES
(4, 72, 1),
(5, 72, 3),
(6, 72, 5),
(7, 72, 7),
(8, 72, 18),
(9, 72, 20),
(26, 73, 1),
(27, 73, 2),
(28, 73, 3),
(29, 73, 5),
(30, 73, 6),
(31, 73, 7),
(35, 3, 5),
(36, 3, 9),
(37, 3, 8);

-- --------------------------------------------------------

--
-- Struktura tabele `dijak_profil`
--

CREATE TABLE `dijak_profil` (
  `id` int NOT NULL,
  `dijak_id` int DEFAULT NULL,
  `prvic_prijavljen` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `dijak_profil`
--

INSERT INTO `dijak_profil` (`id`, `dijak_id`, `prvic_prijavljen`) VALUES
(1, 72, 1),
(2, 73, 1),
(3, 3, 1);

-- --------------------------------------------------------

--
-- Struktura tabele `naloge`
--

CREATE TABLE `naloge` (
  `id` int NOT NULL,
  `predmet_id` int NOT NULL,
  `razred` varchar(10) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `opis` text,
  `rok_oddaje` date DEFAULT NULL,
  `created_by_profesor_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `naloge`
--

INSERT INTO `naloge` (`id`, `predmet_id`, `razred`, `naziv`, `opis`, `rok_oddaje`, `created_by_profesor_id`, `created_at`) VALUES
(1, 7, '2.c', 'replika spletne strani', 'naredi repliko spletne strani naše šolske spletne učilnice', '2025-11-30', 74, '2025-11-01 19:37:16'),
(2, 7, '1.b', 'gfxcghv', 'hdyufvhfxycfh', '2025-11-21', 74, '2025-11-01 19:42:07');

-- --------------------------------------------------------

--
-- Struktura tabele `oddaje_nalog`
--

CREATE TABLE `oddaje_nalog` (
  `id` int NOT NULL,
  `naloga_id` int NOT NULL,
  `dijak_id` int NOT NULL,
  `datoteka_pot` varchar(500) DEFAULT NULL,
  `oddano_datum` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ocena` varchar(10) DEFAULT NULL,
  `komentar` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `oddane_naloge`
--

CREATE TABLE `oddane_naloge` (
  `id` int NOT NULL,
  `dijak_id` int DEFAULT NULL,
  `predmet` varchar(100) DEFAULT NULL,
  `naslov_naloge` varchar(255) DEFAULT NULL,
  `datoteka` varchar(255) DEFAULT NULL,
  `datum_oddaje` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('oddano','ocenjeno') DEFAULT 'oddano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `predmeti`
--

CREATE TABLE `predmeti` (
  `id` int NOT NULL,
  `ime_predmeta` varchar(100) NOT NULL,
  `opis` text,
  `link` varchar(255) DEFAULT NULL,
  `slika` varchar(255) DEFAULT NULL,
  `tip_programa` enum('gimnazija','tehniska','vsi') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `predmeti`
--

INSERT INTO `predmeti` (`id`, `ime_predmeta`, `opis`, `link`, `slika`, `tip_programa`) VALUES
(1, 'Slovenščina', 'Spoznavanje pisateljev in pesnikov', 'predmet.slo.php', 'https://placehold.co/600x400/4e54c8/white?text=Slovenščina', 'vsi'),
(2, 'Matematika', 'Osnove algebre, geometrije in računanja', 'predmet.mat.php', 'https://placehold.co/600x400/ff7e5f/white?text=Matematika', 'vsi'),
(3, 'Angleščina', 'Učenje časov, esejev in ponovitev', 'predmet.ang.php', 'https://placehold.co/600x400/11998e/white?text=Angleščina', 'vsi'),
(4, 'Zgodovina', 'Preučevanje zgodovinskih dogodkov', 'predmet.zgodovina.php', 'https://placehold.co/600x400/8f94fb/white?text=Zgodovina', 'gimnazija'),
(5, 'Geografija', 'Raziskovanje Zemlje in njenih lastnosti', 'predmet.geografija.php', 'https://placehold.co/600x400/ff7e5f/white?text=Geografija', 'gimnazija'),
(6, 'Kemija', 'Preučevanje snovi in njihovih lastnosti', 'predmet.kem.php', 'https://placehold.co/600x400/11998e/white?text=Kemija', 'gimnazija'),
(7, 'Računalniški praktikum', 'Programiranje na višjem nivoju', 'predmet.rpr.php', 'https://placehold.co/600x400/8f94fb/white?text=RPR', 'tehniska'),
(8, 'Stroka moderne vsebine', 'Uvod v programiranje in spletni razvoj', 'predmet.smv.php', 'https://placehold.co/600x400/ff7e5f/white?text=SMV', 'tehniska'),
(9, 'Napredna uporaba podatkovnih baz', 'Delov s podatkovnimi bazami in SQL', 'predmet.nup.php', 'https://placehold.co/600x400/11998e/white?text=NUP', 'tehniska');

-- --------------------------------------------------------

--
-- Struktura tabele `profesorji_predmeti`
--

CREATE TABLE `profesorji_predmeti` (
  `id` int NOT NULL,
  `profesor_id` int DEFAULT NULL,
  `predmet_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `profesorji_predmeti`
--

INSERT INTO `profesorji_predmeti` (`id`, `profesor_id`, `predmet_id`) VALUES
(1, 51, 2),
(2, 52, 2),
(3, 53, 1),
(4, 54, 1),
(5, 55, 3),
(6, 56, 3),
(7, 57, 4),
(8, 58, 5),
(9, 59, 6),
(10, 60, 7),
(11, 61, 8),
(12, 61, 9),
(13, 74, 7),
(14, 74, 8),
(15, 63, 3),
(16, 63, 5);

-- --------------------------------------------------------

--
-- Struktura tabele `uporabniki`
--

CREATE TABLE `uporabniki` (
  `id` int NOT NULL,
  `tip_uporabnika` enum('dijak','profesor','administrator') DEFAULT NULL,
  `ime` varchar(50) DEFAULT NULL,
  `priimek` varchar(50) DEFAULT NULL,
  `razred` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `geslo` varchar(255) DEFAULT NULL,
  `datum_registracije` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `uporabniki`
--

INSERT INTO `uporabniki` (`id`, `tip_uporabnika`, `ime`, `priimek`, `razred`, `email`, `geslo`, `datum_registracije`) VALUES
(1, 'dijak', 'Ana', 'Novak', '1.a', 'ana.novak23@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(2, 'dijak', 'Bojan', 'Kovač', '1.a', 'bojan.kovac45@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(3, 'dijak', 'Cvetka', 'Horvat', '1.a', 'cvetka.horvat67@student.esola.si', '$2y$10$dg6iAXmNPKn/cCCMr0VEKuvuuJg6K0H2CRN11BUREo791NkYf.1C.', '2025-10-30 11:29:29'),
(4, 'dijak', 'David', 'Potokar', '1.a', 'david.potokar89@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(5, 'dijak', 'Eva', 'Zupan', '1.a', 'eva.zupan12@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(6, 'dijak', 'Filip', 'Jereb', '2.a', 'filip.jereb34@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(7, 'dijak', 'Gabrijela', 'Sever', '2.a', 'gabrijela.sever56@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(8, 'dijak', 'Hana', 'Tomažič', '2.a', 'hana.tomazic78@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(9, 'dijak', 'Igor', 'Vogrin', '2.a', 'igor.vogrin90@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(10, 'dijak', 'Jana', 'Rozman', '2.a', 'jana.rozman13@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(11, 'dijak', 'Klemen', 'Petrič', '3.a', 'klemen.petric24@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(12, 'dijak', 'Lara', 'Krajnc', '3.a', 'lara.krajnc35@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(13, 'dijak', 'Matic', 'Zajc', '3.a', 'matic.zajc46@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(14, 'dijak', 'Nika', 'Kos', '3.a', 'nika.kos57@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(15, 'dijak', 'Oskar', 'Bizjak', '3.a', 'oskar.bizjak68@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(16, 'dijak', 'Petra', 'Hočevar', '4.a', 'petra.hocevar79@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(17, 'dijak', 'Rok', 'Koren', '4.a', 'rok.koren80@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(18, 'dijak', 'Sara', 'Vidmar', '4.a', 'sara.vidmar91@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(19, 'dijak', 'Tadej', 'Eržen', '4.a', 'tadej.erzen14@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(20, 'dijak', 'Urška', 'Pirc', '4.a', 'urska.pirc25@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(21, 'dijak', 'Vid', 'Kralj', '1.b', 'vid.kralj36@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(22, 'dijak', 'Zala', 'Mlakar', '1.b', 'zala.mlakar47@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(23, 'dijak', 'Alen', 'Zupančič', '1.b', 'alen.zupancic58@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(24, 'dijak', 'Branka', 'Kovačič', '1.b', 'branka.kovacic69@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(25, 'dijak', 'Ciril', 'Potočnik', '1.b', 'ciril.potocnik70@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(26, 'dijak', 'Damjan', 'Logar', '2.b', 'damjan.logar81@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(27, 'dijak', 'Ema', 'Kneževič', '2.b', 'ema.knezevic92@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(28, 'dijak', 'Franci', 'Jerman', '2.b', 'franci.jerman15@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(29, 'dijak', 'Gorazd', 'Lesjak', '2.b', 'gorazd.lesjak26@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(30, 'dijak', 'Helena', 'Zver', '2.b', 'helena.zver37@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(31, 'dijak', 'Ivan', 'Bergant', '3.b', 'ivan.bergant48@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(32, 'dijak', 'Jasna', 'Dolenc', '3.b', 'jasna.dolenc59@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(33, 'dijak', 'Katarina', 'Fras', '3.b', 'katarina.fras60@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(34, 'dijak', 'Luka', 'Golob', '3.b', 'luka.golob71@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(35, 'dijak', 'Maja', 'Hribernik', '3.b', 'maja.hribernik82@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(36, 'dijak', 'Nejc', 'Ilić', '4.b', 'nejc.ilic93@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(37, 'dijak', 'Olga', 'Jelen', '4.b', 'olga.jelen16@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(38, 'dijak', 'Peter', 'Kokalj', '4.b', 'peter.kokalj27@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(39, 'dijak', 'Rebeka', 'Lah', '4.b', 'rebeka.lah38@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(40, 'dijak', 'Simon', 'Medved', '4.b', 'simon.medved49@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(41, 'dijak', 'Tina', 'Nova', '1.c', 'tina.nova50@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(42, 'dijak', 'Urban', 'Oman', '1.c', 'urban.oman61@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(43, 'dijak', 'Vesna', 'Pavlin', '1.c', 'vesna.pavlin72@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(44, 'dijak', 'Žan', 'Rupnik', '1.c', 'zan.rupnik83@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(45, 'dijak', 'Ajda', 'Stanković', '1.c', 'ajda.stankovic94@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(46, 'dijak', 'Boris', 'Tavčar', '2.c', 'boris.tavcar17@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(47, 'dijak', 'Cvetka', 'Uršič', '2.c', 'cvetka.ursic28@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(48, 'dijak', 'Dejan', 'Vidovič', '2.c', 'dejan.vidovic39@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(49, 'dijak', 'Erika', 'Zorman', '2.c', 'erika.zorman51@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(50, 'dijak', 'Frane', 'Žnidaršič', '2.c', 'frane.znidarsic62@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(51, 'dijak', 'Gregor', 'Anžlovar', '3.c', 'gregor.anzlovar73@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(52, 'dijak', 'Hedvika', 'Bevc', '3.c', 'hedvika.bevc84@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(53, 'dijak', 'Iztok', 'Cvetko', '3.c', 'iztok.cvetko95@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(54, 'dijak', 'Jelka', 'Drevenšek', '3.c', 'jelka.drevensek18@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(55, 'dijak', 'Karlo', 'Furlan', '3.c', 'karlo.furlan29@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(56, 'dijak', 'Lea', 'Gorenc', '4.c', 'lea.gorenc40@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(57, 'dijak', 'Milan', 'Hozjan', '4.c', 'milan.hozjan52@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(58, 'dijak', 'Nina', 'Ivančič', '4.c', 'nina.ivancic63@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(59, 'dijak', 'Oton', 'Jug', '4.c', 'oton.jug74@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(60, 'dijak', 'Pia', 'Kobal', '4.c', 'pia.kobal85@student.esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(61, 'profesor', 'Marko', 'Petrič', NULL, 'marko.petric@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(62, 'profesor', 'Alenka', 'Kovač', NULL, 'alenka.kovac@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(63, 'profesor', 'Mojca', 'Horvat', NULL, 'mojca.horvat@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(64, 'profesor', 'Peter', 'Novak', NULL, 'peter.novak@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(65, 'profesor', 'Ana', 'Zupan', NULL, 'ana.zupan@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(66, 'profesor', 'Robert', 'Krajnc', NULL, 'robert.krajnc@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(67, 'profesor', 'Marjan', 'Potokar', NULL, 'marjan.potokar@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(68, 'profesor', 'Katarina', 'Sever', NULL, 'katarina.sever@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(69, 'profesor', 'Branko', 'Jereb', NULL, 'branko.jereb@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(70, 'profesor', 'Damjan', 'Vogrin', NULL, 'damjan.vogrin@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(71, 'profesor', 'Eva', 'Rozman', NULL, 'eva.rozman@esola.si', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-30 11:29:29'),
(72, 'dijak', 'anja', 'test', '1.a', 'anja.test@student.esola.com', '$2y$10$Lwe7jhzh7G01uuV3A6jvPObDLrmZELZm7Nan2943xU4/PZMXRAwaa', '2025-10-31 20:57:02'),
(73, 'dijak', 'urh', 'roserrr', '4.b', 'urh.test@dijak.esola.com', '$2y$10$rKxDClKGQ9o4oNkXH7bfmOjjN7uEOPx1qcmtW34OLOyeLzAxUH/I2', '2025-11-01 13:46:10'),
(74, 'profesor', 'matic', 'holobar', '', 'matic.test@esola.com', '$2y$10$2I5J/WMY1C1eXh2TPq0d7.vd.q6fUb8nCPvWkCWMHbZKWRR7Lv9te', '2025-11-01 19:20:09'),
(75, 'administrator', 'zan', 'teleban', '', 'teleban@esola.com', '$2y$10$OBD4kpZ2WtrQ8IbNbZjbYe7iCQYdiuNfjXuN.6OnGnPyF20pRim1C', '2025-11-01 19:58:21');

-- --------------------------------------------------------

--
-- Struktura tabele `vsi_predmeti`
--

CREATE TABLE `vsi_predmeti` (
  `id` int NOT NULL,
  `ime_predmeta` varchar(100) NOT NULL,
  `opis` text,
  `tip_programa` enum('gimnazija','tehniska','vsi') DEFAULT NULL,
  `letnik` enum('1','2','3','4','vsi') DEFAULT 'vsi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `vsi_predmeti`
--

INSERT INTO `vsi_predmeti` (`id`, `ime_predmeta`, `opis`, `tip_programa`, `letnik`) VALUES
(1, 'Slovenščina', 'Spoznavanje pisateljev in pesnikov', 'vsi', 'vsi'),
(2, 'Matematika', 'Osnove algebre, geometrije in računanja', 'vsi', 'vsi'),
(3, 'Angleščina', 'Učenje časov, esejev in ponovitev', 'vsi', 'vsi'),
(4, 'Zgodovina', 'Preučevanje zgodovinskih dogodkov', 'vsi', 'vsi'),
(5, 'Geografija', 'Raziskovanje Zemje in njenih lastnosti', 'vsi', 'vsi'),
(6, 'Biologija', 'Preučevanje živih organizmov', 'vsi', 'vsi'),
(7, 'Kemija', 'Preučevanje snovi in njihovih lastnosti', 'vsi', 'vsi'),
(8, 'Fizika', 'Preučevanje naravnih zakonitosti', 'vsi', 'vsi'),
(9, 'Latinski jezik', 'Učenje klasičnega jezika', 'gimnazija', 'vsi'),
(10, 'Filozofija', 'Preučevanje temeljnih vprašanj', 'gimnazija', 'vsi'),
(11, 'Računalniški praktikum', 'Programiranje na višjem nivoju', 'tehniska', 'vsi'),
(12, 'Stroka moderne vsebine', 'Uvod v programiranje in spletni razvoj', 'tehniska', 'vsi'),
(13, 'Napredna uporaba podatkovnih baz', 'Delov s podatkovnimi bazami in SQL', 'tehniska', 'vsi'),
(14, 'Digitalna tehnologija', 'Osnove digitalne tehnologije', 'tehniska', 'vsi'),
(15, 'Slovenščina', 'Spoznavanje pisateljev in pesnikov', 'vsi', 'vsi'),
(16, 'Matematika', 'Osnove algebre, geometrije in računanja', 'vsi', 'vsi'),
(17, 'Angleščina', 'Učenje časov, esejev in ponovitev', 'vsi', 'vsi'),
(18, 'Zgodovina', 'Preučevanje zgodovinskih dogodkov', 'vsi', 'vsi'),
(19, 'Geografija', 'Raziskovanje Zemje in njenih lastnosti', 'vsi', 'vsi'),
(20, 'Biologija', 'Preučevanje živih organizmov', 'vsi', 'vsi'),
(21, 'Kemija', 'Preučevanje snovi in njihovih lastnosti', 'vsi', 'vsi'),
(22, 'Fizika', 'Preučevanje naravnih zakonitosti', 'vsi', 'vsi'),
(23, 'Latinski jezik', 'Učenje klasičnega jezika', 'gimnazija', 'vsi'),
(24, 'Filozofija', 'Preučevanje temeljnih vprašanj', 'gimnazija', 'vsi'),
(25, 'Računalniški praktikum', 'Programiranje na višjem nivoju', 'tehniska', 'vsi'),
(26, 'Stroka moderne vsebine', 'Uvod v programiranje in spletni razvoj', 'tehniska', 'vsi'),
(27, 'Napredna uporaba podatkovnih baz', 'Delov s podatkovnimi bazami in SQL', 'tehniska', 'vsi'),
(28, 'Digitalna tehnologija', 'Osnove digitalne tehnologije', 'tehniska', 'vsi');

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `dijak_predmeti`
--
ALTER TABLE `dijak_predmeti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dijak_id` (`dijak_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indeksi tabele `dijak_profil`
--
ALTER TABLE `dijak_profil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dijak_id` (`dijak_id`);

--
-- Indeksi tabele `naloge`
--
ALTER TABLE `naloge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `predmet_id` (`predmet_id`),
  ADD KEY `created_by_profesor_id` (`created_by_profesor_id`);

--
-- Indeksi tabele `oddaje_nalog`
--
ALTER TABLE `oddaje_nalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `naloga_id` (`naloga_id`),
  ADD KEY `dijak_id` (`dijak_id`);

--
-- Indeksi tabele `oddane_naloge`
--
ALTER TABLE `oddane_naloge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dijak_id` (`dijak_id`);

--
-- Indeksi tabele `predmeti`
--
ALTER TABLE `predmeti`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `profesorji_predmeti`
--
ALTER TABLE `profesorji_predmeti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indeksi tabele `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksi tabele `vsi_predmeti`
--
ALTER TABLE `vsi_predmeti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `dijak_predmeti`
--
ALTER TABLE `dijak_predmeti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT tabele `dijak_profil`
--
ALTER TABLE `dijak_profil`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT tabele `naloge`
--
ALTER TABLE `naloge`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabele `oddaje_nalog`
--
ALTER TABLE `oddaje_nalog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabele `oddane_naloge`
--
ALTER TABLE `oddane_naloge`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabele `predmeti`
--
ALTER TABLE `predmeti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT tabele `profesorji_predmeti`
--
ALTER TABLE `profesorji_predmeti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT tabele `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT tabele `vsi_predmeti`
--
ALTER TABLE `vsi_predmeti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `dijak_predmeti`
--
ALTER TABLE `dijak_predmeti`
  ADD CONSTRAINT `dijak_predmeti_ibfk_1` FOREIGN KEY (`dijak_id`) REFERENCES `uporabniki` (`id`),
  ADD CONSTRAINT `dijak_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `vsi_predmeti` (`id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `dijak_profil`
--
ALTER TABLE `dijak_profil`
  ADD CONSTRAINT `dijak_profil_ibfk_1` FOREIGN KEY (`dijak_id`) REFERENCES `uporabniki` (`id`);

--
-- Omejitve za tabelo `naloge`
--
ALTER TABLE `naloge`
  ADD CONSTRAINT `naloge_ibfk_1` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`),
  ADD CONSTRAINT `naloge_ibfk_2` FOREIGN KEY (`created_by_profesor_id`) REFERENCES `uporabniki` (`id`);

--
-- Omejitve za tabelo `oddaje_nalog`
--
ALTER TABLE `oddaje_nalog`
  ADD CONSTRAINT `oddaje_nalog_ibfk_1` FOREIGN KEY (`naloga_id`) REFERENCES `naloge` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oddaje_nalog_ibfk_2` FOREIGN KEY (`dijak_id`) REFERENCES `uporabniki` (`id`);

--
-- Omejitve za tabelo `oddane_naloge`
--
ALTER TABLE `oddane_naloge`
  ADD CONSTRAINT `oddane_naloge_ibfk_1` FOREIGN KEY (`dijak_id`) REFERENCES `uporabniki` (`id`);

--
-- Omejitve za tabelo `profesorji_predmeti`
--
ALTER TABLE `profesorji_predmeti`
  ADD CONSTRAINT `profesorji_predmeti_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `uporabniki` (`id`),
  ADD CONSTRAINT `profesorji_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
