-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 24, 2026 alle 09:06
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfolio`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$M6EkO/XK8gP/Rk.zGhzsdevjKC6BaLsmDfWq/erws.Q20PX14jzMi');

-- --------------------------------------------------------

--
-- Struttura della tabella `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descrizione` text NOT NULL,
  `tipo` enum('scolastico','personale') NOT NULL DEFAULT 'personale',
  `modalita` enum('singolo','gruppo') NOT NULL DEFAULT 'singolo',
  `periodo` varchar(100) DEFAULT NULL,
  `demo_url` varchar(300) DEFAULT NULL,
  `github_url` varchar(300) DEFAULT NULL,
  `doc_filename` varchar(255) DEFAULT NULL,
  `doc_type` enum('pdf','md') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `projects`
--

INSERT INTO `projects` (`id`, `nome`, `descrizione`, `tipo`, `modalita`, `periodo`, `demo_url`, `github_url`, `doc_filename`, `doc_type`, `created_at`) VALUES
(1, 'Menù Digitale - Database', 'Web app per la gestione digitale del menù di un locale. Il gestore accede tramite login e può creare menù, categorie e piatti, attivandoli o disattivandoli in tempo reale. Genera un QR code che i clienti inquadrano per visualizzare il menù aggiornato, senza installare nulla.\r\n\r\nCosa ho imparato:\r\n- generazione di QR code lato server\r\n- gestione ruoli e sessioni in PHP\r\n- strutturazione di un pannello admin CRUD completo', 'scolastico', 'gruppo', 'Gennaio 2026', 'https://menudigitale.page.gd/', 'https://github.com/manzonithomas/Manzoni_Sesana_Travellini_Menu_Digitale', NULL, NULL, '2026-03-23 19:38:53'),
(2, 'Lotteria digitale - Database', 'Web app che simula una lotteria digitale con due tipi di account: gestore e utente. Gli utenti acquistano biglietti numerati progressivamente, scalando i propri crediti. Ogni lotteria è a tempo: alla scadenza, il gestore può estrarre il vincitore oppure rimborsare tutti i partecipanti se non è stato raggiunto il minimo di biglietti venduti.\r\n\r\nCosa ho imparato:\r\n- invio email tramite Composer (PHPMailer) con server SMTP locale PaperCut per il testing\r\n- Cron Job su XAMPP per la gestione automatica delle scadenze', 'scolastico', 'gruppo', 'Febbraio 2026', NULL, 'https://github.com/manzonithomas/Manzoni_Sesana_Paggi-Lotteria', NULL, NULL, '2026-03-23 19:44:06'),
(3, 'F1 API Dashboard', 'Dashboard sulla Formula 1 che consuma l\'API ufficiale OpenF1 in tempo reale. Permette di navigare gare, classifiche piloti e costruttori, dettagli circuiti con meteo e Google Maps, e comunicazioni radio dei team — protette da login. Include un sistema di cache su localStorage con fallback automatico quando l\'API non risponde, e due minigiochi interattivi.\r\n\r\nCosa ho imparato:\r\n- gestione asincrona con Fetch API e Async/Await\r\n- sistema di cache con localStorage e strategie di fallback\r\n- manipolazione dinamica del DOM su un progetto multi-pagina', 'scolastico', 'gruppo', 'Febbraio 2026', 'https://apif1tps.vercel.app/', 'https://github.com/manzonithomas/Sesana_Manzoni-ProgTPS_API', 'doc_69c18cfba47a32.38155294.md', 'md', '2026-03-23 19:49:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `project_photos`
--

CREATE TABLE `project_photos` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `project_photos`
--

INSERT INTO `project_photos` (`id`, `project_id`, `filename`, `is_cover`, `sort_order`) VALUES
(1, 3, 'proj_69c18bb467f085.06473765.png', 1, 0),
(2, 3, 'proj_69c18bdd8e1e88.61522180.png', 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `project_techs`
--

CREATE TABLE `project_techs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `tech` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `project_techs`
--

INSERT INTO `project_techs` (`id`, `project_id`, `tech`) VALUES
(85, 2, 'PHP'),
(86, 2, 'MySQL'),
(87, 2, 'HTML'),
(88, 2, 'CSS'),
(89, 2, 'JavaScript'),
(90, 2, 'PHPMailer'),
(91, 2, 'Composer'),
(92, 2, 'PaperCut SMTP'),
(93, 1, 'PHP'),
(94, 1, 'MySQL'),
(95, 1, 'HTML'),
(96, 1, 'CSS'),
(97, 1, 'JavaScript'),
(98, 1, 'W3.CSS'),
(105, 3, 'HTML5'),
(106, 3, 'CSS3'),
(107, 3, 'JavaScript ES6+'),
(108, 3, 'OpenF1 API'),
(109, 3, 'Fetch API'),
(110, 3, 'LocalStorage');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indici per le tabelle `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `project_photos`
--
ALTER TABLE `project_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indici per le tabelle `project_techs`
--
ALTER TABLE `project_techs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `project_photos`
--
ALTER TABLE `project_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `project_techs`
--
ALTER TABLE `project_techs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `project_photos`
--
ALTER TABLE `project_photos`
  ADD CONSTRAINT `project_photos_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `project_techs`
--
ALTER TABLE `project_techs`
  ADD CONSTRAINT `project_techs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
