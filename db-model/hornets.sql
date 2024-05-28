-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pon 14. srp 2023, 18:59
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
  `desc` varchar(200) DEFAULT NULL,
  `text` mediumtext NOT NULL,
  `User_id` int(11) NOT NULL,
  `created_at` varchar(300) DEFAULT NULL,
  `updated_at` varchar(300) DEFAULT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `level` int(11) NOT NULL,
  `desc` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `verified` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  ADD KEY `fk_Blogs_User1_idx` (`User_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT pro tabulku `securitylevel`
--
ALTER TABLE `securitylevel`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

--
-- Omezení pro tabulku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_Users_SecurityLevel` FOREIGN KEY (`SecurityLevel_id`) REFERENCES `mydb`.`securitylevel` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
