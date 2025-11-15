-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 12:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `silent_evidence`
--

-- --------------------------------------------------------

--
-- Table structure for table `carousel_slides`
--

CREATE TABLE `carousel_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel_slides`
--

INSERT INTO `carousel_slides` (`id`, `title`, `caption`, `image_url`, `sort_order`, `is_active`) VALUES
(1, 'Do Not Look Behind You', 'Some stories follow you home.', 'https://images.unsplash.com/photo-1484704849700-f032a568e944?q=80', 1, 1),
(2, 'Voices in the Attic', 'You are not alone in your own house.', 'https://images.unsplash.com/photo-1500080209535-717dd4ebaa6b?q=80', 2, 1),
(3, 'The Hallway That Watches You', 'You only see it when the lights are off.', 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?q=80', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `cat_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `parent` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cat_key`, `label`, `tag`, `description`, `parent`) VALUES
(1, 'true', 'True stories', 'TRUE', 'Real experiences users claim actually happened.', NULL),
(2, 'paranormal', 'Paranormal', 'PARANORMAL', 'Ghosts, spirits, haunted houses, cursed objects.', NULL),
(3, 'urban', 'Urban legends', 'URBAN', 'Stories that spread online and feel too real.', NULL),
(4, 'short', 'Short nightmares', 'SHORT', 'Quick reads that hit fast.', NULL),
(5, 'haunted', 'Haunted places', 'HAUNTED', 'Real locations with disturbing history.', 'paranormal'),
(6, 'ghosts', 'Ghost encounters', 'GHOSTS', 'Unexplainable sightings and hauntings.', 'paranormal'),
(7, 'missing', 'Missing persons', 'MISSING', 'Cases that leave more questions than answers.', 'true'),
(8, 'crime', 'Crime and mystery', 'CRIME', 'Dark events that defy explanation.', 'true'),
(9, 'sleep', 'Sleep paralysis', 'SLEEP', 'The figures you cannot move away from.', 'paranormal'),
(10, 'forest', 'Forest horror', 'FOREST', 'What hides between the trees.', 'paranormal'),
(11, 'night', 'Night shift stories', 'NIGHT', 'Late hours that get way too strange.', 'true'),
(12, 'calls', 'Strange phone calls', 'CALLS', 'Voices that should not exist.', 'urban'),
(13, 'creatures', 'Creature sightings', 'CREATURES', 'Encounters with things not human.', 'urban'),
(14, 'abandoned', 'Abandoned places', 'ABANDONED', 'Ruins that feel alive inside.', 'paranormal'),
(15, 'psychological', 'Psychological horror', 'PSYCHO', 'Mind-bending stories that mess with your head.', 'short');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `show_latest` tinyint(1) NOT NULL DEFAULT 1,
  `show_popular` tinyint(1) NOT NULL DEFAULT 1,
  `show_featured` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_settings`
--

INSERT INTO `homepage_settings` (`id`, `show_latest`, `show_popular`, `show_featured`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-11-15 09:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category` enum('true','paranormal','urban','short') NOT NULL DEFAULT 'true',
  `content` longtext NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `likes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `title`, `slug`, `category`, `content`, `is_published`, `is_featured`, `views`, `likes`, `created_at`, `updated_at`) VALUES
(1, 2, 'h ;jk k;j kn', 'h-jk-kj-kn', 'true', '; bjnlnni\'k', 1, 0, 1, 0, '2025-11-15 10:59:33', '2025-11-15 14:06:55'),
(2, 2, 'brgbarbr', 'brgbarbr', 'true', 'bzdrfbrrwe', 1, 0, 17, 0, '2025-11-15 11:00:40', '2025-11-15 19:45:01');

-- --------------------------------------------------------

--
-- Table structure for table `story_bookmarks`
--

CREATE TABLE `story_bookmarks` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_comments`
--

CREATE TABLE `story_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `story_comments`
--

INSERT INTO `story_comments` (`id`, `story_id`, `user_id`, `content`, `created_at`) VALUES
(2, 2, 1, 'test one two tree', '2025-11-15 14:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `story_likes`
--

CREATE TABLE `story_likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `display_name`, `avatar`, `bio`, `role`, `created_at`, `last_login`) VALUES
(1, 'chrismolina', 'chris@chris.com', '$2y$10$GyO.1rYTydKDmmVk49at2uSnxvLDm4YBbD0/Dr5NO1E/i8mRPDfui', 'chris molina', 'uploads/avatars/avatar_1_1763198680.jpg', '', 'admin', '2025-11-15 10:19:37', '2025-11-15 11:05:32'),
(2, 'testtest', 'test@test.com', '$2y$10$E9NT4Y1RWYFavJ2FFPG7V.3Lr4DhMUguizsZgs3aXoJyCDgLC5D..', 'test test', 'uploads/avatars/avatar_2_1763200718.webp', '', 'user', '2025-11-15 10:57:57', '2025-11-15 10:58:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat_key` (`cat_key`);

--
-- Indexes for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slug` (`slug`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `story_bookmarks`
--
ALTER TABLE `story_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bookmarks_user_story` (`user_id`,`story_id`);

--
-- Indexes for table `story_comments`
--
ALTER TABLE `story_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `story_likes`
--
ALTER TABLE `story_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_likes_user_story` (`user_id`,`story_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_username` (`username`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `story_bookmarks`
--
ALTER TABLE `story_bookmarks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `story_comments`
--
ALTER TABLE `story_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `story_likes`
--
ALTER TABLE `story_likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `fk_stories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
