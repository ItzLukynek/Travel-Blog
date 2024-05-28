-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Ned 24. zář 2023, 09:40
-- Verze serveru: 10.4.24-MariaDB
-- Verze PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `hornets`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `blogcomments`
--

CREATE TABLE `blogcomments` (
  `id` int(11) NOT NULL,
  `text` varchar(2000) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `created_at` varchar(300) NOT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `deleted_at` varchar(45) DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `Blogs_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `blogphotos`
--

CREATE TABLE `blogphotos` (
  `id` int(11) NOT NULL,
  `url` varchar(400) NOT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  `deleted_at` varchar(45) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `Blogs_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `desc` varchar(300) DEFAULT NULL,
  `text` varchar(15000) NOT NULL,
  `User_id` int(11) NOT NULL,
  `created_at` varchar(300) DEFAULT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `active` int(11) NOT NULL,
  `Destination_id` varchar(50) NOT NULL,
  `concept` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `blogs`
--

INSERT INTO `blogs` (`id`, `desc`, `text`, `User_id`, `created_at`, `updated_at`, `active`, `Destination_id`, `concept`) VALUES
(49, 'hh', 'haha', 17, 'Tue-08-23 22:47:37', 'Tue-09-23 13:13:04', 1, 'AO', 0),
(50, 'koko', 'nemám tě rád                                                    ', 17, 'Fri-09-23 12:51:11', 'Mon-09-23 08:42:00', 1, 'AO', 0),
(80, 'ge', 'gegege', 17, 'Tue-09-23 17:20:58', 'Tue-09-23 17:20:58', 1, 'AO', 0),
(81, 'g', 'gd', 17, 'Wed-09-23 20:01:03', 'Wed-09-23 20:01:03', 1, 'DZ', 0),
(84, 'hi', 'mmm', 17, 'Thu-09-23 19:19:29', 'Sat-09-23 16:35:34', 0, 'AR', 1),
(85, 'gg', 'gg', 17, 'Sat-09-23 15:57:53', 'Sat-09-23 15:57:53', 1, 'AO', 0),
(86, 'g', 'g', 17, 'Sat-09-23 16:00:22', 'Sat-09-23 16:00:22', 1, 'AR', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `destination`
--

CREATE TABLE `destination` (
  `id` varchar(50) NOT NULL,
  `EnglishName` varchar(100) NOT NULL,
  `CzechName` varchar(100) NOT NULL,
  `Shortcut` varchar(10) NOT NULL,
  `Active` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `destination`
--

INSERT INTO `destination` (`id`, `EnglishName`, `CzechName`, `Shortcut`, `Active`) VALUES
('0', '0', '0', '0', '0'),
('AE', 'United Arab Emirates', 'Spojené arabské emiráty', 'UAE', '0'),
('AF', 'Afghanistan', 'Afghánistán', 'AFG', '0'),
('AL', 'Albania', 'Albánie', 'ALB', '0'),
('AM', 'Armenia', 'Arménie', 'ARM', '0'),
('AO', 'Angola', 'Angola', 'AGO', '0'),
('AR', 'Argentina', 'Argentina', 'ARG', '0'),
('AT', 'Austria', 'Rakousko', 'AUT', '0'),
('AU', 'Australia', 'Austrálie', 'AUS', '0'),
('AZ', 'Azerbaijan', 'Ázerbájdžán', 'AZE', '0'),
('BA', 'Bosnia and Herzegovina', 'Bosna a Hercegovina', 'BIH', '0'),
('BD', 'Bangladesh', 'Bangladéš', 'BGD', '0'),
('BE', 'Belgium', 'Belgie', 'BEL', '0'),
('BF', 'Burkina Faso', 'Burkina Faso', 'BFA', '0'),
('BG', 'Bulgaria', 'Bulharsko', 'BGR', '0'),
('BI', 'Burundi', 'Burundi', 'BDI', '0'),
('BJ', 'Benin', 'Benin', 'BEN', '0'),
('BN', 'Brunei Darussalam', 'Brunej', 'BRN', '0'),
('BO', 'Bolivia', 'Bolívie', 'BOL', '0'),
('BR', 'Brazil', 'Brazílie', 'BRA', '0'),
('BS', 'Bahamas', 'Bahamy', 'BHS', '0'),
('BT', 'Bhutan', 'Bhútán', 'BTN', '0'),
('BW', 'Botswana', 'Botswana', 'BWA', '0'),
('BY', 'Belarus', 'Bělorusko', 'BLR', '0'),
('BZ', 'Belize', 'Belize', 'BLZ', '0'),
('CA', 'Canada', 'Kanada', 'CAN', '0'),
('CD', 'Democratic Republic of the Congo', 'Demokratická republika Kongo', 'COD', '0'),
('CF', 'Central African Republic', 'Středoafrická republika', 'CAF', '0'),
('CG', 'Republic of the Congo', 'Republika Kongo', 'COG', '0'),
('CH', 'Switzerland', 'Švýcarsko', 'CHE', '0'),
('CI', 'Côte d\'Ivoire', 'Pobřeží slonoviny', 'CIV', '0'),
('CL', 'Chile', 'Chile', 'CHL', '0'),
('CM', 'Cameroon', 'Kamerun', 'CMR', '0'),
('CN', 'China', 'Čína', 'CHN', '0'),
('CO', 'Colombia', 'Kolumbie', 'COL', '0'),
('CR', 'Costa Rica', 'Kostarika', 'CRI', '0'),
('CU', 'Cuba', 'Kuba', 'CUB', '0'),
('CY', 'Cyprus', 'Kypr', 'CYP', '0'),
('CZ', 'Czechia', 'Česko', 'CZE', '0'),
('DE', 'Germany', 'Německo', 'DEU', '0'),
('DJ', 'Djibouti', 'Džibutsko', 'DJI', '0'),
('DK', 'Denmark', 'Dánsko', 'DNK', '0'),
('DO', 'Dominican Republic', 'Dominikánská republika', 'DOM', '0'),
('DZ', 'Algeria', 'Alžírsko', 'DZA', '0'),
('EC', 'Ecuador', 'Ekvádor', 'ECU', '0'),
('EE', 'Estonia', 'Estonsko', 'EST', '0'),
('EG', 'Egypt', 'Egypt', 'EGY', '0'),
('EH', 'Western Sahara', 'Západní Sahara', 'ESH', '0'),
('ER', 'Eritrea', 'Eritrea', 'ERI', '0'),
('ES', 'Spain', 'Španělsko', 'ESP', '0'),
('ET', 'Ethiopia', 'Etiopie', 'ETH', '0'),
('FI', 'Finland', 'Finsko', 'FIN', '0'),
('FJ', 'Fiji', 'Fidži', 'FJI', '0'),
('FK', 'Falkland Islands', 'Falklandské ostrovy', 'FLK', '0'),
('FR', 'France', 'Francie', 'FRA', '0'),
('GA', 'Gabon', 'Gabon', 'GAB', '0'),
('GB', 'United Kingdom', 'Spojené království', 'GBR', '0'),
('GE', 'Georgia', 'Gruzie', 'GEO', '0'),
('GF', 'French Guiana', 'Francouzská Guyana', 'GUF', '0'),
('GH', 'Ghana', 'Ghana', 'GHA', '0'),
('GL', 'Greenland', 'Grónsko', 'GRL', '0'),
('GM', 'Gambia', 'Gambie', 'GMB', '0'),
('GN', 'Guinea', 'Guinea', 'GIN', '0'),
('GQ', 'Equatorial Guinea', 'Rovníková Guinea', 'GNQ', '0'),
('GR', 'Greece', 'Řecko', 'GRC', '0'),
('GT', 'Guatemala', 'Guatemala', 'GTM', '0'),
('GW', 'Guinea-Bissau', 'Guinea-Bissau', 'GNB', '0'),
('GY', 'Guyana', 'Guyana', 'GUY', '0'),
('HN', 'Honduras', 'Honduras', 'HND', '0'),
('HR', 'Croatia', 'Chorvatsko', 'HRV', '0'),
('HT', 'Haiti', 'Haiti', 'HTI', '0'),
('HU', 'Hungary', 'Maďarsko', 'HUN', '0'),
('ID', 'Indonesia', 'Indonésie', 'IDN', '0'),
('IE', 'Ireland', 'Irsko', 'IRL', '0'),
('IL', 'Israel', 'Izrael', 'ISR', '0'),
('IN', 'India', 'Indie', 'IND', '0'),
('IQ', 'Iraq', 'Irák', 'IRQ', '0'),
('IR', 'Iran', 'Írán', 'IRN', '0'),
('IS', 'Iceland', 'Island', 'ISL', '0'),
('IT', 'Italy', 'Itálie', 'ITA', '0'),
('JM', 'Jamaica', 'Jamajka', 'JAM', '0'),
('JO', 'Jordan', 'Jordánsko', 'JOR', '0'),
('JP', 'Japan', 'Japonsko', 'JPN', '0'),
('KE', 'Kenya', 'Keňa', 'KEN', '0'),
('KG', 'Kyrgyzstan', 'Kyrgyzstán', 'KGZ', '0'),
('KH', 'Cambodia', 'Kambodža', 'KHM', '0'),
('KP', 'North Korea', 'Severní Korea', 'PRK', '0'),
('KR', 'South Korea', 'Jižní Korea', 'KOR', '0'),
('KW', 'Kuwait', 'Kuvajt', 'KWT', '0'),
('KZ', 'Kazakhstan', 'Kazachstán', 'KAZ', '0'),
('LA', 'Lao People\'s Democratic Republic', 'Laos', 'LAO', '0'),
('LB', 'Lebanon', 'Libanon', 'LBN', '0'),
('LK', 'Sri Lanka', 'Srí Lanka', 'LKA', '0'),
('LR', 'Liberia', 'Libérie', 'LBR', '0'),
('LS', 'Lesotho', 'Lesotho', 'LSO', '0'),
('LT', 'Lithuania', 'Litva', 'LTU', '0'),
('LU', 'Luxembourg', 'Lucembursko', 'LUX', '0'),
('LV', 'Latvia', 'Lotyšsko', 'LVA', '0'),
('LY', 'Libya', 'Libye', 'LBY', '0'),
('MA', 'Morocco', 'Maroko', 'MAR', '0'),
('MD', 'Moldova', 'Moldavsko', 'MDA', '0'),
('ME', 'Montenegro', 'Černá Hora', 'MNE', '0'),
('MG', 'Madagascar', 'Madagaskar', 'MDG', '0'),
('MK', 'Macedonia', 'Severní Makedonie', 'MKD', '0'),
('ML', 'Mali', 'Mali', 'MLI', '0'),
('MM', 'Myanmar', 'Myanmar', 'MMR', '0'),
('MN', 'Mongolia', 'Mongolsko', 'MNG', '0'),
('MR', 'Mauritania', 'Mauritánie', 'MRT', '0'),
('MW', 'Malawi', 'Malawi', 'MWI', '0'),
('MX', 'Mexico', 'Mexiko', 'MEX', '0'),
('MY', 'Malaysia', 'Malajsie', 'MYS', '0'),
('MZ', 'Mozambique', 'Mosambik', 'MOZ', '0'),
('NA', 'Namibia', 'Namibie', 'NAM', '0'),
('NC', 'New Caledonia', 'Nová Kaledonie', 'NCL', '0'),
('NE', 'Niger', 'Niger', 'NER', '0'),
('NG', 'Nigeria', 'Nigérie', 'NGA', '0'),
('NI', 'Nicaragua', 'Nikaragua', 'NIC', '0'),
('NL', 'Netherlands', 'Nizozemsko', 'NLD', '0'),
('NO', 'Norway', 'Norsko', 'NOR', '0'),
('NP', 'Nepal', 'Nepál', 'NPL', '0'),
('NZ', 'New Zealand', 'Nový Zéland', 'NZL', '0'),
('OM', 'Oman', 'Omán', 'OMN', '0'),
('PA', 'Panama', 'Panama', 'PAN', '0'),
('PE', 'Peru', 'Peru', 'PER', '0'),
('PG', 'Papua New Guinea', 'Papua-Nová Guinea', 'PNG', '0'),
('PH', 'Philippines', 'Filipíny', 'PHL', '0'),
('PK', 'Pakistan', 'Pákistán', 'PAK', '0'),
('PL', 'Poland', 'Polsko', 'POL', '0'),
('PR', 'Puerto Rico', 'Portoriko', 'PRI', '0'),
('PS', 'Palestinian Territories', 'Palestinská území', 'PSE', '0'),
('PT', 'Portugal', 'Portugalsko', 'PRT', '0'),
('PY', 'Paraguay', 'Paraguay', 'PRY', '0'),
('QA', 'Qatar', 'Katar', 'QAT', '0'),
('RO', 'Romania', 'Rumunsko', 'ROU', '0'),
('RS', 'Serbia', 'Srbsko', 'SRB', '0'),
('RU', 'Russia', 'Rusko', 'RUS', '0'),
('RW', 'Rwanda', 'Rwanda', 'RWA', '0'),
('SA', 'Saudi Arabia', 'Saúdská Arábie', 'SAU', '0'),
('SB', 'Solomon Islands', 'Šalamounovy ostrovy', 'SLB', '0'),
('SD', 'Sudan', 'Súdán', 'SDN', '0'),
('SE', 'Sweden', 'Švédsko', 'SWE', '0'),
('SI', 'Slovenia', 'Slovinsko', 'SVN', '0'),
('SJ', 'Svalbard and Jan Mayen', 'Špicberky a Jan Mayen', 'SJM', '0'),
('SK', 'Slovakia', 'Slovensko', 'SVK', '0'),
('SL', 'Sierra Leone', 'Sierra Leone', 'SLE', '0'),
('SN', 'Senegal', 'Senegal', 'SEN', '0'),
('SO', 'Somalia', 'Somálsko', 'SOM', '0'),
('SR', 'Suriname', 'Surinam', 'SUR', '0'),
('SS', 'South Sudan', 'Jižní Súdán', 'SSD', '0'),
('SV', 'El Salvador', 'Salvador', 'SLV', '0'),
('SY', 'Syria', 'Sýrie', 'SYR', '0'),
('SZ', 'Swaziland', 'Svazijsko', 'SWZ', '0'),
('TD', 'Chad', 'Čad', 'TCD', '0'),
('TF', 'French Southern and Antarctic Lands', 'Francouzská jižní a antarktická území', 'ATF', '0'),
('TG', 'Togo', 'Togo', 'TGO', '0'),
('TH', 'Thailand', 'Thajsko', 'THA', '0'),
('TJ', 'Tajikistan', 'Tádžikistán', 'TJK', '0'),
('TL', 'Timor-Leste', 'Východní Timor', 'TLS', '0'),
('TM', 'Turkmenistan', 'Turkmenistán', 'TKM', '0'),
('TN', 'Tunisia', 'Tunisko', 'TUN', '0'),
('TR', 'Turkey', 'Turecko', 'TUR', '0'),
('TT', 'Trinidad and Tobago', 'Trinidad a Tobago', 'TTO', '0'),
('TW', 'Taiwan', 'Tchaj-wan', 'TWN', '0'),
('TZ', 'Tanzania', 'Tanzanie', 'TZA', '0'),
('UA', 'Ukraine', 'Ukrajina', 'UKR', '0'),
('UG', 'Uganda', 'Uganda', 'UGA', '0'),
('US', 'United States', 'Spojené státy americké', 'USA', '0'),
('UY', 'Uruguay', 'Uruguay', 'URY', '0'),
('UZ', 'Uzbekistan', 'Uzbekistán', 'UZB', '0'),
('VE', 'Venezuela', 'Venezuela', 'VEN', '0'),
('VN', 'Vietnam', 'Vietnam', 'VNM', '0'),
('VU', 'Vanuatu', 'Vanuatu', 'VUT', '0'),
('XK', 'Kosovo', 'Kosovo', 'XKX', '0'),
('YE', 'Yemen', 'Jemen', 'YEM', '0'),
('ZA', 'South Africa', 'Jihoafrická republika', 'ZAF', '0'),
('ZM', 'Zambia', 'Zambie', 'ZMB', '0'),
('ZW', 'Zimbabwe', 'Zimbabwe', 'ZWE', '0');

-- --------------------------------------------------------

--
-- Struktura tabulky `photogallery`
--

CREATE TABLE `photogallery` (
  `id` int(11) NOT NULL,
  `created_at` varchar(300) NOT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `User_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `photogalleryphotos`
--

CREATE TABLE `photogalleryphotos` (
  `id` int(11) NOT NULL,
  `url` varchar(300) NOT NULL,
  `PhotoGallery_id` int(11) NOT NULL,
  `created_at` varchar(45) NOT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `securitylevel`
--

CREATE TABLE `securitylevel` (
  `id` int(11) NOT NULL,
  `desc` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `securitylevel`
--

INSERT INTO `securitylevel` (`id`, `desc`) VALUES
(0, 'Admin'),
(1, 'Blogger'),
(2, 'User'),
(3, 'Guest');

-- --------------------------------------------------------

--
-- Struktura tabulky `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `created_at` varchar(300) NOT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `active` varchar(45) NOT NULL,
  `User_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `storiesphotos`
--

CREATE TABLE `storiesphotos` (
  `id` int(11) NOT NULL,
  `url` varchar(300) NOT NULL,
  `Stories_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `LastName` varchar(300) NOT NULL,
  `FirstName` varchar(300) NOT NULL,
  `SecurityLevel_id` int(11) NOT NULL,
  `Email` varchar(300) NOT NULL,
  `verified` int(11) DEFAULT NULL,
  `password` varchar(400) NOT NULL,
  `created_at` varchar(300) DEFAULT NULL,
  `updated_at` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `LastName`, `FirstName`, `SecurityLevel_id`, `Email`, `verified`, `password`, `created_at`, `updated_at`) VALUES
(13, 'Korba', 'Lukáš', 0, 'luk.korba@gmail.com', 0, '$2y$10$UdRSolN4wx9EqGyqP0qh9Ogp6x3aHCBRPULTjx1wOW374oNROc3iq', NULL, NULL),
(14, 'Peterka', 'Dan', 2, 'admin@tofrci.cz', 0, '$2y$10$XOWsySTqx2QPHEcir/67MuDFSoSD3lwd3MdSviVL5Sw7Qvy0LBGFu', NULL, NULL),
(15, 'Pery', 'Ptakopysk', 2, 'Ptak@gmail.com', 0, '$2y$10$BqpY8kvaXoa4SN7s6KJJBut9b0O/.8x9qUomIUij.qpFXRPL2w1Qe', NULL, NULL),
(16, 'hehe', 'hehe', 2, 'lk@hh.cz', 0, '$2y$10$yEfVbFEzrwd.FcTbUsJ3Du6I98sf40qPM8TLq57JgiPDyOOotsg2K', NULL, NULL),
(17, 'cumlover', 'matěj', 0, 'ad@ad.ad', 0, '$2y$10$bo9vqPQcCPE8Q9gcas4gYO/kNQV8b8/BjwwzjiJGo7K09jLCHZa4y', NULL, NULL),
(18, 'Blogger', '', 1, 'gg@gg.gg', 0, '$2y$10$t9y07YKbjYIjML6GU0.PPeZ3YZ2KO3sD3wFfRyXrY/BNmw6LLz8uy', NULL, NULL);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `blogcomments`
--
ALTER TABLE `blogcomments`
  ADD PRIMARY KEY (`id`,`User_id`,`Blogs_id`),
  ADD KEY `fk_Comments_User1_idx` (`User_id`),
  ADD KEY `fk_Comments_Blogs1_idx` (`Blogs_id`);

--
-- Indexy pro tabulku `blogphotos`
--
ALTER TABLE `blogphotos`
  ADD PRIMARY KEY (`id`,`Blogs_id`),
  ADD KEY `fk_BlogPhotos_Blogs1_idx` (`Blogs_id`);

--
-- Indexy pro tabulku `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`,`User_id`),
  ADD KEY `fk_Blogs_User1_idx` (`User_id`),
  ADD KEY `fk_Blogs_Destination` (`Destination_id`);

--
-- Indexy pro tabulku `destination`
--
ALTER TABLE `destination`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `photogallery`
--
ALTER TABLE `photogallery`
  ADD PRIMARY KEY (`id`,`User_id`),
  ADD KEY `fk_PhotoGallery_User1_idx` (`User_id`);

--
-- Indexy pro tabulku `photogalleryphotos`
--
ALTER TABLE `photogalleryphotos`
  ADD PRIMARY KEY (`id`,`PhotoGallery_id`),
  ADD KEY `fk_PhotoGalleryPhotos_PhotoGallery1_idx` (`PhotoGallery_id`);

--
-- Indexy pro tabulku `securitylevel`
--
ALTER TABLE `securitylevel`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`,`User_id`),
  ADD KEY `fk_Stories_User1_idx` (`User_id`);

--
-- Indexy pro tabulku `storiesphotos`
--
ALTER TABLE `storiesphotos`
  ADD PRIMARY KEY (`id`,`Stories_id`),
  ADD KEY `fk_StoriesPhotos_Stories1_idx` (`Stories_id`);

--
-- Indexy pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`,`SecurityLevel_id`),
  ADD KEY `fk_Users_SecurityLevel_idx` (`SecurityLevel_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `blogcomments`
--
ALTER TABLE `blogcomments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `blogphotos`
--
ALTER TABLE `blogphotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT pro tabulku `photogallery`
--
ALTER TABLE `photogallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `photogalleryphotos`
--
ALTER TABLE `photogalleryphotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `storiesphotos`
--
ALTER TABLE `storiesphotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `blogcomments`
--
ALTER TABLE `blogcomments`
  ADD CONSTRAINT `fk_Comments_Blogs1` FOREIGN KEY (`Blogs_id`) REFERENCES `blogs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Comments_User1` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `blogphotos`
--
ALTER TABLE `blogphotos`
  ADD CONSTRAINT `fk_BlogPhotos_Blogs1` FOREIGN KEY (`Blogs_id`) REFERENCES `blogs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `fk_Blogs_Destination` FOREIGN KEY (`Destination_id`) REFERENCES `destination` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Blogs_User1` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `photogallery`
--
ALTER TABLE `photogallery`
  ADD CONSTRAINT `fk_PhotoGallery_User1` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `photogalleryphotos`
--
ALTER TABLE `photogalleryphotos`
  ADD CONSTRAINT `fk_PhotoGalleryPhotos_PhotoGallery1` FOREIGN KEY (`PhotoGallery_id`) REFERENCES `photogallery` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `fk_Stories_User1` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `storiesphotos`
--
ALTER TABLE `storiesphotos`
  ADD CONSTRAINT `fk_StoriesPhotos_Stories1` FOREIGN KEY (`Stories_id`) REFERENCES `stories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
