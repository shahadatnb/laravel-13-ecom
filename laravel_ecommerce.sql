-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 20, 2026 at 02:27 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `visibility` enum('public','hidden','homepage','featured') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `short_description`, `description`, `logo`, `banner`, `website`, `country`, `email`, `phone`, `address`, `sort_order`, `featured`, `status`, `visibility`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Samsung', 'samsung', 'Quia est aspernatur aspernatur similique consequatur assumenda maiores.', 'Aut iste iste consequatur doloremque qui illum facere vero. Minima voluptas unde sint culpa maxime sed aut. Debitis autem qui nemo aut. Esse iusto voluptatem a eveniet quia.', NULL, NULL, 'https://samsung.com', 'South Korea', NULL, NULL, NULL, 0, 1, 'active', 'public', 'Samsung - Official Store', 'Ea iure odit quo enim dolorem assumenda a eum atque odit perferendis natus eaque.', 'Samsung, electronics, shop, buy online', 'https://samsung.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 'Apple', 'apple', 'Eos eligendi omnis qui autem et et placeat unde omnis.', 'Eum eum voluptas esse rem adipisci. Qui repellat inventore perspiciatis veniam totam ad eos et.', NULL, NULL, 'https://apple.com', 'United States', NULL, NULL, NULL, 0, 1, 'active', 'public', 'Apple - Official Store', 'Sunt architecto voluptas dolore molestias qui qui et sint ratione id culpa dolorem tenetur aut est libero ut.', 'Apple, electronics, shop, buy online', 'https://apple.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, 'Sony', 'sony', 'Dolorem commodi magnam aut laudantium quia vitae praesentium eos est veniam amet molestiae.', 'Exercitationem incidunt deserunt quia laborum sit in esse. Voluptatum sed sit nostrum numquam distinctio dolores.', NULL, NULL, 'https://sony.com', 'Japan', NULL, NULL, NULL, 0, 1, 'active', 'public', 'Sony - Official Store', 'Modi ullam necessitatibus non praesentium voluptatum quaerat officiis delectus officia voluptate officia et quasi odio nobis reiciendis perspiciatis et.', 'Sony, electronics, shop, buy online', 'https://sony.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(4, 'Nike', 'nike', 'Reprehenderit nulla nemo nihil maiores ab omnis laborum iste eveniet maiores aut.', 'Est porro omnis temporibus ducimus neque aperiam nam. Voluptatum dolorem sint voluptate dolor qui veritatis beatae. Asperiores animi aliquid facere aspernatur.', NULL, NULL, 'https://nike.com', 'United States', NULL, NULL, NULL, 0, 1, 'active', 'public', 'Nike - Official Store', 'Inventore incidunt rerum sed dolores id vitae ex necessitatibus occaecati earum aliquam sit quae.', 'Nike, electronics, shop, buy online', 'https://nike.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(5, 'Adidas', 'adidas', 'Nihil vel vitae aspernatur reiciendis deleniti vitae.', 'Eum velit rerum et. Ab accusantium sapiente repellat tempora magnam aperiam adipisci. Voluptatem cupiditate nulla voluptatem autem. Quas ut voluptatibus culpa earum non.', NULL, NULL, 'https://adidas.com', 'Germany', NULL, NULL, NULL, 0, 1, 'active', 'public', 'Adidas - Official Store', 'Accusamus quas velit accusamus eligendi labore omnis in magnam eos nobis cum molestiae qui molestias quia aspernatur tempora eum et et accusantium pariatur voluptatem rem.', 'Adidas, electronics, shop, buy online', 'https://adidas.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(6, 'LG', 'lg', 'Occaecati debitis voluptates quo quam exercitationem et ducimus molestias ipsam porro qui deserunt ex.', 'Sequi aut sed explicabo aut et. Eum cupiditate explicabo tenetur omnis aut et est consequuntur. Rerum corrupti eveniet sit incidunt esse qui eos. Dignissimos architecto vel quis architecto est.', NULL, NULL, 'https://lg.com', 'South Korea', NULL, NULL, NULL, 0, 0, 'active', 'public', 'LG - Official Store', 'Rerum repellat fuga dolorem magnam possimus recusandae odio aut nihil magnam reprehenderit soluta porro iste in a non cum voluptate aliquam explicabo porro.', 'LG, electronics, shop, buy online', 'https://lg.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(7, 'HP', 'hp', 'Quia ut temporibus impedit et voluptatem est voluptatibus laborum exercitationem.', 'Sint id ut harum et sint. Possimus aut autem esse minima. Iure odio doloribus cum vel qui corporis.', NULL, NULL, 'https://hp.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'HP - Official Store', 'Impedit suscipit sapiente dignissimos aut culpa ex sed est ex labore qui voluptas ut quidem maxime quaerat est excepturi dolores aspernatur aliquid eum.', 'HP, electronics, shop, buy online', 'https://hp.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(8, 'Dell', 'dell', 'Velit est expedita ut optio aperiam aspernatur alias.', 'Quos quis numquam et mollitia et nulla. Dolore doloribus ullam libero ab recusandae aperiam necessitatibus doloremque. Aut illum nemo quia veritatis sapiente alias.', NULL, NULL, 'https://dell.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Dell - Official Store', 'Ab assumenda fugiat tempora et quia et et iusto et aut laudantium esse libero rem sit maxime voluptatem.', 'Dell, electronics, shop, buy online', 'https://dell.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(9, 'Lenovo', 'lenovo', 'Praesentium et ut doloribus consequatur excepturi hic corrupti corrupti similique consequuntur.', 'Est quia sint eum. Rem expedita ut libero suscipit qui consequatur non. Ut omnis corporis dolorem qui accusamus.', NULL, NULL, 'https://lenovo.com', 'China', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Lenovo - Official Store', 'Odit reiciendis at voluptates et molestias quae temporibus numquam odio reprehenderit accusantium dolor amet.', 'Lenovo, electronics, shop, buy online', 'https://lenovo.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(10, 'Asus', 'asus', 'Debitis beatae qui repellat reprehenderit sint eligendi hic at voluptatem voluptatem velit quam.', 'Ullam eaque maxime blanditiis numquam sed recusandae hic saepe. Voluptas nihil ut quidem eligendi occaecati eaque.', NULL, NULL, 'https://asus.com', 'Taiwan', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Asus - Official Store', 'Nisi dolor aut in doloremque rerum laborum aut magni cumque qui labore aut facilis molestiae natus aperiam eaque similique autem.', 'Asus, electronics, shop, buy online', 'https://asus.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(11, 'Microsoft', 'microsoft', 'Est minima inventore est rerum vel quia rerum veniam et nihil suscipit accusamus.', 'Sint corporis similique et dicta. Officiis ullam est officiis perspiciatis alias voluptatem non id.', NULL, NULL, 'https://microsoft.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Microsoft - Official Store', 'In ducimus in minima optio sunt sunt ut placeat et enim iste unde ut omnis numquam reprehenderit vero id.', 'Microsoft, electronics, shop, buy online', 'https://microsoft.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(12, 'Philips', 'philips', 'Aliquam quod sequi et numquam distinctio voluptatum.', 'Labore rerum cupiditate non reiciendis laboriosam aspernatur. Nostrum maiores laudantium quia dolorem. Et quia doloremque ullam quasi asperiores.', NULL, NULL, 'https://philips.com', 'Netherlands', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Philips - Official Store', 'In aut vitae necessitatibus quis iusto rem non iure iusto deleniti aut natus.', 'Philips, electronics, shop, buy online', 'https://philips.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(13, 'Bose', 'bose', 'Et quis suscipit et consequatur voluptate dolorem unde aliquid magnam non fuga molestias rerum placeat.', 'Temporibus ducimus nihil id dolorem. Consectetur illum quisquam autem fugit. Voluptatem distinctio quos tempore id porro eaque. Voluptas cumque soluta et in ad.', NULL, NULL, 'https://bose.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Bose - Official Store', 'Nihil eligendi reprehenderit tenetur quis id nihil voluptatibus ad non perspiciatis cumque dicta repellat dolorem quas qui dolores aliquam eaque reprehenderit voluptates est sit eos quas autem.', 'Bose, electronics, shop, buy online', 'https://bose.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(14, 'JBL', 'jbl', 'Minus laborum ex quos nulla et eveniet.', 'Quis non quia inventore iure enim sed. Ipsum et eaque aut numquam et dolor reprehenderit. Ratione ex et qui consequatur blanditiis voluptates consequatur. At ut et et quod voluptas. Non voluptatem qui aut.', NULL, NULL, 'https://jbl.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'JBL - Official Store', 'Debitis ducimus praesentium doloribus nostrum dolore eum qui autem deleniti numquam repellat voluptatem aut quasi tempore odit necessitatibus illum mollitia.', 'JBL, electronics, shop, buy online', 'https://jbl.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(15, 'Canon', 'canon', 'Totam cum natus omnis officiis unde amet expedita eveniet nihil harum.', 'Veritatis autem velit esse debitis quasi. Incidunt et molestiae sed. Qui optio possimus delectus non illum et.', NULL, NULL, 'https://canon.com', 'Japan', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Canon - Official Store', 'Molestiae vel ad qui sed temporibus perferendis vitae quis voluptas autem enim ducimus.', 'Canon, electronics, shop, buy online', 'https://canon.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(16, 'Nikon', 'nikon', 'Repudiandae enim voluptas est veritatis laudantium unde accusamus magnam pariatur voluptas molestiae.', 'Voluptatem labore unde deleniti. Nam velit iste aliquid molestiae mollitia repellat. Minus dolor minima et hic et amet. Soluta eaque inventore quis officiis eum eaque. Praesentium ipsam aspernatur consequatur sint.', NULL, NULL, 'https://nikon.com', 'Japan', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Nikon - Official Store', 'Natus tempore quos aut alias eos itaque saepe iusto at alias saepe voluptas ipsam.', 'Nikon, electronics, shop, buy online', 'https://nikon.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(17, 'Under Armour', 'under-armour', 'Possimus quas id et rem incidunt id.', 'Distinctio dolores repellendus qui ad veniam asperiores quia. Et ea quo quia facere. Illum rem est alias.', NULL, NULL, 'https://underarmour.com', 'United States', NULL, NULL, NULL, 0, 0, 'active', 'public', 'Under Armour - Official Store', 'Dolores quis quia voluptatem consequatur aut eos quidem eligendi aperiam eos quia soluta consequuntur et aut iste aperiam voluptate asperiores veritatis quaerat est rerum itaque.', 'Under Armour, electronics, shop, buy online', 'https://underarmour.com', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_bn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `visibility` enum('public','hidden','menu_only','homepage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `name_bn`, `slug`, `short_description`, `description`, `icon`, `thumbnail`, `banner`, `sort_order`, `featured`, `status`, `visibility`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Electronics', NULL, 'electronics', 'Sapiente ea omnis illum ipsum veritatis a nostrum adipisci aut vel consequatur.', 'Saepe veritatis rem quo quos molestias. Aut veritatis minima voluptatem voluptatem fuga sed. Qui quod et ducimus natus sint ipsum eum. Adipisci similique expedita maiores.', 'fa-microchip', NULL, NULL, 0, 1, 'active', 'public', 'Electronics - Shop Online', 'Harum dolores accusantium doloremque ut quo cupiditate dicta quasi dolor et at laudantium.', 'Electronics, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, NULL, 'Clothing', NULL, 'clothing', 'Doloremque eius praesentium enim ut aut magnam provident possimus qui.', 'Mollitia voluptate velit incidunt voluptas beatae. Numquam saepe illum nobis velit assumenda neque. Et eius qui molestiae reiciendis earum qui. Ipsa voluptates ab nulla.', 'fa-shirt', NULL, NULL, 0, 1, 'active', 'public', 'Clothing - Shop Online', 'Quia ducimus quo laboriosam quo voluptatem aperiam ullam et nostrum provident nulla et nostrum deleniti repudiandae.', 'Clothing, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, NULL, 'Home & Garden', NULL, 'home-garden', 'Repellat voluptas eveniet aliquid perspiciatis quasi distinctio deleniti ut aut aut.', 'Tempore ipsam aut esse iure temporibus. Quo nostrum laborum velit debitis velit laboriosam soluta. Et voluptatem nulla molestiae quisquam voluptas ut molestiae. Omnis fugiat enim adipisci in sit inventore aut.', 'fa-house', NULL, NULL, 0, 1, 'active', 'public', 'Home & Garden - Shop Online', 'Qui provident aperiam nostrum officiis unde placeat est ullam facilis assumenda consequatur libero magnam libero accusamus recusandae atque qui atque illo non qui culpa est aliquid repellat asperiores est.', 'Home & Garden, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(4, NULL, 'Sports', NULL, 'sports', 'Praesentium voluptatem sint error ducimus animi rerum molestias molestiae dolor aut accusamus dolores.', 'Similique autem aliquid accusantium nisi. Aut et et sapiente sunt ut id deserunt dignissimos. Dolorem ut porro in enim error. Harum voluptate doloribus aut aut ea alias provident.', 'fa-futbol', NULL, NULL, 0, 1, 'active', 'public', 'Sports - Shop Online', 'Repellat vero ex omnis soluta nulla doloribus doloremque inventore architecto sequi doloremque hic officia quod placeat saepe numquam nostrum perferendis facere est enim illum.', 'Sports, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(5, NULL, 'Photography', NULL, 'photography', 'Voluptas earum sit dolor laborum velit ut ipsa labore et eveniet quis quo.', 'Dolorem dolore nam possimus quia ut corrupti eum est. Quas omnis vitae nemo. Quisquam tenetur eaque cupiditate quia cumque.', 'fa-camera', NULL, NULL, 0, 1, 'active', 'public', 'Photography - Shop Online', 'Amet quidem amet suscipit ut quia at ex aut minus labore molestias et et veniam in nemo quo harum saepe dignissimos architecto iure suscipit qui necessitatibus dolores natus.', 'Photography, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(6, 1, 'Computers & Laptops', NULL, 'computers-laptops', 'Omnis recusandae quia maiores quae qui distinctio enim alias nulla odit rerum.', 'Hic natus necessitatibus officiis. Voluptatibus ut ullam est incidunt animi modi. Asperiores itaque et ullam. Error sint est dolor aperiam et est.', 'fa-laptop', NULL, NULL, 0, 0, 'active', 'public', 'Computers & Laptops - Shop Online', 'Provident quis ut ut iure dolorum voluptate libero labore et aliquam velit repellendus commodi blanditiis et eaque delectus quasi sint ut rerum quo odio sint.', 'Computers & Laptops, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(7, 1, 'Smartphones & Tablets', NULL, 'smartphones-tablets', 'Iusto voluptatum ut nihil minus pariatur deserunt aut.', 'Quo quo laborum illum soluta esse. Nam voluptatem ut qui molestias ut doloribus. Voluptatem provident voluptatem molestiae et quo in.', 'fa-mobile-alt', NULL, NULL, 0, 0, 'active', 'public', 'Smartphones & Tablets - Shop Online', 'Neque laudantium repellat est atque eos qui praesentium eius omnis quam ut animi nulla et accusamus omnis ullam quas consequatur molestiae quia aut et.', 'Smartphones & Tablets, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(8, 1, 'Audio & Headphones', NULL, 'audio-headphones', 'Ea cupiditate corporis tempore nostrum dolore repellendus non et ut est a explicabo consequatur.', 'Quidem perspiciatis unde impedit. Nisi aliquam possimus impedit error autem autem enim. Laudantium et ex dolorem omnis.', 'fa-headphones', NULL, NULL, 0, 0, 'active', 'public', 'Audio & Headphones - Shop Online', 'Soluta quia ullam sed enim non maxime explicabo ullam vel et ducimus exercitationem qui iste aut assumenda vitae iusto deserunt.', 'Audio & Headphones, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(9, 2, 'Men\'s Clothing', NULL, 'mens-clothing', 'Voluptatem enim modi consequatur voluptatem quae optio.', 'Provident illo cupiditate est consequatur. Eos quae iure voluptatem deserunt. In ut amet fugiat ducimus.', 'fa-male', NULL, NULL, 0, 0, 'active', 'public', 'Men\'s Clothing - Shop Online', 'Sint dignissimos aliquam earum nihil non dolores accusamus sequi ut nisi ea placeat doloribus recusandae magnam.', 'Men\'s Clothing, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(10, 2, 'Women\'s Clothing', NULL, 'womens-clothing', 'Provident non dolorem reiciendis laborum possimus fuga quidem magni.', 'Et voluptatem vel qui sint quod repellat velit. Qui quo molestiae maiores voluptates impedit.', 'fa-female', NULL, NULL, 0, 0, 'active', 'public', 'Women\'s Clothing - Shop Online', 'Est odit dolore ut illum hic fuga ex eaque dolorem quo at incidunt dolor blanditiis sed iusto est culpa ex harum quidem at quos.', 'Women\'s Clothing, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(11, 2, 'Kids\' Clothing', NULL, 'kids-clothing', 'In non et fugit ipsa qui voluptatum fuga ut perspiciatis totam ex consequuntur.', 'Quia velit dolores sit porro excepturi nostrum commodi. Soluta ipsum nihil rerum repellat omnis quisquam. Iure eius provident officiis excepturi et earum ad nobis. Animi officiis labore dolorum porro. Rerum recusandae est inventore.', 'fa-child', NULL, NULL, 0, 0, 'active', 'public', 'Kids\' Clothing - Shop Online', 'Sint qui ducimus aliquam dolores voluptas praesentium quo odit adipisci occaecati tenetur minus in natus nisi aut.', 'Kids\' Clothing, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(12, 3, 'Furniture', NULL, 'furniture', 'Id qui sit molestias officiis provident aliquam et.', 'Quas aperiam ipsa ipsam autem voluptatem eum. Odio placeat nam hic aliquid quis a. Nemo architecto ratione dolorem neque est suscipit. Corporis nisi distinctio deserunt magnam.', 'fa-couch', NULL, NULL, 0, 0, 'active', 'public', 'Furniture - Shop Online', 'Ullam alias soluta vel sit qui iure saepe numquam at illo aut quaerat.', 'Furniture, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(13, 3, 'Kitchen Appliances', NULL, 'kitchen-appliances', 'Tempora illo atque fugit sed aut quidem officiis rerum.', 'Voluptatum dolore accusantium laboriosam dolor. Hic aut ab nesciunt ut. Rerum similique voluptatem id aut porro occaecati quibusdam.', 'fa-blender', NULL, NULL, 0, 0, 'active', 'public', 'Kitchen Appliances - Shop Online', 'Molestiae et doloribus et mollitia minus error tenetur praesentium est dolor sed impedit quibusdam aliquid.', 'Kitchen Appliances, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(14, 4, 'Football', NULL, 'football', 'Fuga ut et et aliquam quidem facere ipsa qui debitis dolor debitis blanditiis.', 'Adipisci accusamus labore accusamus aut accusamus asperiores ipsum. Laudantium ab eveniet excepturi ad dolorum. Natus impedit doloremque voluptatem illum ea. Velit ratione doloremque quae est harum consequatur.', 'fa-football-ball', NULL, NULL, 0, 0, 'active', 'public', 'Football - Shop Online', 'Sunt laboriosam totam molestiae animi dolorum provident in quibusdam qui commodi aut voluptatem sit modi eius quis exercitationem et ipsam laborum.', 'Football, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(15, 4, 'Basketball', NULL, 'basketball', 'Temporibus quae doloribus magnam sit repellendus debitis id eos voluptate magni explicabo.', 'Et accusantium suscipit doloribus distinctio quia id. Sit soluta quam ea. Excepturi alias earum dolores nisi.', 'fa-basketball-ball', NULL, NULL, 0, 0, 'active', 'public', 'Basketball - Shop Online', 'Animi architecto sint impedit mollitia est voluptas officiis quidem sunt alias voluptates ut eos omnis quidem ducimus ut et nemo.', 'Basketball, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(16, 5, 'Cameras', NULL, 'cameras', 'Sint et consequuntur ad consectetur ad rerum iusto non dolorem ut commodi.', 'Voluptate officia est at quia quia ad. Iste placeat officiis doloribus iste ducimus rem. Esse esse voluptate voluptas adipisci occaecati quia. Adipisci non repellat laborum earum temporibus iure.', 'fa-camera', NULL, NULL, 0, 0, 'active', 'public', 'Cameras - Shop Online', 'Est est praesentium repellendus fugiat culpa vitae dolore aut illo quod expedita at est vel sapiente neque pariatur expedita veniam.', 'Cameras, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(17, 5, 'Lenses', NULL, 'lenses', 'Nulla non consequatur occaecati velit sit magni quasi enim est.', 'Quia debitis exercitationem rerum dolores. Explicabo consectetur laboriosam asperiores aliquam. Non vitae illo repudiandae eos ab quo minima. Et labore esse ratione hic.', 'fa-camera-retro', NULL, NULL, 0, 0, 'active', 'public', 'Lenses - Shop Online', 'Quas est illum et enim maxime dolorem molestias quod tempora libero natus aut quis reprehenderit et.', 'Lenses, shop, buy, online, store', NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

CREATE TABLE `category_product` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_discount` decimal(12,2) DEFAULT NULL,
  `min_order_amount` decimal(12,2) DEFAULT NULL,
  `max_order_amount` decimal(12,2) DEFAULT NULL,
  `usage_limit` int UNSIGNED DEFAULT NULL,
  `per_user_limit` int UNSIGNED NOT NULL DEFAULT '1',
  `total_used` int UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `priority` int NOT NULL DEFAULT '0',
  `scope` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_auto_apply` tinyint(1) NOT NULL DEFAULT '0',
  `is_first_order_only` tinyint(1) NOT NULL DEFAULT '0',
  `is_guest_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `customer_restriction` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_multiple` tinyint(1) NOT NULL DEFAULT '0',
  `settings` json DEFAULT NULL,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_categories`
--

CREATE TABLE `coupon_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `is_excluded` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_customers`
--

CREATE TABLE `coupon_customers` (
  `id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_products`
--

CREATE TABLE `coupon_products` (
  `id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `is_excluded` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usages`
--

CREATE TABLE `coupon_usages` (
  `id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `order_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `marketing_opt_in` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','banned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `minimum_order_amount` decimal(10,2) DEFAULT NULL COMMENT 'Free delivery threshold',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_zones`
--

INSERT INTO `delivery_zones` (`id`, `name`, `type`, `charge`, `minimum_order_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Inside Dhaka', 'inside_dhaka', '10.00', '100.00', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 'Outside Dhaka', 'outside_dhaka', '25.00', '200.00', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zone_districts`
--

CREATE TABLE `delivery_zone_districts` (
  `id` bigint UNSIGNED NOT NULL,
  `delivery_zone_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_zone_districts`
--

INSERT INTO `delivery_zone_districts` (`id`, `delivery_zone_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dhaka', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 1, 'Gazipur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, 1, 'Narayanganj', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(4, 1, 'Munshiganj', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(5, 1, 'Manikganj', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(6, 1, 'Narsingdi', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(7, 1, 'Madaripur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(8, 1, 'Shariatpur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(9, 1, 'Tangail', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(10, 1, 'Kishoreganj', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(11, 1, 'Faridpur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(12, 1, 'Gopalganj', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(13, 1, 'Rajbari', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(14, 2, 'Jamalpur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(15, 2, 'Mymensingh', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(16, 2, 'Netrokona', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(17, 2, 'Sherpur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(18, 2, 'Bandarban', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(19, 2, 'Brahmanbaria', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(20, 2, 'Chandpur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(21, 2, 'Chittagong', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(22, 2, 'Comilla', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(23, 2, 'Cox\'s Bazar', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(24, 2, 'Feni', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(25, 2, 'Khagrachhari', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(26, 2, 'Lakshmipur', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(27, 2, 'Noakhali', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(28, 2, 'Rangamati', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(29, 2, 'Bagerhat', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(30, 2, 'Chuadanga', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(31, 2, 'Jessore', 'active', '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(32, 2, 'Jhenaidah', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(33, 2, 'Khulna', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(34, 2, 'Kushtia', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(35, 2, 'Magura', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(36, 2, 'Meherpur', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(37, 2, 'Narail', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(38, 2, 'Satkhira', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(39, 2, 'Bogra', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(40, 2, 'Joypurhat', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(41, 2, 'Naogaon', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(42, 2, 'Natore', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(43, 2, 'Nawabganj', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(44, 2, 'Pabna', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(45, 2, 'Rajshahi', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(46, 2, 'Sirajganj', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(47, 2, 'Dinajpur', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(48, 2, 'Gaibandha', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(49, 2, 'Kurigram', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(50, 2, 'Lalmonirhat', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(51, 2, 'Nilphamari', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(52, 2, 'Panchagarh', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(53, 2, 'Rangpur', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(54, 2, 'Thakurgaon', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(55, 2, 'Habiganj', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(56, 2, 'Maulvibazar', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(57, 2, 'Sunamganj', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(58, 2, 'Sylhet', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(59, 2, 'Barguna', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(60, 2, 'Barisal', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(61, 2, 'Bhola', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(62, 2, 'Jhalokati', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(63, 2, 'Patuakhali', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25'),
(64, 2, 'Pirojpur', 'active', '2026-08-19 19:00:25', '2026-08-19 19:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Shop Now',
  `cta_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/products',
  `bg_gradient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'from-blue-600 via-blue-700 to-indigo-900',
  `image_emoji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '?',
  `badge_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Limited Time Offer',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `cta_text`, `cta_link`, `bg_gradient`, `image_emoji`, `badge_text`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'New Season Collection', 'Discover the latest trends with amazing prices', 'Shop Now', '/products', 'from-primary-700 via-primary-800 to-primary-900', '🛍️', 'New Arrivals', 1, 1, '2026-08-19 19:05:12', '2026-08-19 19:05:12'),
(2, 'Mega Sale — Up to 40% Off', 'Limited time offer on selected electronics & accessories', 'View Deals', '/products', 'from-accent-600 via-accent-700 to-red-800', '🔥', 'Hot Deal', 2, 1, '2026-08-19 19:05:12', '2026-08-19 19:05:12'),
(3, 'Premium Quality, Best Prices', 'Shop from top brands with guaranteed authenticity', 'Explore', '/categories', 'from-emerald-600 via-teal-700 to-cyan-800', '✨', 'Top Brands', 3, 1, '2026-08-19 19:05:12', '2026-08-19 19:05:12');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stock` int NOT NULL DEFAULT '0',
  `reserved_stock` int NOT NULL DEFAULT '0',
  `available_stock` int GENERATED ALWAYS AS ((`current_stock` - `reserved_stock`)) STORED,
  `minimum_stock` int NOT NULL DEFAULT '0',
  `maximum_stock` int DEFAULT NULL,
  `reorder_level` int NOT NULL DEFAULT '0',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `warehouse_id`, `product_variant_id`, `sku`, `barcode`, `current_stock`, `reserved_stock`, `minimum_stock`, `maximum_stock`, `reorder_level`, `location`, `created_at`, `updated_at`) VALUES
(1, 17, 1, NULL, NULL, NULL, 1, 0, 0, NULL, 0, NULL, '2026-08-19 19:35:28', '2026-08-19 19:35:28'),
(2, 88, 1, 61, NULL, NULL, 5, 0, 0, NULL, 0, NULL, '2026-08-19 20:01:23', '2026-08-19 20:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_adjustments`
--

CREATE TABLE `inventory_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_before` int NOT NULL,
  `quantity_change` int NOT NULL,
  `quantity_after` int NOT NULL,
  `reason` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_before` int NOT NULL DEFAULT '0',
  `quantity_change` int NOT NULL,
  `quantity_after` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `batch_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`id`, `inventory_id`, `product_id`, `warehouse_id`, `product_variant_id`, `user_id`, `type`, `reference_type`, `reference_id`, `reference_number`, `quantity_before`, `quantity_change`, `quantity_after`, `unit_cost`, `status`, `reason`, `batch_number`, `expiry_date`, `created_by_type`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 1, NULL, 1, 'purchase', 'manual', NULL, NULL, 0, 1, 1, '500.00', 'completed', 'Stock in', NULL, NULL, NULL, '2026-08-19 19:35:28', '2026-08-19 19:35:28'),
(2, 2, 88, 1, 61, 1, 'adjustment', 'manual', NULL, NULL, 0, 5, 5, NULL, 'completed', 'Manual stock in', NULL, NULL, NULL, '2026-08-19 20:01:23', '2026-08-19 20:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'image, document, video, etc.',
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `alt_text` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_21_113334_create_personal_access_tokens_table', 1),
(5, '2026_07_21_113347_create_permission_tables', 1),
(6, '2026_07_24_100000_add_user_profile_fields_to_users_table', 1),
(7, '2026_07_24_120000_create_brands_table', 1),
(8, '2026_07_24_130000_create_categories_table', 1),
(9, '2026_07_24_140000_create_products_table', 1),
(10, '2026_07_24_150000_create_product_variants_table', 1),
(11, '2026_07_24_160000_create_product_attributes_table', 1),
(12, '2026_07_24_160001_create_product_attribute_values_table', 1),
(13, '2026_07_24_160002_create_product_gallery_table', 1),
(14, '2026_07_26_100000_create_customer_profiles_table', 1),
(15, '2026_07_26_100003_create_wallets_table', 1),
(16, '2026_07_26_100004_create_wallet_transactions_table', 1),
(17, '2026_07_26_100005_create_user_addresses_table', 1),
(18, '2026_07_27_142728_create_orders_table', 1),
(19, '2026_07_27_142736_create_order_items_table', 1),
(20, '2026_07_27_142737_create_order_status_histories_table', 1),
(21, '2026_07_27_145704_create_coupons_table', 1),
(22, '2026_07_27_145705_create_coupon_usages_table', 1),
(23, '2026_07_27_145706_create_coupon_products_table', 1),
(24, '2026_07_27_145707_create_coupon_categories_table', 1),
(25, '2026_07_27_145708_create_coupon_customers_table', 1),
(26, '2026_07_27_150441_create_warehouses_table', 1),
(27, '2026_07_27_150442_create_inventory_table', 1),
(28, '2026_07_27_150443_create_inventory_transactions_table', 1),
(29, '2026_07_27_150444_create_stock_reservations_table', 1),
(30, '2026_07_27_150445_create_stock_transfers_table', 1),
(31, '2026_07_27_150446_create_inventory_adjustments_table', 1),
(32, '2026_07_27_150447_create_stock_audits_table', 1),
(33, '2026_07_27_161518_create_customers_table', 1),
(34, '2026_07_27_161550_add_type_to_users_and_customer_id_to_profiles', 1),
(35, '2026_07_28_000001_create_delivery_zones_table', 1),
(36, '2026_07_28_000002_create_delivery_zone_districts_table', 1),
(37, '2026_07_28_000003_alter_delivery_zones_table', 1),
(38, '2026_07_28_143322_create_hero_slides_table', 1),
(39, '2026_07_28_143323_create_site_settings_table', 1),
(40, '2026_07_28_144012_add_image_settings_to_site_settings', 1),
(41, '2026_07_28_145626_add_static_page_settings_to_site_settings', 1),
(42, '2026_07_28_145854_create_pages_table', 1),
(43, '2026_07_28_151428_move_static_pages_from_settings_to_pages', 1),
(44, '2026_07_28_160000_create_media_table', 1),
(45, '2026_07_28_165427_create_category_product_table', 1),
(46, '2026_07_28_184727_create_wishlists_table', 1),
(47, '2026_07_28_190601_add_shipping_tax_settings_to_site_settings', 1),
(48, '2026_07_29_022649_add_currency_settings_to_site_settings_table', 1),
(49, '2026_07_29_100000_fix_existing_products_product_type', 1),
(50, '2026_07_29_100001_add_guest_checkout_to_orders_table', 1),
(51, '2026_07_29_100002_add_customer_id_to_orders_table', 1),
(52, '2026_08_15_000002_add_active_theme_setting_to_site_settings', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping_charge` decimal(15,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BDT',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `billing_address` text COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `wholesale_price` decimal(15,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `variant_attributes` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_status_histories`
--

CREATE TABLE `order_status_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `from_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about-us', '<h2>Welcome to MyVoucher</h2>\n<p>MyVoucher is Bangladesh\'s premier online shopping destination, offering a curated selection of quality products at competitive prices. Founded with the mission to make online shopping accessible, reliable, and enjoyable for everyone.</p>\n\n<h2>Our Mission</h2>\n<p>We believe that everyone deserves access to quality products without breaking the bank. Our team works tirelessly to source the best products from trusted brands and deliver them right to your doorstep.</p>\n\n<h2>Why Choose Us?</h2>\n<ul>\n<li><strong>Quality Guaranteed</strong> — Every product is carefully vetted before listing.</li>\n<li><strong>Best Prices</strong> — We offer competitive pricing with regular promotions.</li>\n<li><strong>Fast Delivery</strong> — Reliable delivery across all 8 divisions of Bangladesh.</li>\n<li><strong>Secure Payments</strong> — Multiple payment options with SSL encryption.</li>\n<li><strong>Customer Support</strong> — Our dedicated team is available 7 days a week.</li>\n</ul>\n\n<h2>Our Values</h2>\n<p><strong>Integrity:</strong> We are honest and transparent in all our dealings.</p>\n<p><strong>Quality:</strong> We never compromise on product quality.</p>\n<p><strong>Customer First:</strong> Your satisfaction is our top priority.</p>\n<p><strong>Innovation:</strong> We constantly improve to serve you better.</p>\n\n<h2>Contact Us</h2>\n<p>Have questions? We\'d love to hear from you.</p>\n<p><strong>Phone:</strong> +880 1700-123456</p>\n<p><strong>Email:</strong> support@myvoucher.com</p>\n<p><strong>Address:</strong> Gulshan-2, Dhaka 1212, Bangladesh</p>', 'About Us - MyVoucher', 'Learn more about MyVoucher.', 'published', 1, '2026-08-19 18:58:40', '2026-08-19 19:32:08'),
(2, 'FAQ', 'faq', '<h2>General Questions</h2>\n\n<h3>What is MyVoucher?</h3>\n<p>MyVoucher is an online shopping platform based in Bangladesh. We offer a wide range of products including electronics, fashion, home goods, and more — all at competitive prices with reliable delivery.</p>\n\n<h3>Do I need an account to shop?</h3>\n<p>No, you can browse products and place orders as a guest. However, creating an account gives you access to order tracking, wishlist, wallet, and exclusive member offers.</p>\n\n<h3>How do I create an account?</h3>\n<p>Click the \"Join Now\" button at the top of the page and fill in your details. You can also register during checkout.</p>\n\n<h2>Orders &amp; Payment</h2>\n\n<h3>What payment methods do you accept?</h3>\n<p>We accept Cash on Delivery (COD), bKash, Nagad, Bank Transfer, and Credit/Debit Cards.</p>\n\n<h3>Can I modify or cancel my order?</h3>\n<p>You can cancel your order before it is shipped. Once shipped, cancellation is not possible, but you can refuse delivery. To modify or cancel, contact our support team immediately.</p>\n\n<h3>How do I apply a coupon code?</h3>\n<p>During checkout, you will see a \"Coupon Code\" field. Enter your code and click \"Apply\" to receive the discount.</p>\n\n<h2>Delivery</h2>\n\n<h3>How long does delivery take?</h3>\n<ul>\n<li><strong>Dhaka:</strong> 1-2 business days</li>\n<li><strong>Outside Dhaka:</strong> 2-5 business days</li>\n</ul>\n\n<h3>Is there free shipping?</h3>\n<p>Yes! Orders above ৳5,000 qualify for free standard delivery within Bangladesh.</p>\n\n<h3>How can I track my order?</h3>\n<p>Go to <strong>Track Order</strong> and enter your email address and order number to see real-time status updates.</p>\n\n<h2>Returns &amp; Refunds</h2>\n\n<h3>What is your return policy?</h3>\n<p>We offer a 7-day return policy for most items. The product must be unused and in its original packaging.</p>\n\n<h3>How long does a refund take?</h3>\n<p>Refunds are processed within 5-7 business days after we receive and inspect the returned item.</p>', 'FAQ - MyVoucher', 'Frequently asked questions.', 'published', 2, '2026-08-19 18:58:40', '2026-08-19 19:32:08'),
(3, 'Returns Policy', 'returns', '<h2>Returns & Exchanges Policy</h2>\n<h3>30-Day Return Policy</h3>\n<p>We offer a 30-day return policy from the date of delivery. If you are not satisfied with your purchase, you can return it for a full refund or exchange.</p>\n<h3>Conditions</h3>\n<ul>\n<li>Product must be unused and in original packaging</li>\n<li>All accessories and tags must be included</li>\n<li>Proof of purchase required</li>\n</ul>\n<h3>How to Return</h3>\n<ol>\n<li>Log in to your account and go to Orders</li>\n<li>Select the order and click \"Return\"</li>\n<li>Choose the reason for return</li>\n<li>Print the return label</li>\n<li>Pack the item securely and drop it off at your nearest delivery point</li>\n</ol>\n<h3>Refund Processing</h3>\n<p>Refunds are processed within 5-7 business days after we receive the returned item. The amount will be credited to your original payment method.</p>', 'Returns Policy - E-Commerce', NULL, 'published', 0, '2026-08-19 18:58:40', '2026-08-19 18:58:40'),
(4, 'Shipping Info', 'shipping-info', '<h2>Shipping Information</h2>\n<h3>Delivery Options</h3>\n<p>We offer the following delivery options:</p>\n<ul>\n<li><strong>Standard Delivery:</strong> 3-5 business days — $5.00</li>\n<li><strong>Express Delivery:</strong> 24-48 hours — $12.00</li>\n<li><strong>Free Shipping:</strong> On all orders over $50.00</li>\n</ul>\n<h3>Delivery Areas</h3>\n<p>We deliver to all districts in Bangladesh. Our delivery partners cover both urban and rural areas.</p>\n<h3>Tracking</h3>\n<p>Once your order is shipped, you will receive a tracking number via email and SMS. You can track your order from your account dashboard.</p>\n<h3>Delivery Hours</h3>\n<p>Deliveries are made from 9:00 AM to 8:00 PM, Sunday to Thursday. Friday and Saturday deliveries are available in select areas.</p>', 'Shipping Info - E-Commerce', NULL, 'published', 0, '2026-08-19 18:58:40', '2026-08-19 18:58:40'),
(5, 'Shipping Policy', 'shipping-policy', '<h2>Shipping Overview</h2>\n<p>We deliver to all 8 divisions and 64 districts across Bangladesh. Shipping costs and delivery times vary based on your location and order size.</p>\n\n<h2>Delivery Areas &amp; Timeframes</h2>\n<ul>\n<li><strong>Dhaka City:</strong> 1-2 business days</li>\n<li><strong>Gazipur, Narayanganj, Savar (Greater Dhaka):</strong> 1-3 business days</li>\n<li><strong>Chattogram Division:</strong> 2-4 business days</li>\n<li><strong>Rajshahi, Khulna, Sylhet Divisions:</strong> 3-5 business days</li>\n<li><strong>Barishal, Rangpur, Mymensingh Divisions:</strong> 3-5 business days</li>\n</ul>\n\n<h2>Shipping Charges</h2>\n<ul>\n<li><strong>Inside Dhaka:</strong> ৳60 per kg</li>\n<li><strong>Outside Dhaka:</strong> ৳100 per kg</li>\n<li><strong>Free Shipping:</strong> Orders above ৳5,000</li>\n</ul>\n\n<h2>Order Tracking</h2>\n<p>Once your order is shipped, you will receive an SMS with your tracking information. You can also track your order from the <strong>Track Order</strong> page.</p>\n\n<h2>Cash on Delivery (COD)</h2>\n<p>Cash on Delivery is available for all locations. Please keep the exact amount ready at the time of delivery.</p>\n\n<h2>Delivery Partners</h2>\n<p>We work with trusted courier partners including Pathao, Paperfly, and SA Paribahan to ensure your orders arrive safely and on time.</p>\n\n<h2>Failed Delivery</h2>\n<p>If delivery fails due to incorrect address or unavailability, our team will contact you to reschedule. After 3 failed attempts, the order may be cancelled and a refund initiated.</p>', 'Shipping Policy - MyVoucher', 'Shipping methods and delivery times.', 'published', 3, '2026-08-19 19:32:08', '2026-08-19 19:32:08'),
(6, 'Return Policy', 'return-policy', '<h2>Return Policy</h2>\n<p>We want you to be completely satisfied with your purchase. If you\'re not happy, we make returns easy.</p>\n\n<h2>Eligibility</h2>\n<p>You can return items within <strong>7 days</strong> of delivery if:</p>\n<ul>\n<li>The item is unused and in original condition</li>\n<li>The original packaging is intact</li>\n<li>You have the receipt or order confirmation</li>\n</ul>\n\n<h2>Non-Returnable Items</h2>\n<ul>\n<li>Perishable goods (food, flowers)</li>\n<li>Personal care and hygiene products</li>\n<li>Undergarments and swimwear</li>\n<li>Customized or personalized items</li>\n<li>Digital products and gift cards</li>\n</ul>\n\n<h2>How to Return</h2>\n<ol>\n<li>Contact our support team at <strong>support@myvoucher.com</strong> or call <strong>+880 1700-123456</strong></li>\n<li>Provide your order number and reason for return</li>\n<li>Our rider will pick up the item (Dhaka) or you can send it via courier (outside Dhaka)</li>\n<li>Once we receive and inspect the item, your refund will be processed</li>\n</ol>\n\n<h2>Refunds</h2>\n<ul>\n<li><strong>Wallet Credit:</strong> Within 24 hours of return approval</li>\n<li><strong>bKash / Nagad:</strong> Within 3-5 business days</li>\n<li><strong>Bank Transfer:</strong> Within 5-7 business days</li>\n<li><strong>Cash (COD orders):</strong> Refunded via bKash or bank transfer</li>\n</ul>\n\n<h2>Exchanges</h2>\n<p>We offer exchanges for different sizes or colors of the same product, subject to availability. Contact us to arrange an exchange.</p>\n\n<h2>Damaged or Wrong Items</h2>\n<p>If you receive a damaged or wrong item, please contact us within <strong>48 hours</strong> of delivery with photos. We will arrange an immediate replacement or full refund at no extra cost.</p>', 'Return Policy - MyVoucher', 'Hassle-free return policy.', 'published', 4, '2026-08-19 19:32:08', '2026-08-19 19:32:08'),
(7, 'Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2>\n<p><em>Last updated: August 2026</em></p>\n<p>At MyVoucher, we take your privacy seriously. This policy explains how we collect, use, and protect your personal information.</p>\n\n<h2>Information We Collect</h2>\n<h3>Personal Information</h3>\n<ul>\n<li>Name, email address, phone number</li>\n<li>Shipping and billing addresses</li>\n<li>Payment information (processed securely, never stored in full)</li>\n</ul>\n\n<h3>Automated Information</h3>\n<ul>\n<li>Device type, browser, and IP address</li>\n<li>Pages visited and time spent on our site</li>\n<li>Referring website or search engine</li>\n</ul>\n\n<h2>How We Use Your Information</h2>\n<ul>\n<li>Process and fulfill your orders</li>\n<li>Send order updates and delivery notifications</li>\n<li>Improve our products and services</li>\n<li>Send promotional offers (with your consent)</li>\n<li>Prevent fraud and ensure platform security</li>\n</ul>\n\n<h2>Information Sharing</h2>\n<p>We do not sell your personal information. We share data only with:</p>\n<ul>\n<li><strong>Courier partners</strong> — to deliver your orders</li>\n<li><strong>Payment processors</strong> — to handle transactions securely</li>\n<li><strong>Legal authorities</strong> — when required by law</li>\n</ul>\n\n<h2>Data Security</h2>\n<p>We use industry-standard SSL encryption, firewalls, and access controls to protect your data. All payment transactions are processed through PCI-DSS compliant systems.</p>\n\n<h2>Your Rights</h2>\n<ul>\n<li>Access your personal data</li>\n<li>Correct inaccurate information</li>\n<li>Request deletion of your account</li>\n<li>Opt out of marketing communications</li>\n</ul>\n\n<h2>Contact</h2>\n<p>For privacy-related questions, contact us at <strong>privacy@myvoucher.com</strong>.</p>', 'Privacy Policy - MyVoucher', 'How we protect your data.', 'published', 5, '2026-08-19 19:32:08', '2026-08-19 19:32:08'),
(8, 'Terms & Conditions', 'terms', '<h2>Terms &amp; Conditions</h2>\n<p><em>Last updated: August 2026</em></p>\n<p>By using MyVoucher, you agree to these terms and conditions. Please read them carefully.</p>\n\n<h2>General Terms</h2>\n<ul>\n<li>You must be at least 18 years old to create an account</li>\n<li>You are responsible for maintaining the confidentiality of your account</li>\n<li>One account per person; duplicate accounts may be suspended</li>\n<li>Providing false information may result in account termination</li>\n</ul>\n\n<h2>Products &amp; Pricing</h2>\n<ul>\n<li>All prices are in Bangladeshi Taka (BDT) and include applicable taxes unless stated otherwise</li>\n<li>Product images are for illustration; actual products may vary slightly</li>\n<li>We reserve the right to change prices without prior notice</li>\n<li>In case of pricing errors, we will contact you before processing the order</li>\n</ul>\n\n<h2>Orders</h2>\n<ul>\n<li>An order is confirmed only after successful payment or COD acceptance</li>\n<li>We reserve the right to cancel orders due to stock unavailability, payment issues, or suspected fraud</li>\n<li>Order quantities may be limited per customer for promotional items</li>\n</ul>\n\n<h2>Payments</h2>\n<ul>\n<li>Cash on Delivery is available for all locations in Bangladesh</li>\n<li>Online payments are processed through secure, encrypted channels</li>\n<li>Failed payments may result in order cancellation</li>\n</ul>\n\n<h2>Intellectual Property</h2>\n<p>All content on MyVoucher including logos, images, text, and design is our intellectual property and may not be reproduced without permission.</p>\n\n<h2>Limitation of Liability</h2>\n<p>MyVoucher is not liable for indirect damages, delays caused by third-party couriers, or issues arising from incorrect user-provided information.</p>\n\n<h2>Changes to Terms</h2>\n<p>We may update these terms periodically. Continued use of the platform after changes constitutes acceptance of the updated terms.</p>\n\n<h2>Governing Law</h2>\n<p>These terms are governed by the laws of Bangladesh. Any disputes shall be resolved in the courts of Dhaka.</p>', 'Terms & Conditions - MyVoucher', 'Terms of using MyVoucher.', 'published', 6, '2026-08-19 19:32:08', '2026-08-19 19:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_bn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regular_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock` int UNSIGNED NOT NULL DEFAULT '0',
  `minimum_stock` int UNSIGNED NOT NULL DEFAULT '0',
  `maximum_order` int UNSIGNED DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `tax_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','pending','published','hidden','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `product_type` enum('simple','variable','digital','service','bundle') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'simple',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('public','private','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `brand_id`, `category_id`, `name`, `name_bn`, `slug`, `short_description`, `description`, `sku`, `barcode`, `thumbnail`, `regular_price`, `sale_price`, `wholesale_price`, `cost_price`, `stock`, `minimum_stock`, `maximum_order`, `weight`, `length`, `width`, `height`, `tax_class`, `shipping_class`, `status`, `product_type`, `featured`, `visibility`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `published_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'Samsung Galaxy S24 Ultra', NULL, 'samsung-galaxy-s24-ultra-0', 'Nam suscipit aut sequi nam eum accusantium rerum delectus nulla doloribus quo ab autem expedita maiores omnis.', 'Harum ea aspernatur nisi quasi necessitatibus nihil saepe sit. Dolores quos ut at aut expedita. Tempore dolores voluptates velit maxime consequatur occaecati incidunt dicta. Quidem perferendis rerum aliquid qui. Eos et quod eos et et quia harum atque. Maiores cupiditate nostrum delectus minus velit voluptatem. Dolor autem et dolorem dignissimos fugit iure.', 'SKU-0001', 'snXmWY0o5H', NULL, '1199.99', '1019.99', '839.99', '600.00', 50, 10, 10, '0.95', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Samsung Galaxy S24 Ultra - Buy Online', 'Sit nesciunt consequatur maiores est voluptas tempora porro qui quia nostrum aut assumenda qui dolores esse sunt temporibus veniam ducimus culpa ea ratione velit nisi rem.', 'Samsung Galaxy S24 Ultra, buy, shop, online, Samsung', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 1, 7, 'Samsung Galaxy S24', NULL, 'samsung-galaxy-s24-1', 'Nihil debitis incidunt et aperiam sed quisquam tempora mollitia reiciendis provident eaque provident eos aut quis.', 'Veniam commodi esse cum eum consequuntur nobis. Tenetur quia consequatur facere corporis non provident. Facilis sequi nobis sit iure ea consequuntur quaerat. Repellendus molestiae est reprehenderit. Corrupti et et asperiores debitis iste. Nobis possimus optio quia repellendus cum dolor. Dolor numquam cumque doloribus ipsam molestiae ipsa culpa.', 'SKU-0002', 'OWoJBHLm6t', NULL, '899.99', '764.99', '629.99', '450.00', 80, 10, 10, '5.46', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Samsung Galaxy S24 - Buy Online', 'Quam quam unde ex est culpa qui nulla quia iure provident dolorum sed sit voluptatem explicabo repudiandae voluptatem amet est maiores dolorem quia voluptatem.', 'Samsung Galaxy S24, buy, shop, online, Samsung', NULL, '2026-08-11 09:30:54', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, 1, 7, 'Samsung Galaxy A54', NULL, 'samsung-galaxy-a54-2', 'Praesentium voluptas dignissimos qui cumque et omnis tempore neque rerum quia non aut quia quae porro iure qui quis.', 'Nostrum itaque in veniam et placeat sed qui. Delectus et accusantium aut. Qui qui ex praesentium id. Commodi dicta quia doloribus sint ut cumque. Sit suscipit debitis unde vitae non. Ab in quisquam dolorem aspernatur tenetur quibusdam asperiores.', 'SKU-0003', '0oirmXr1qM', NULL, '449.99', '382.49', '314.99', '225.00', 120, 10, 10, '3.59', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Samsung Galaxy A54 - Buy Online', 'Cumque harum minima omnis fugiat praesentium doloremque et quo praesentium est eveniet et rem accusantium.', 'Samsung Galaxy A54, buy, shop, online, Samsung', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(4, 2, 7, 'Apple iPhone 15 Pro Max', NULL, 'apple-iphone-15-pro-max-3', 'Consequatur aut id ea rem fugit maiores placeat saepe quis hic in rerum non doloremque eum.', 'Velit consequuntur eius veniam vel nam. Mollitia aliquam sapiente tempore ea ipsa ut velit. Qui vel fugiat possimus et aut totam cum deserunt. Ab cumque voluptatem accusantium vitae. Culpa magni dolores natus voluptas sint non sit. Est aut ab debitis sed rerum. Autem numquam nulla sit rerum voluptatem saepe aut.', 'SKU-0004', 'JfWqJ4gwtZ', NULL, '1399.99', '1189.99', '979.99', '700.00', 40, 10, 10, '1.82', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple iPhone 15 Pro Max - Buy Online', 'Esse fugit exercitationem consequatur id asperiores ad dolorum nostrum similique et est non temporibus perferendis quis qui voluptas sint autem enim incidunt fuga numquam corrupti perferendis.', 'Apple iPhone 15 Pro Max, buy, shop, online, Apple', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(5, 2, 7, 'Apple iPhone 15', NULL, 'apple-iphone-15-4', 'Molestiae labore iusto adipisci soluta qui quidem libero odit laborum totam qui vel impedit illo odio beatae modi necessitatibus atque.', 'Aspernatur non molestiae eveniet aut rem at. Reprehenderit consequatur non architecto autem. Laudantium fugiat asperiores ipsam architecto aliquam qui corrupti. Aut magni molestiae unde in. Dolores et eaque neque omnis veritatis et. Maiores ex ea vel et porro itaque sunt. Cum quas culpa sunt quidem.', 'SKU-0005', 'xNF7UG0N5O', NULL, '999.99', '849.99', '699.99', '500.00', 60, 10, 10, '5.84', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple iPhone 15 - Buy Online', 'Aut exercitationem aliquid nihil quibusdam non quidem eaque sequi dolorem ea aut magnam id corrupti et hic omnis.', 'Apple iPhone 15, buy, shop, online, Apple', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(6, 2, 7, 'Apple iPhone 14', NULL, 'apple-iphone-14-5', 'Delectus consequatur tenetur eum quis veniam a ea ipsa qui quae quis corporis sunt dolorem officiis illo.', 'Veniam explicabo est vitae corporis explicabo est. Aut officia rerum aut et. Debitis esse deleniti voluptas at id consequatur ea est. Ex iste sunt quisquam voluptatem quo nihil laborum. Eos deleniti porro est odio provident autem. Earum aut dicta explicabo nihil et similique quia.', 'SKU-0006', 's2wn1MaKBS', NULL, '799.99', '679.99', '559.99', '400.00', 100, 10, 10, '4.25', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple iPhone 14 - Buy Online', 'Eligendi dignissimos consectetur maiores adipisci magnam enim cum necessitatibus libero doloremque quod sequi velit ullam minus accusantium eveniet rerum.', 'Apple iPhone 14, buy, shop, online, Apple', NULL, '2026-07-25 09:20:16', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(7, 1, 7, 'Google Pixel 8 Pro', NULL, 'google-pixel-8-pro-6', 'Numquam exercitationem incidunt quas molestiae rem exercitationem nulla consequatur asperiores corrupti sunt qui.', 'Labore commodi similique ut cum iure et. Mollitia nihil quos atque aut. Tenetur consequatur expedita sapiente iusto ducimus rem. Est quas ullam est quibusdam incidunt. Excepturi voluptate voluptas deserunt voluptatem. Voluptas consequatur aperiam iusto illo. Qui eaque dolor facere recusandae asperiores nobis.', 'SKU-0007', 'yj4gCMWLmX', NULL, '999.99', '849.99', '699.99', '500.00', 35, 10, 10, '1.30', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Google Pixel 8 Pro - Buy Online', 'Porro officiis qui expedita culpa dolores placeat unde architecto reprehenderit ab sit quae.', 'Google Pixel 8 Pro, buy, shop, online, Samsung', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(8, 1, 7, 'OnePlus 12', NULL, 'oneplus-12-7', 'Laborum iusto deleniti eaque molestias eos similique ratione itaque et illum voluptatem non voluptatibus dignissimos perspiciatis quisquam fuga autem et.', 'Sit recusandae reiciendis dolor magni omnis. Debitis modi adipisci excepturi est minima. Consequatur dolor unde dolor architecto. Incidunt rerum quam quidem recusandae omnis et vel. Suscipit eum perferendis dolores minus tempora. Quam necessitatibus est repellat quas. Ut et in dolorem blanditiis dolor et animi reiciendis.', 'SKU-0008', 'jSvwtiWUts', NULL, '799.99', '679.99', '559.99', '400.00', 45, 10, 10, '2.56', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'OnePlus 12 - Buy Online', 'Eos dolores tempore quidem commodi harum dicta modi delectus ut eos nobis quasi reiciendis sint impedit sapiente.', 'OnePlus 12, buy, shop, online, Samsung', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(9, 1, 7, 'Xiaomi 14 Ultra', NULL, 'xiaomi-14-ultra-8', 'Eos minima debitis ut et id ducimus fugit error expedita.', 'Cupiditate ut et ut itaque. Voluptates ullam nam excepturi inventore expedita id tempora. Perspiciatis corporis rem a voluptatem quia inventore. Et impedit et vitae praesentium quam ipsum. Est suscipit hic consequatur libero perferendis est. Autem deserunt dolor libero. Molestias et quam velit maiores sunt corporis doloribus.', 'SKU-0009', 'pLiSX1LSRA', NULL, '899.99', '764.99', '629.99', '450.00', 30, 10, 10, '2.01', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Xiaomi 14 Ultra - Buy Online', 'In quasi ut voluptas molestiae omnis nobis architecto voluptatibus et odio quod voluptates ea rerum libero aperiam atque consequatur natus est quod officia.', 'Xiaomi 14 Ultra, buy, shop, online, Samsung', NULL, '2026-07-22 09:09:57', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(10, 11, 7, 'Microsoft Surface Pro 11', NULL, 'microsoft-surface-pro-11-9', 'Qui ut architecto dicta tempore nostrum sed quisquam saepe aliquam magni.', 'Nostrum laboriosam provident sunt ad molestias non iusto. Autem dolorum nostrum ipsum exercitationem unde voluptas. Deserunt ducimus consectetur expedita voluptatem aut dolores dolorem. Ad sit autem et voluptate.', 'SKU-0010', 'FU9z6DzPQV', NULL, '1099.99', '934.99', '769.99', '550.00', 25, 10, 10, '1.76', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Microsoft Surface Pro 11 - Buy Online', 'Corrupti laborum nihil suscipit illo aut libero adipisci aut omnis eveniet eum quos eos illum ut omnis saepe ullam tempora ex sapiente quia autem rerum.', 'Microsoft Surface Pro 11, buy, shop, online, Microsoft', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(11, 2, 6, 'Apple MacBook Pro 16\"', NULL, 'apple-macbook-pro-16-10', 'Nulla recusandae et maiores ut corporis necessitatibus et qui possimus non ab.', 'Nesciunt impedit doloremque nesciunt perferendis. Officia quis placeat velit. Qui aut cumque voluptas rerum est cumque consectetur. Magni sunt aut rerum quidem possimus quidem autem. Enim reiciendis sunt reiciendis cum ut et occaecati. Vel accusantium illo aut est. Quae voluptatibus esse ut omnis.', 'SKU-0011', 'kIi6HTqr3b', NULL, '2499.99', '2124.99', '1749.99', '1250.00', 30, 10, 10, '2.14', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple MacBook Pro 16\" - Buy Online', 'Unde in est exercitationem tenetur pariatur assumenda quisquam cupiditate corporis sit neque quo vel quo atque nihil dicta aut autem nihil.', 'Apple MacBook Pro 16\", buy, shop, online, Apple', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(12, 2, 6, 'Apple MacBook Air M3', NULL, 'apple-macbook-air-m3-11', 'Aut harum sit possimus vitae est expedita dolorum sapiente non aut debitis velit.', 'Inventore et impedit excepturi. Sit necessitatibus maxime velit illum doloremque. Est iure iste laborum id iusto dolorum. Nobis ea accusamus velit. Aspernatur eligendi esse dolor debitis. Similique dolor accusamus facere aut illo accusantium tempora.', 'SKU-0012', 'Ue8FWVVbtJ', NULL, '1299.99', '1104.99', '909.99', '650.00', 45, 10, 10, '9.40', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple MacBook Air M3 - Buy Online', 'Nesciunt cum omnis voluptas aut et possimus provident quaerat sint iusto dolore aliquam est ut adipisci veniam expedita.', 'Apple MacBook Air M3, buy, shop, online, Apple', NULL, '2026-07-26 18:26:08', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(13, 8, 6, 'Dell XPS 15', NULL, 'dell-xps-15-12', 'Quo ratione dolor dignissimos sit voluptatum et ipsum ex in perferendis et iusto sed sapiente.', 'Sed quia earum qui rerum. Dolorum quidem laboriosam aut aspernatur omnis temporibus. Voluptate autem soluta et consequatur consequatur. Ex eum iure accusamus fugiat.', 'SKU-0013', 'OQJC7SzeRP', NULL, '1799.99', '1529.99', '1259.99', '900.00', 20, 10, 10, '9.47', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Dell XPS 15 - Buy Online', 'Sunt cupiditate voluptates temporibus ad beatae architecto totam voluptatem dolor similique dolores praesentium omnis et nulla nihil ut quo fuga et porro aut et porro.', 'Dell XPS 15, buy, shop, online, Dell', NULL, '2026-07-31 14:50:13', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(14, 7, 6, 'HP Pavilion 15 Laptop', NULL, 'hp-pavilion-15-laptop-13', 'Omnis consequatur dignissimos rem praesentium aut enim placeat quidem et recusandae.', 'Ea eaque voluptatem ea. Ipsa et omnis voluptate veniam voluptatibus. Nesciunt omnis est consectetur et perferendis. Sed mollitia quidem cupiditate et sint hic.', 'SKU-0014', 'f8vNSzaecW', NULL, '799.99', '679.99', '559.99', '400.00', 40, 10, 10, '8.32', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'HP Pavilion 15 Laptop - Buy Online', 'Minima ea occaecati repudiandae quia corporis asperiores consequuntur accusamus ratione fuga voluptatem voluptas aperiam similique consequatur.', 'HP Pavilion 15 Laptop, buy, shop, online, HP', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(15, 9, 6, 'Lenovo ThinkPad X1 Carbon', NULL, 'lenovo-thinkpad-x1-carbon-14', 'Exercitationem est at quibusdam mollitia tempore temporibus laudantium soluta quo necessitatibus nobis provident voluptatem aut.', 'Magnam quia tempora sed culpa ea deleniti vero quam. Saepe et rem eos saepe molestiae sed minima. Ut est dolores vel est in. Praesentium est consequatur explicabo non nulla eos consequuntur. Quaerat possimus velit molestias qui sint. Eos et aut illum sunt deleniti. Quis consequatur sunt quaerat ut.', 'SKU-0015', 'S0XVnoRUWD', NULL, '1499.99', '1274.99', '1049.99', '750.00', 35, 10, 10, '0.17', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Lenovo ThinkPad X1 Carbon - Buy Online', 'Dolorem harum laborum repellat sint omnis vero perspiciatis eos voluptatum qui necessitatibus nihil non ducimus consectetur est eum dolore qui et.', 'Lenovo ThinkPad X1 Carbon, buy, shop, online, Lenovo', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(16, 10, 6, 'Asus ROG Zephyrus G14', NULL, 'asus-rog-zephyrus-g14-15', 'Eius laborum inventore sed assumenda neque nisi iure nihil ipsam ut voluptate magnam.', 'Delectus et ducimus doloribus. Ex quia saepe quidem odio sed autem corporis. Expedita vel asperiores possimus est. Et tempore fuga sunt cum iusto sint autem. Consectetur vitae et porro sit iure. Aut pariatur quod consequatur quo sapiente nostrum. Dolorem atque nam labore nulla magni ea unde.', 'SKU-0016', 'WfPU6glkLd', NULL, '1599.99', '1359.99', '1119.99', '800.00', 25, 10, 10, '9.13', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Asus ROG Zephyrus G14 - Buy Online', 'Temporibus voluptas delectus repellat aut molestias accusamus aut debitis dolores facere sed ut rerum voluptatibus et quis ut quae.', 'Asus ROG Zephyrus G14, buy, shop, online, Asus', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(17, 8, 6, 'Acer Swift 3', NULL, 'acer-swift-3-16', 'Accusantium ullam sint iure amet perferendis ut libero in aperiam vel delectus omnis.', 'Voluptates quaerat qui exercitationem et. Nihil qui cum tenetur occaecati quas amet. Distinctio at tempore ut voluptatem dignissimos quo laudantium. Perspiciatis occaecati omnis ullam tempore. Nesciunt aliquid maiores adipisci. Atque id porro qui. Sunt ipsa dignissimos officiis sint.', 'SKU-0017', 'eKHY4uYaHy', NULL, '699.99', '594.99', '489.99', '350.00', 56, 10, 10, '4.45', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Acer Swift 3 - Buy Online', 'Nihil sit quis aut odio odio dolores nam tempore ut ut corrupti dignissimos et quaerat aut.', 'Acer Swift 3, buy, shop, online, Dell', NULL, '2026-07-21 02:56:31', NULL, '2026-08-19 19:00:24', '2026-08-19 19:35:28'),
(18, 7, 6, 'HP Omen 17', NULL, 'hp-omen-17-17', 'Repudiandae tempora et harum id amet eos nihil eveniet ad id itaque provident rerum ex nesciunt modi non esse.', 'Minus sint molestiae qui repudiandae ea. Libero saepe quod aut qui qui. Ipsa esse ut dolor. Nostrum commodi exercitationem dolorem rerum omnis cupiditate. Qui et aut quis et sunt quo neque.', 'SKU-0018', '6tfE97b059', NULL, '1499.99', '1274.99', '1049.99', '750.00', 18, 10, 10, '9.57', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'HP Omen 17 - Buy Online', 'Autem architecto voluptatibus labore atque fuga quia doloremque exercitationem placeat rem omnis illum et doloremque rerum enim ullam possimus.', 'HP Omen 17, buy, shop, online, HP', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(19, 6, 1, 'LG OLED 65\" TV', NULL, 'lg-oled-65-tv-18', 'Voluptatem eligendi minima facere itaque quisquam velit reiciendis eum voluptatem.', 'Nemo ipsum amet in et et suscipit. Magnam fugiat explicabo pariatur non. Aut et molestiae praesentium beatae. Illum voluptatem sit quod. Quos nam voluptatem ipsum totam ipsam quae quo.', 'SKU-0019', 'g7DHYW4ITN', NULL, '1999.99', '1699.99', '1399.99', '1000.00', 20, 10, 10, '3.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'LG OLED 65\" TV - Buy Online', 'Omnis nesciunt odit qui quas adipisci aliquid dolor cumque aperiam aut quidem qui fuga qui nesciunt laboriosam ut.', 'LG OLED 65\" TV, buy, shop, online, LG', NULL, '2026-08-05 02:07:29', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(20, 1, 1, 'Samsung 65\" QLED TV', NULL, 'samsung-65-qled-tv-19', 'Eaque et praesentium magni sit laborum sed recusandae ut quod temporibus quae ea omnis doloremque doloribus similique neque.', 'Officia optio est autem et laboriosam. Eius sed sint totam est. Aliquam quo earum rem qui molestiae saepe fugit. Magni in ut reiciendis maiores quia aperiam. Perferendis ratione soluta maxime.', 'SKU-0020', 'E2qqv4r0eA', NULL, '1299.99', '1104.99', '909.99', '650.00', 25, 10, 10, '3.27', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Samsung 65\" QLED TV - Buy Online', 'Eum rerum et id distinctio voluptatem doloremque reprehenderit et et harum illo et molestiae ipsum natus excepturi aut consequatur soluta eos ipsam commodi ipsa.', 'Samsung 65\" QLED TV, buy, shop, online, Samsung', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(21, 3, 1, 'Sony Bravia 55\" LED', NULL, 'sony-bravia-55-led-20', 'Quod nihil natus et ratione omnis possimus est velit sapiente quia.', 'Ipsa rem quisquam vero voluptates doloremque delectus voluptatem. Enim ut est in neque nihil debitis est. Non fuga eos hic nihil omnis neque veritatis. Quisquam dolorem autem sit eaque consequatur. Aperiam tempore distinctio voluptatem enim facilis repudiandae. Non distinctio explicabo et voluptatibus. Nesciunt voluptatem magnam officia temporibus dignissimos suscipit voluptatem.', 'SKU-0021', 'VHJBGMK7Cm', NULL, '899.99', '764.99', '629.99', '450.00', 30, 10, 10, '9.04', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Sony Bravia 55\" LED - Buy Online', 'Ad quod hic quas facilis quia consequatur inventore molestias aut commodi aut harum ut distinctio.', 'Sony Bravia 55\" LED, buy, shop, online, Sony', NULL, '2026-08-06 23:46:35', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(22, 3, 1, 'Sony PlayStation 5', NULL, 'sony-playstation-5-21', 'Aliquid aut at atque aut magni sit animi sunt quos ut possimus aperiam ut ipsum ullam inventore magnam sit autem est.', 'Ut eius iste est nulla ratione et vitae voluptatem. Maiores quia accusamus velit vel soluta totam in voluptate. Voluptatum sit dolores ut quis quidem eveniet qui. Iure omnis vel ipsa id. Totam at repellat quia illo repudiandae. Et corporis et incidunt perspiciatis qui dignissimos. Est officia consequuntur ducimus recusandae a deleniti et natus.', 'SKU-0022', 'jYEw6pxtd9', NULL, '499.99', '424.99', '349.99', '250.00', 45, 10, 10, '6.85', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Sony PlayStation 5 - Buy Online', 'Sint molestias sunt laborum aut voluptatibus ad ducimus in ipsam placeat sint eos itaque eum commodi voluptas corrupti consequuntur sapiente odit eos suscipit consequuntur.', 'Sony PlayStation 5, buy, shop, online, Sony', NULL, '2026-07-31 04:25:43', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(23, 1, 1, 'Samsung Galaxy Tab S9', NULL, 'samsung-galaxy-tab-s9-22', 'Rem deleniti inventore nobis tempora provident quae porro fugiat optio a et cum ad praesentium maiores.', 'Fuga sit voluptates in rerum ratione minus dignissimos quia. Ab nisi voluptate sequi dolorem tempora molestias. Praesentium quis suscipit voluptatibus velit suscipit. Alias est ab cumque necessitatibus iusto qui nam rerum. Quis exercitationem laudantium soluta quaerat et. Aut odio excepturi praesentium ut cum impedit. Consectetur quia officia optio eligendi cumque voluptatem.', 'SKU-0023', 'oN8FYHFF4p', NULL, '799.99', '679.99', '559.99', '400.00', 35, 10, 10, '3.19', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Samsung Galaxy Tab S9 - Buy Online', 'Qui distinctio exercitationem enim commodi sed ullam quidem veritatis adipisci ab perspiciatis sed facilis.', 'Samsung Galaxy Tab S9, buy, shop, online, Samsung', NULL, '2026-08-01 19:04:51', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(24, 2, 8, 'Apple AirPods Pro 2', NULL, 'apple-airpods-pro-2-23', 'Aut distinctio qui iure velit quis non pariatur sit vel.', 'Ab ratione et dolore eos sint. Ullam quisquam iure sed eveniet quia iste quia qui. Deserunt at qui tenetur distinctio esse quam excepturi. Aperiam officia voluptatem quisquam nemo sed eveniet aperiam quia. Aliquam dignissimos nisi repudiandae omnis distinctio eos. Magni harum veniam magni nulla placeat commodi tempore pariatur.', 'SKU-0024', 'Gl0ZxzS5SM', NULL, '249.00', '211.65', '174.30', '124.50', 200, 10, 10, '6.93', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Apple AirPods Pro 2 - Buy Online', 'Qui dicta et deleniti consequuntur ullam distinctio veniam quis cupiditate doloribus minima id iste laboriosam accusamus.', 'Apple AirPods Pro 2, buy, shop, online, Apple', NULL, '2026-08-03 02:16:03', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(25, 3, 8, 'Sony WH-1000XM5 Headphones', NULL, 'sony-wh-1000xm5-headphones-24', 'Rerum eius voluptatum vitae reiciendis voluptatem voluptate ut sit consequatur.', 'Inventore quo recusandae maxime. Sunt qui sint magni id. Architecto officia optio quas ratione ea quia dolorum aliquid. Veniam consequatur enim quo error cumque temporibus iusto. Laboriosam cum dolore quis voluptate.', 'SKU-0025', 'rLI0kzemaN', NULL, '349.99', '297.49', '244.99', '175.00', 100, 10, 10, '1.26', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Sony WH-1000XM5 Headphones - Buy Online', 'Et placeat distinctio voluptas et non qui reiciendis repudiandae aut expedita enim doloremque deserunt eaque necessitatibus rerum qui in inventore totam eligendi quia nulla tenetur maxime dolores.', 'Sony WH-1000XM5 Headphones, buy, shop, online, Sony', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(26, 13, 8, 'Bose QuietComfort 45', NULL, 'bose-quietcomfort-45-25', 'Quod officiis qui laboriosam dolore non et est adipisci rerum atque voluptatem autem excepturi sed ea quas impedit numquam sit accusantium.', 'Sapiente asperiores porro quisquam libero. Possimus incidunt nobis at. Id fugit voluptatibus voluptatem praesentium natus corrupti. Aut dolores qui commodi numquam ut recusandae nihil. Error consectetur omnis optio reprehenderit cumque. Illo nulla odit corporis. Commodi unde earum reiciendis id.', 'SKU-0026', 'a3D22SAt9O', NULL, '329.99', '280.49', '230.99', '165.00', 80, 10, 10, '4.83', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Bose QuietComfort 45 - Buy Online', 'Veniam architecto maiores et suscipit quidem deleniti et nihil expedita aut deleniti voluptatibus architecto quo hic ipsa cupiditate beatae voluptatibus quia animi numquam maiores quis.', 'Bose QuietComfort 45, buy, shop, online, Bose', NULL, '2026-08-05 07:18:39', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(27, 14, 8, 'JBL Flip 6 Speaker', NULL, 'jbl-flip-6-speaker-26', 'Magni est blanditiis qui debitis neque distinctio quo est autem saepe quo voluptatibus eos sit libero consequatur numquam minima totam.', 'Incidunt est dolorem possimus ea voluptas enim. Omnis voluptatum natus labore quidem facilis. Perferendis adipisci quidem voluptatem architecto vitae ad minima rem. Quia placeat omnis odit eveniet dolores ullam quia. Consequatur et tenetur mollitia animi necessitatibus eligendi.', 'SKU-0027', 'W9d5HyksfP', NULL, '129.99', '110.49', '90.99', '65.00', 120, 10, 10, '9.79', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'JBL Flip 6 Speaker - Buy Online', 'Numquam optio aut aut rerum quia suscipit debitis omnis doloremque impedit repudiandae reprehenderit rerum.', 'JBL Flip 6 Speaker, buy, shop, online, JBL', NULL, '2026-07-20 11:46:15', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(28, 14, 8, 'JBL Quantum 800', NULL, 'jbl-quantum-800-27', 'Sequi ipsa qui ratione aperiam quibusdam est cumque sint eum sit iure ut cumque delectus aperiam quis temporibus tempora.', 'Qui nihil impedit soluta voluptate. Iste sit similique perspiciatis doloribus eaque eos. Mollitia aut iure veritatis quia ipsum. Id ut nihil et qui dolores dolore tempora. Quidem commodi tenetur rerum.', 'SKU-0028', 'LG0vzmrptA', NULL, '199.99', '169.99', '139.99', '100.00', 65, 10, 10, '9.23', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'JBL Quantum 800 - Buy Online', 'Aut nesciunt officia consequuntur nostrum rerum aut et cupiditate reprehenderit consequatur natus velit et quae est id odit voluptatem qui eum alias consequatur dolor velit.', 'JBL Quantum 800, buy, shop, online, JBL', NULL, '2026-08-16 06:32:04', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(29, 13, 8, 'Bose SoundLink Bluetooth Speaker', NULL, 'bose-soundlink-bluetooth-speaker-28', 'Illum fuga facilis inventore dolorem similique vero sed rerum omnis voluptate voluptas nihil quos vel atque ratione praesentium unde.', 'Quasi dolorem deserunt qui suscipit qui et et. Non aliquid dicta eius quasi id. Est accusamus architecto similique impedit ullam. Provident repudiandae nesciunt et neque architecto quae.', 'SKU-0029', 'pWn2xf8twB', NULL, '199.99', '169.99', '139.99', '100.00', 50, 10, 10, '2.77', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Bose SoundLink Bluetooth Speaker - Buy Online', 'Sed possimus architecto enim quia officiis saepe sit distinctio quas delectus sed qui quia.', 'Bose SoundLink Bluetooth Speaker, buy, shop, online, Bose', NULL, '2026-07-30 18:11:37', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(30, 4, 9, 'Nike Air Max 270', NULL, 'nike-air-max-270-29', 'Doloremque autem et ipsam dolor minima fuga dignissimos magni libero.', 'Magnam veritatis eum sequi nam. Ratione rerum mollitia eaque porro. Fugit soluta illo accusamus et molestiae ut quis. Inventore nam sapiente similique recusandae quam aliquid minus eaque. Quaerat non recusandae delectus vitae. Eius maxime officia corrupti illo. Sint voluptatibus nostrum iste molestiae sint quod.', 'SKU-0030', 'BvHsOdEBTi', NULL, '150.00', '127.50', '105.00', '75.00', 200, 10, 10, '3.58', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nike Air Max 270 - Buy Online', 'Occaecati at consequatur neque et est ab eius ut rerum quisquam aut alias sequi suscipit labore id dolorem quam sunt.', 'Nike Air Max 270, buy, shop, online, Nike', NULL, '2026-08-05 11:42:34', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(31, 4, 9, 'Nike Dry-Fit T-Shirt', NULL, 'nike-dry-fit-t-shirt-30', 'Ea veniam et pariatur soluta voluptas nihil voluptates aut a.', 'Facilis ipsum sit minima inventore omnis illo veniam est. Ducimus non nisi quia voluptatum eum quis minus. Aut ut debitis cumque necessitatibus. Et qui ipsa non aut cupiditate dolorum.', 'SKU-0031', 'PWlkVq9B8q', NULL, '35.00', '29.75', '24.50', '17.50', 500, 10, 10, '9.44', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nike Dry-Fit T-Shirt - Buy Online', 'Mollitia labore est dignissimos magni dolorum delectus qui minima maiores et neque temporibus dolores nesciunt ea.', 'Nike Dry-Fit T-Shirt, buy, shop, online, Nike', NULL, '2026-07-28 11:02:30', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(32, 4, 9, 'Nike Running Shorts', NULL, 'nike-running-shorts-31', 'Qui nobis ut est non dolorem deserunt iure quisquam vel tempora aut omnis dolores cum odit fuga odio.', 'Molestiae maiores ex voluptatem quia. Omnis harum qui voluptates. At commodi et maiores. Velit optio cum a aut. Labore magni voluptatibus saepe dolorem.', 'SKU-0032', 'Yb2jAI7YKB', NULL, '45.00', '38.25', '31.50', '22.50', 350, 10, 10, '8.64', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nike Running Shorts - Buy Online', 'Aut consequatur aut qui nemo facere officiis omnis facilis dolores non perferendis dolore quae molestiae.', 'Nike Running Shorts, buy, shop, online, Nike', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(33, 5, 9, 'Adidas Ultraboost 22', NULL, 'adidas-ultraboost-22-32', 'In incidunt natus reprehenderit qui eos qui nemo qui officia aut fugit voluptatem deleniti.', 'Odio id maiores vitae veritatis ducimus odio enim. Nostrum deleniti ab nulla libero et. Ducimus recusandae unde non quis commodi dolor. Ducimus veritatis blanditiis sit accusamus sunt labore blanditiis. Aut excepturi laudantium sit voluptatum.', 'SKU-0033', '2VRka3pKBZ', NULL, '180.00', '153.00', '126.00', '90.00', 150, 10, 10, '3.41', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Adidas Ultraboost 22 - Buy Online', 'Asperiores et possimus sit et quam animi eum eum ut nihil cum repellat dolorum eaque omnis fugiat rerum.', 'Adidas Ultraboost 22, buy, shop, online, Adidas', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(34, 5, 9, 'Adidas Track Pants', NULL, 'adidas-track-pants-33', 'Ipsam maiores pariatur amet et ad autem amet et velit enim consectetur.', 'Harum quo occaecati consequatur dolore et eum qui. Dolorem non ab error ipsam libero aperiam sint. Aliquid voluptatem quas quia dolor ut omnis. Earum dolorem vitae nihil. Tempore commodi tenetur recusandae veniam qui unde temporibus.', 'SKU-0034', 'yVWGIRzVxw', NULL, '65.00', '55.25', '45.50', '32.50', 300, 10, 10, '9.73', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Adidas Track Pants - Buy Online', 'Sunt incidunt a qui incidunt numquam et omnis consequuntur aut mollitia blanditiis nobis nam quia.', 'Adidas Track Pants, buy, shop, online, Adidas', NULL, '2026-08-09 10:08:13', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(35, 17, 9, 'Under Armour HeatGear Shirt', NULL, 'under-armour-heatgear-shirt-34', 'Qui voluptates sed soluta minus et aut non architecto voluptates temporibus nam magni.', 'Dolor illum inventore sed illo voluptatem quis sed et. Cum facere esse cupiditate vitae nulla beatae. Occaecati adipisci et voluptatem minima iure cumque maxime. Aut eligendi dolores velit voluptatibus ut. Omnis quibusdam dolorem culpa natus sunt eveniet est. Sit ea neque impedit voluptatem laudantium.', 'SKU-0035', 'Rcnub9uWjW', NULL, '30.00', '25.50', '21.00', '15.00', 400, 10, 10, '5.06', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Under Armour HeatGear Shirt - Buy Online', 'Recusandae expedita voluptates ea nemo ipsum sequi ratione odio iste quibusdam occaecati voluptatum dolor.', 'Under Armour HeatGear Shirt, buy, shop, online, Under Armour', NULL, '2026-07-27 03:31:11', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(36, 4, 9, 'Nike Sportswear Club Fleece', NULL, 'nike-sportswear-club-fleece-35', 'Et consectetur eos facilis nulla voluptatem hic tempora voluptate et.', 'Blanditiis esse enim vel voluptatibus et rem. Perferendis asperiores dolorum ut. Ducimus reprehenderit ut optio expedita necessitatibus sunt fuga. Quia expedita aspernatur sed maxime minima sed rerum architecto.', 'SKU-0036', 'iRaY7P9tEO', NULL, '85.00', '72.25', '59.50', '42.50', 180, 10, 10, '9.82', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nike Sportswear Club Fleece - Buy Online', 'Voluptatem tempore est qui at quos rerum nisi recusandae quia odio repellendus eos iusto facilis nihil sed distinctio et.', 'Nike Sportswear Club Fleece, buy, shop, online, Nike', NULL, '2026-08-08 14:59:45', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(37, 5, 9, 'Adidas Originals 3-Stripes', NULL, 'adidas-originals-3-stripes-36', 'Iure ducimus ut aliquam dolor officiis similique facere nam neque placeat molestiae optio eaque nihil consequatur consectetur blanditiis.', 'In sunt non libero exercitationem voluptas aperiam. Eaque deleniti voluptates nihil minima tempora eaque eum placeat. Nesciunt et aperiam quidem possimus provident vel. Voluptatibus ex repudiandae molestiae. Et dicta facere aut dolores adipisci odio. Et consequatur in qui unde eos rerum assumenda.', 'SKU-0037', 'EsS6de2hM7', NULL, '75.00', '63.75', '52.50', '37.50', 220, 10, 10, '5.03', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Adidas Originals 3-Stripes - Buy Online', 'Ab quia est nisi in blanditiis rerum qui iure debitis aliquid laudantium qui corrupti et.', 'Adidas Originals 3-Stripes, buy, shop, online, Adidas', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(38, 4, 10, 'Women Cotton T-Shirt', NULL, 'women-cotton-t-shirt-37', 'Aspernatur sit qui officiis officia id sequi pariatur dolor dolor in distinctio dolor eos reiciendis doloribus consectetur vel repellendus.', 'Fugiat tempore facere consequuntur laboriosam ad voluptatem. Eaque expedita iusto nam accusamus repellendus omnis. Soluta odit vitae sint placeat quia voluptatem. Maxime quia magni quas eligendi voluptatem velit. Debitis eos incidunt maxime architecto.', 'SKU-0038', 'whPDZ9F72v', NULL, '28.00', '23.80', '19.60', '14.00', 450, 10, 10, '8.18', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Women Cotton T-Shirt - Buy Online', 'Nostrum ut suscipit modi et et officia facere itaque suscipit minima in et earum illum alias totam eius quasi magnam omnis vero provident cumque debitis molestias facere.', 'Women Cotton T-Shirt, buy, shop, online, Nike', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(39, 5, 10, 'Women Running Shoes', NULL, 'women-running-shoes-38', 'Velit ratione sapiente voluptatem optio rerum iusto magnam quia pariatur.', 'Totam consequatur neque possimus magnam architecto et molestias et. Dicta sit placeat debitis. Est quo et et ipsum nisi ducimus enim qui. Eaque voluptas magni dolores quis officiis. Eaque ullam ut quis dolorem et ut. Maxime omnis incidunt veniam nemo dolore sequi.', 'SKU-0039', 'dJveTdqV8p', NULL, '120.00', '102.00', '84.00', '60.00', 160, 10, 10, '9.78', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Women Running Shoes - Buy Online', 'Nisi sint voluptatum qui quae dolores velit excepturi ut reiciendis voluptatibus similique quidem ut.', 'Women Running Shoes, buy, shop, online, Adidas', NULL, '2026-08-04 13:24:49', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(40, 4, 11, 'Kids Cotton T-Shirt', NULL, 'kids-cotton-t-shirt-39', 'Eos sunt doloribus labore enim non ea vero qui non quaerat est ex eos ut dolores et provident.', 'Sed eligendi nobis repudiandae. Qui repudiandae aut quibusdam quisquam sit repudiandae deleniti. Optio quae in autem excepturi. Minima voluptatem libero dolor ut aliquid. Optio voluptate sed quas.', 'SKU-0040', 'lTmKxJGzII', NULL, '20.00', '17.00', '14.00', '10.00', 300, 10, 10, '4.04', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Kids Cotton T-Shirt - Buy Online', 'Aut aut ut eius quia at nesciunt exercitationem est corporis odio et quis enim voluptatem.', 'Kids Cotton T-Shirt, buy, shop, online, Nike', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(41, 5, 11, 'Kids Sneakers', NULL, 'kids-sneakers-40', 'Voluptatum doloremque cumque unde quo aut sit accusantium quis ab reprehenderit cum porro sit placeat eum architecto labore et minima est.', 'Error error eius eaque eveniet culpa non voluptatum quis. Accusamus deserunt qui natus voluptates commodi et maxime. Autem sint vitae sint est eligendi. Ut a aperiam commodi recusandae voluptates quis perferendis. Quasi omnis numquam culpa et hic quae aut. Sapiente velit tenetur veniam architecto temporibus numquam quae.', 'SKU-0041', 'bmD445zmMT', NULL, '65.00', '55.25', '45.50', '32.50', 250, 10, 10, '9.98', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Kids Sneakers - Buy Online', 'Eaque quia consequatur qui et consequatur saepe quia vitae voluptate et est maxime aut quae rerum corrupti minima iste.', 'Kids Sneakers, buy, shop, online, Adidas', NULL, '2026-07-21 00:13:22', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(42, 12, 3, 'Philips Hue Starter Kit', NULL, 'philips-hue-starter-kit-41', 'Accusamus expedita eum accusantium optio sunt dolorem doloremque a cupiditate et illum et eos sit adipisci maiores id fugit.', 'Voluptatum fugiat quas omnis omnis tenetur perspiciatis. Quod accusantium nihil aperiam ipsam eius enim aspernatur. In fugiat voluptates nesciunt. Et qui molestiae esse. Quos quia rem dignissimos excepturi consequatur dolores deserunt. Et autem quaerat id tempora qui earum eaque.', 'SKU-0042', 'WSGsZ0XMkL', NULL, '199.99', '169.99', '139.99', '100.00', 60, 10, 10, '2.56', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Philips Hue Starter Kit - Buy Online', 'Rerum cum rerum harum in consectetur possimus sint magni fugit quod laborum et corporis doloremque voluptates numquam repellat qui quo eius est velit nihil minus et ratione.', 'Philips Hue Starter Kit, buy, shop, online, Philips', NULL, '2026-08-13 08:28:06', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(43, 1, 12, 'Furniture Office Chair Pro', NULL, 'furniture-office-chair-pro-42', 'Doloribus numquam similique incidunt ut quia perspiciatis praesentium est est sit exercitationem repellat minima nemo temporibus qui iste quis.', 'Pariatur sunt explicabo quo minus qui magni. Alias dolor voluptate possimus reprehenderit. Doloribus eligendi ipsum enim. Quos consequatur officia recusandae laboriosam voluptas. Rerum occaecati fugit eaque molestiae voluptatem beatae. Ducimus ea in magnam eos rem corporis explicabo. Cupiditate rerum odio nemo quia quia autem.', 'SKU-0043', '7PxGWht2q8', NULL, '599.00', '509.15', '419.30', '299.50', 10, 10, 10, '4.37', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Furniture Office Chair Pro - Buy Online', 'Et at quis delectus labore et ab esse quia nisi omnis amet sunt rerum qui animi laboriosam est sed eos dolores dolorem.', 'Furniture Office Chair Pro, buy, shop, online, Samsung', NULL, '2026-07-30 23:21:36', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(44, 12, 3, 'Home Air Purifier', NULL, 'home-air-purifier-43', 'Dolor ipsum dicta est magni optio deleniti aliquam est ut officiis error necessitatibus reiciendis sit ipsam placeat qui provident est.', 'Exercitationem ut et unde laborum. Alias est tempore esse et velit corrupti. Magni cumque non officiis sunt reiciendis quasi sit. Necessitatibus exercitationem voluptatem ratione et atque qui et. Quia enim officiis in repudiandae.', 'SKU-0044', 'JwmLMgWe39', NULL, '149.99', '127.49', '104.99', '75.00', 40, 10, 10, '2.61', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Home Air Purifier - Buy Online', 'Quis unde soluta aut atque praesentium natus omnis labore exercitationem nemo explicabo maiores voluptatum et ut perspiciatis eos.', 'Home Air Purifier, buy, shop, online, Philips', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(45, 4, 4, 'Nike Sports Yoga Mat', NULL, 'nike-sports-yoga-mat-44', 'Laborum velit culpa et quis quaerat aliquam ut eveniet impedit unde architecto dolorem eius quo eos doloribus occaecati voluptatem.', 'Et vitae vel quia cumque. Autem et omnis ipsam odio consectetur. Et et est dolorum officiis quasi ipsum. Id officia soluta nulla distinctio nostrum libero. Officia dicta at dolorem fugiat incidunt earum. Blanditiis eos at sapiente expedita velit cum assumenda. Animi doloremque veritatis ea exercitationem maiores recusandae. Est perspiciatis sed et quam vero fugit neque.', 'SKU-0045', 'x46uUU1Kua', NULL, '35.00', '29.75', '24.50', '17.50', 180, 10, 10, '7.08', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nike Sports Yoga Mat - Buy Online', 'Illo ipsa numquam ut facere libero deserunt consequatur et maxime et non nesciunt.', 'Nike Sports Yoga Mat, buy, shop, online, Nike', NULL, '2026-08-10 10:22:44', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(46, 5, 14, 'Adidas Football', NULL, 'adidas-football-45', 'Consectetur quidem temporibus tenetur consectetur id iure provident saepe aspernatur impedit voluptate deleniti voluptatem voluptatem quo sed nihil.', 'Nihil quia facilis ratione eius. Qui possimus ipsa minima animi. Vel pariatur soluta qui eos vero quia et est. Molestiae aperiam repudiandae et iste est.', 'SKU-0046', 'shGyHj3gtG', NULL, '25.00', '21.25', '17.50', '12.50', 300, 10, 10, '5.60', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Adidas Football - Buy Online', 'Excepturi aliquid eveniet mollitia consectetur possimus excepturi maiores quia adipisci vel sit alias neque doloremque ea voluptatem minus consequatur cum adipisci libero.', 'Adidas Football, buy, shop, online, Adidas', NULL, '2026-08-18 04:30:36', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(47, 4, 15, 'Basketball Official Size 7', NULL, 'basketball-official-size-7-46', 'Dignissimos aut rerum dolorem deserunt ex at et est voluptas autem voluptas incidunt quidem nostrum fugiat earum illo consequatur dolor ab.', 'In ea est asperiores iure. Magnam commodi impedit quia aperiam natus voluptatem qui. Quia veritatis laboriosam odit non ipsum. Asperiores ut saepe nostrum neque molestiae ea velit. Minus nesciunt unde minima est laboriosam. Corrupti deleniti et aut accusantium repellendus porro consequatur.', 'SKU-0047', 'PWhRx2ZmUC', NULL, '30.00', '25.50', '21.00', '15.00', 250, 10, 10, '8.67', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Basketball Official Size 7 - Buy Online', 'Maiores est voluptatem dolore et voluptate non qui sint autem id et ullam voluptates earum officiis sint voluptatem eaque non rerum provident itaque.', 'Basketball Official Size 7, buy, shop, online, Nike', NULL, '2026-08-13 16:48:26', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(48, 15, 16, 'Canon EOS R5 Camera', NULL, 'canon-eos-r5-camera-47', 'Saepe facere iusto corrupti nisi tempora sit doloremque qui qui rerum.', 'Molestiae sit exercitationem nisi provident voluptatibus provident fugiat. Consequatur tempore tenetur expedita nisi totam numquam ut. Nostrum et totam quia. Assumenda a numquam voluptates itaque culpa placeat ex accusantium.', 'SKU-0048', 'KVFiVCW9es', NULL, '3899.00', '3314.15', '2729.30', '1949.50', 15, 10, 10, '9.68', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Canon EOS R5 Camera - Buy Online', 'Necessitatibus ratione voluptate deleniti aut qui est et est harum ad ut explicabo voluptas nobis quod a nisi et non impedit saepe rem excepturi iusto nisi.', 'Canon EOS R5 Camera, buy, shop, online, Canon', NULL, '2026-08-10 20:13:55', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(49, 16, 16, 'Nikon Z9 Camera Body', NULL, 'nikon-z9-camera-body-48', 'Est omnis ut sequi iste quis consequuntur ut repudiandae doloremque nam laboriosam enim quod qui est qui quis sequi sunt.', 'Distinctio nulla omnis ex quis. Dolorem at qui ut. Ipsa rerum non similique ut. Aut sint quasi impedit quam laborum dicta ducimus.', 'SKU-0049', '437sverGZM', NULL, '5499.00', '4674.15', '3849.30', '2749.50', 10, 10, 10, '1.21', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nikon Z9 Camera Body - Buy Online', 'Nisi possimus et exercitationem sit debitis corrupti dolores minima quasi sunt magni et atque autem doloremque adipisci saepe porro atque facere.', 'Nikon Z9 Camera Body, buy, shop, online, Nikon', NULL, '2026-08-15 13:10:56', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(50, 15, 17, 'Canon RF 50mm f/1.2 Lens', NULL, 'canon-rf-50mm-f12-lens-49', 'Omnis rerum maiores omnis nam consequuntur sequi voluptas quis omnis numquam sed totam enim deleniti libero exercitationem.', 'Facilis itaque ut nam enim harum. Recusandae aperiam voluptas suscipit delectus. Blanditiis accusamus incidunt perferendis. Assumenda quod beatae accusamus cupiditate non ea blanditiis. Quidem voluptatem dolorem aut modi. Vel quisquam aut non quisquam iure nemo. Sunt quis explicabo nobis et ea.', 'SKU-0050', 'VKDyq4HFfJ', NULL, '2299.00', '1954.15', '1609.30', '1149.50', 12, 10, 10, '0.56', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Canon RF 50mm f/1.2 Lens - Buy Online', 'Hic quo non ut quos voluptatem dolore perferendis ut inventore rerum ullam eos blanditiis sint eveniet omnis nisi quibusdam.', 'Canon RF 50mm f/1.2 Lens, buy, shop, online, Canon', NULL, '2026-08-04 08:55:32', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(51, 16, 17, 'Nikon NIKKOR Z 24-70mm f/2.8', NULL, 'nikon-nikkor-z-24-70mm-f28-50', 'Et repellendus minima tempora blanditiis minima aut aliquid asperiores voluptate.', 'Iure et beatae ipsam. Cum ad corrupti pariatur officiis sit quis non velit. Omnis nihil exercitationem optio. Omnis cupiditate sint et minus velit. Iusto ut facilis et nesciunt voluptas.', 'SKU-0051', 'bH6Zlo5LGC', NULL, '2099.00', '1784.15', '1469.30', '1049.50', 8, 10, 10, '1.23', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', 'Nikon NIKKOR Z 24-70mm f/2.8 - Buy Online', 'Veniam perspiciatis earum quo qui aut deleniti repellat corrupti numquam placeat autem excepturi consequuntur libero distinctio delectus inventore voluptatem nam saepe expedita minus sit amet.', 'Nikon NIKKOR Z 24-70mm f/2.8, buy, shop, online, Nikon', NULL, NULL, NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(52, 3, 17, 'Sony FE 70-200mm f/2.8 GM II', NULL, 'sony-fe-70-200mm-f28-gm-ii-51', 'Unde quidem minus autem aliquid quis veniam praesentium ducimus voluptate.', 'Omnis quia sit nesciunt sit quia fugiat et nostrum. Dolorem id dolor dolorum qui repellat. Ullam facilis quidem autem ea eveniet. Libero ut dignissimos earum adipisci assumenda sed est. Autem sed eius enim est vero quaerat. Explicabo voluptatem mollitia aliquam excepturi delectus ab.', 'SKU-0052', 'YrcYyIYqKu', NULL, '2699.99', '2294.99', '1889.99', '1350.00', 10, 10, 10, '8.99', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', 'Sony FE 70-200mm f/2.8 GM II - Buy Online', 'Et nulla explicabo animi quia enim asperiores alias tenetur voluptatem dicta quod ipsam id iusto vitae et voluptas et dolores.', 'Sony FE 70-200mm f/2.8 GM II, buy, shop, online, Sony', NULL, '2026-07-20 16:32:02', NULL, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(53, 1, 7, 'Samsung Galaxy S24 Ultra', 'স্যামসাং গ্যালাক্সি এস২৪ আলট্রা', 'samsung-galaxy-s24-ultra', 'Galaxy AI flagship with S Pen, 200MP camera, and titanium frame.', '<h2>Samsung Galaxy S24 Ultra</h2><p>The ultimate Galaxy experience. Featuring the Snapdragon 8 Gen 3 processor, a stunning 6.8\" QHD+ Dynamic AMOLED display, and an embedded S Pen for productivity on the go.</p><ul><li>200MP main camera with AI-enhanced photography</li><li>Titanium frame — stronger and lighter</li><li>5000mAh battery with 45W fast charging</li><li>Galaxy AI: Live Translate, Circle to Search, Chat Assist</li></ul>', 'SAM-S24U-256', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Samsung+Galaxy+S24+U...', '139999.00', '118999.15', '97999.30', '69999.50', 50, 10, 10, '0.23', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(54, 1, 7, 'Samsung Galaxy S24+', NULL, 'samsung-galaxy-s24', 'Big screen Galaxy AI experience with 4900mAh battery.', NULL, 'SAM-S24P', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Samsung+Galaxy+S24%2B', '99999.00', '84999.15', '69999.30', '49999.50', 80, 10, 10, '0.19', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(55, 1, 7, 'Samsung Galaxy A54 5G', NULL, 'samsung-galaxy-a54-5g', 'Premium mid-range with IP67 water resistance and 50MP OIS camera.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Samsung+Galaxy+A54+5...', '38999.00', '33149.15', '27299.30', '19499.50', 120, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07');
INSERT INTO `products` (`id`, `brand_id`, `category_id`, `name`, `name_bn`, `slug`, `short_description`, `description`, `sku`, `barcode`, `thumbnail`, `regular_price`, `sale_price`, `wholesale_price`, `cost_price`, `stock`, `minimum_stock`, `maximum_order`, `weight`, `length`, `width`, `height`, `tax_class`, `shipping_class`, `status`, `product_type`, `featured`, `visibility`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `published_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(56, 2, 7, 'Apple iPhone 15 Pro Max', 'অ্যাপল আইফোন ১৫ প্রো ম্যাক্স', 'apple-iphone-15-pro-max', 'Titanium design, A17 Pro chip, and a 5x Telephoto camera.', NULL, 'APL-15PM', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+iPhone+15+Pro...', '159999.00', '135999.15', '111999.30', '79999.50', 40, 10, 10, '0.22', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(57, 2, 7, 'Apple iPhone 15', NULL, 'apple-iphone-15', 'Dynamic Island, 48MP camera, USB-C, and color-infused glass back.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+iPhone+15', '109999.00', '93499.15', '76999.30', '54999.50', 60, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(58, 2, 7, 'Apple iPhone 14', NULL, 'apple-iphone-14', 'A15 Bionic chip, 12MP dual camera system, Ceramic Shield front.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+iPhone+14', '79999.00', '67999.15', '55999.30', '39999.50', 100, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(59, 1, 7, 'Xiaomi 14 Ultra', NULL, 'xiaomi-14-ultra', 'Leica Summilux optics, Snapdragon 8 Gen 3, 5000mAh battery.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Xiaomi+14+Ultra', '89999.00', '76499.15', '62999.30', '44999.50', 30, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(60, 2, 6, 'Apple MacBook Pro 16\" M3 Max', 'অ্যাপল ম্যাকবুক প্রো ১৬\" এম৩ ম্যাক্স', 'apple-macbook-pro-16-m3-max', 'The most powerful Mac laptop ever. Up to 128GB unified memory.', NULL, 'APL-MBP16-M3', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+MacBook+Pro+16...', '319999.00', '271999.15', '223999.30', '159999.50', 20, 10, 10, '2.14', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(61, 2, 6, 'Apple MacBook Air 15\" M3', NULL, 'apple-macbook-air-15-m3', 'Impossibly thin. Incredibly powerful. 18-hour battery life.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+MacBook+Air+15...', '159999.00', '135999.15', '111999.30', '79999.50', 45, 10, 10, '1.51', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(62, 8, 6, 'Dell XPS 15 (2024)', NULL, 'dell-xps-15-2024', 'InfinityEdge OLED display, Intel Core Ultra 7, NVIDIA RTX 4060.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Dell+XPS+15+%282024%29', '189999.00', '161499.15', '132999.30', '94999.50', 20, 10, 10, '1.86', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(63, 9, 6, 'Lenovo ThinkPad X1 Carbon Gen 12', NULL, 'lenovo-thinkpad-x1-carbon-gen-12', 'Ultra-light business laptop with 14\" OLED display and Intel vPro.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Lenovo+ThinkPad+X1+C...', '179999.00', '152999.15', '125999.30', '89999.50', 35, 10, 10, '1.08', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(64, 10, 6, 'Asus ROG Zephyrus G16', NULL, 'asus-rog-zephyrus-g16', 'NVIDIA RTX 4070, Intel Core Ultra 9, 16\" ROG Nebula OLED.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Asus+ROG+Zephyrus+G1...', '209999.00', '178499.15', '146999.30', '104999.50', 25, 10, 10, '1.85', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(65, 7, 6, 'HP Pavilion 15', NULL, 'hp-pavilion-15', 'Everyday laptop with AMD Ryzen 5, 8GB RAM, 512GB SSD.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=HP+Pavilion+15', '64999.00', '55249.15', '45499.30', '32499.50', 40, 10, 10, '1.74', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(66, 2, 8, 'Apple AirPods Pro 2 (USB-C)', NULL, 'apple-airpods-pro-2-usb-c', 'Active Noise Cancellation, Adaptive Audio, USB-C, 6hr battery.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+AirPods+Pro+2...', '27999.00', '23799.15', '19599.30', '13999.50', 200, 10, 10, '0.05', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(67, 3, 8, 'Sony WH-1000XM5', NULL, 'sony-wh-1000xm5', 'Industry-leading noise cancellation with Auto NC Optimizer.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Sony+WH-1000XM5', '37999.00', '32299.15', '26599.30', '18999.50', 100, 10, 10, '0.25', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(68, 13, 8, 'Bose QuietComfort Ultra Headphones', NULL, 'bose-quietcomfort-ultra-headphones', 'Immersive spatial audio, world-class noise cancellation.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Bose+QuietComfort+Ul...', '39999.00', '33999.15', '27999.30', '19999.50', 80, 10, 10, '0.25', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(69, 14, 8, 'JBL Charge 5', NULL, 'jbl-charge-5', 'Portable Bluetooth speaker with IP67 waterproof and powerbank.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=JBL+Charge+5', '16999.00', '14449.15', '11899.30', '8499.50', 150, 10, 10, '0.96', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(70, 3, 1, 'Sony PlayStation 5 Slim', NULL, 'sony-playstation-5-slim', 'Slimmer PS5 with 1TB SSD, 4K gaming, and DualSense controller.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Sony+PlayStation+5+S...', '54999.00', '46749.15', '38499.30', '27499.50', 60, 10, 10, '3.20', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(71, 4, 9, 'Nike Air Max 270', 'নাইকে এয়ার ম্যাক্স ২৭০', 'nike-air-max-270', 'Max Air unit delivers unrivaled, all-day comfort.', NULL, 'NIKE-AM270', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Nike+Air+Max+270', '14999.00', '12749.15', '10499.30', '7499.50', 200, 10, 10, '0.34', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(72, 4, 9, 'Nike Dri-FIT T-Shirt', NULL, 'nike-dri-fit-t-shirt', 'Moisture-wicking fabric keeps you dry and comfortable.', NULL, 'NIKE-DRIFIT', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Nike+Dri-FIT+T-Shirt', '2999.00', '2549.15', '2099.30', '1499.50', 500, 10, 10, '0.15', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(73, 4, 9, 'Nike Running Shorts', NULL, 'nike-running-shorts', 'Lightweight woven shorts with built-in liner.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Nike+Running+Shorts', '3999.00', '3399.15', '2799.30', '1999.50', 350, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(74, 5, 9, 'Adidas Ultraboost Light', NULL, 'adidas-ultraboost-light', 'The lightest Ultraboost ever with Light BOOST midsole.', NULL, 'ADI-UBL', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Adidas+Ultraboost+Li...', '17999.00', '15299.15', '12599.30', '8999.50', 150, 10, 10, '0.28', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(75, 5, 9, 'Adidas Track Pants', NULL, 'adidas-track-pants', 'Classic 3-Stripes track pants with zip pockets.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Adidas+Track+Pants', '5999.00', '5099.15', '4199.30', '2999.50', 300, 10, 10, '0.30', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(76, 12, NULL, 'Philips Air Fryer XXL', NULL, 'philips-air-fryer-xxl', 'Rapid Air technology for fat-free frying with Starfish design.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Philips+Air+Fryer+XX...', '22999.00', '19549.15', '16099.30', '11499.50', 40, 10, 10, '7.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(77, NULL, NULL, 'Walton Refrigerator 265L', NULL, 'walton-refrigerator-265l', 'Frost-free double door refrigerator with inverter compressor.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Walton+Refrigerator...', '32999.00', '28049.15', '23099.30', '16499.50', 25, 10, 10, '55.00', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(78, NULL, NULL, 'Walton Washing Machine 10kg', NULL, 'walton-washing-machine-10kg', 'Fully automatic top-load with magic filter and diamond drum.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Walton+Washing+Machi...', '28999.00', '24649.15', '20299.30', '14499.50', 30, 10, 10, '40.00', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(79, 3, NULL, 'L\'Oreal Paris Revitalift Cream', NULL, 'loreal-paris-revitalift-cream', 'Anti-aging face cream with Pro-Retinol and Hyaluronic Acid.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=L%27Oreal+Paris+Revita...', '1999.00', '1699.15', '1399.30', '999.50', 200, 10, 10, '0.05', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(80, 3, NULL, 'Nivea Sun Protect SPF 50', NULL, 'nivea-sun-protect-spf-50', 'UV protection for face and body. Water-resistant formula.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Nivea+Sun+Protect+SP...', '1499.00', '1274.15', '1049.30', '749.50', 180, 10, 10, '0.08', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(81, 4, NULL, 'Nike Brasilia Backpack', NULL, 'nike-brasilia-backpack', 'Durable backpack with padded laptop sleeve and water bottle pocket.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Nike+Brasilia+Backpa...', '4999.00', '4249.15', '3499.30', '2499.50', 120, 10, 10, '0.50', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(82, 4, NULL, 'Yoga Mat Premium 6mm', NULL, 'yoga-mat-premium-6mm', 'Non-slip TPE material, eco-friendly, with carrying strap.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Yoga+Mat+Premium+6mm', '2499.00', '2124.15', '1749.30', '1249.50', 200, 10, 10, '1.20', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(83, 2, NULL, 'Apple Watch Series 9', NULL, 'apple-watch-series-9', 'S9 chip, Double Tap gesture, brighter display, carbon neutral.', NULL, 'APL-AW9', NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Apple+Watch+Series+9', '44999.00', '38249.15', '31499.30', '22499.50', 80, 10, 10, '0.05', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(84, 1, NULL, 'Samsung Galaxy Watch 6 Classic', NULL, 'samsung-galaxy-watch-6-classic', 'Rotating bezel, sapphire crystal, advanced health monitoring.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Samsung+Galaxy+Watch...', '34999.00', '29749.15', '24499.30', '17499.50', 60, 10, 10, '0.06', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(85, 6, 1, 'LG OLED C3 55\" 4K TV', NULL, 'lg-oled-c3-55-4k-tv', 'OLED evo panel, α9 Gen6 AI Processor, Dolby Vision & Atmos.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=LG+OLED+C3+55%22+4K+TV', '129999.00', '110499.15', '90999.30', '64999.50', 20, 10, 10, '18.00', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(86, 1, 1, 'Samsung 55\" QLED 4K TV', NULL, 'samsung-55-qled-4k-tv', 'Quantum Dot color, 100% Color Volume, Ambient Mode+.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Samsung+55%22+QLED+4K...', '69999.00', '59499.15', '48999.30', '34999.50', 25, 10, 10, '15.00', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(87, 3, 1, 'Sony Bravia XR 65\" OLED', NULL, 'sony-bravia-xr-65-oled', 'Cognitive Processor XR, Acoustic Surface Audio+, BRAVIA XR.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Sony+Bravia+XR+65%22+O...', '199999.00', '169999.15', '139999.30', '99999.50', 15, 10, 10, '22.00', NULL, NULL, NULL, 'standard', 'standard', 'published', 'simple', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(88, 3, NULL, 'Casio G-Shock GA-2100', NULL, 'casio-g-shock-ga-2100', 'Carbon Core Guard structure, 200m water resistance.', NULL, NULL, NULL, 'https://placehold.co/400x400/0d6efd/ffffff?text=Casio+G-Shock+GA-210...', '12999.00', '11049.15', '9099.30', '6499.50', 100, 10, 10, '0.05', NULL, NULL, NULL, 'standard', 'standard', 'published', 'variable', 0, 'public', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 19:39:08', '2026-08-19 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'select',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `name`, `slug`, `type`, `description`, `is_required`, `is_filterable`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Size', 'size', 'select', 'Product size variant', 1, 1, 1, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 'Color', 'color', 'color', 'Product color variant', 1, 1, 2, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, 'Material', 'material', 'select', 'Product material variant', 0, 1, 3, '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_attribute_values`
--

INSERT INTO `product_attribute_values` (`id`, `attribute_id`, `value`, `color_code`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'XS', NULL, 1, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(2, 1, 'S', NULL, 2, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(3, 1, 'M', NULL, 3, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(4, 1, 'L', NULL, 4, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(5, 1, 'XL', NULL, 5, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(6, 1, 'XXL', NULL, 6, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(7, 1, '3XL', NULL, 7, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(8, 2, 'Red', '#FF0000', 1, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(9, 2, 'Blue', '#0000FF', 2, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(10, 2, 'Green', '#00AA00', 3, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(11, 2, 'Black', '#000000', 4, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(12, 2, 'White', '#FFFFFF', 5, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(13, 2, 'Yellow', '#FFD700', 6, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(14, 2, 'Purple', '#800080', 7, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(15, 2, 'Orange', '#FFA500', 8, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(16, 2, 'Pink', '#FFC0CB', 9, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(17, 2, 'Gray', '#808080', 10, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(18, 3, 'Cotton', NULL, 1, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(19, 3, 'Polyester', NULL, 2, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(20, 3, 'Wool', NULL, 3, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(21, 3, 'Silk', NULL, 4, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(22, 3, 'Linen', NULL, 5, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(23, 3, 'Denim', NULL, 6, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(24, 3, 'Leather', NULL, 7, '2026-08-19 19:00:24', '2026-08-19 19:00:24'),
(25, 3, 'Nylon', NULL, 8, '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `product_galleries`
--

CREATE TABLE `product_galleries` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_galleries`
--

INSERT INTO `product_galleries` (`id`, `product_id`, `product_variant_id`, `image`, `alt_text`, `caption`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 53, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Samsung+Galaxy+S24+U...', 'Samsung Galaxy S24 Ultra image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(2, 53, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Samsung+Galaxy+S24+U...', 'Samsung Galaxy S24 Ultra image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(3, 53, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Samsung+Galaxy+S24+U...', 'Samsung Galaxy S24 Ultra image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(4, 54, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Samsung+Galaxy+S24%2B', 'Samsung Galaxy S24+ image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(5, 54, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Samsung+Galaxy+S24%2B', 'Samsung Galaxy S24+ image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(6, 54, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Samsung+Galaxy+S24%2B', 'Samsung Galaxy S24+ image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(7, 55, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Samsung+Galaxy+A54+5...', 'Samsung Galaxy A54 5G image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(8, 55, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Samsung+Galaxy+A54+5...', 'Samsung Galaxy A54 5G image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(9, 55, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Samsung+Galaxy+A54+5...', 'Samsung Galaxy A54 5G image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(10, 56, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+iPhone+15+Pro...', 'Apple iPhone 15 Pro Max image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(11, 56, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+iPhone+15+Pro...', 'Apple iPhone 15 Pro Max image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(12, 56, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+iPhone+15+Pro...', 'Apple iPhone 15 Pro Max image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(13, 57, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+iPhone+15', 'Apple iPhone 15 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(14, 57, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+iPhone+15', 'Apple iPhone 15 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(15, 57, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+iPhone+15', 'Apple iPhone 15 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(16, 58, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+iPhone+14', 'Apple iPhone 14 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(17, 58, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+iPhone+14', 'Apple iPhone 14 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(18, 58, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+iPhone+14', 'Apple iPhone 14 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(19, 59, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Xiaomi+14+Ultra', 'Xiaomi 14 Ultra image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(20, 59, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Xiaomi+14+Ultra', 'Xiaomi 14 Ultra image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(21, 59, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Xiaomi+14+Ultra', 'Xiaomi 14 Ultra image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(22, 60, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+MacBook+Pro+16...', 'Apple MacBook Pro 16\" M3 Max image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(23, 60, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+MacBook+Pro+16...', 'Apple MacBook Pro 16\" M3 Max image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(24, 60, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+MacBook+Pro+16...', 'Apple MacBook Pro 16\" M3 Max image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(25, 61, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+MacBook+Air+15...', 'Apple MacBook Air 15\" M3 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(26, 61, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+MacBook+Air+15...', 'Apple MacBook Air 15\" M3 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(27, 61, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+MacBook+Air+15...', 'Apple MacBook Air 15\" M3 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(28, 62, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Dell+XPS+15+%282024%29', 'Dell XPS 15 (2024) image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(29, 62, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Dell+XPS+15+%282024%29', 'Dell XPS 15 (2024) image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(30, 62, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Dell+XPS+15+%282024%29', 'Dell XPS 15 (2024) image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(31, 63, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Lenovo+ThinkPad+X1+C...', 'Lenovo ThinkPad X1 Carbon Gen 12 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(32, 63, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Lenovo+ThinkPad+X1+C...', 'Lenovo ThinkPad X1 Carbon Gen 12 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(33, 63, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Lenovo+ThinkPad+X1+C...', 'Lenovo ThinkPad X1 Carbon Gen 12 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(34, 64, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Asus+ROG+Zephyrus+G1...', 'Asus ROG Zephyrus G16 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(35, 64, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Asus+ROG+Zephyrus+G1...', 'Asus ROG Zephyrus G16 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(36, 64, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Asus+ROG+Zephyrus+G1...', 'Asus ROG Zephyrus G16 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(37, 65, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=HP+Pavilion+15', 'HP Pavilion 15 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(38, 65, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=HP+Pavilion+15', 'HP Pavilion 15 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(39, 65, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=HP+Pavilion+15', 'HP Pavilion 15 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(40, 66, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+AirPods+Pro+2...', 'Apple AirPods Pro 2 (USB-C) image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(41, 66, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+AirPods+Pro+2...', 'Apple AirPods Pro 2 (USB-C) image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(42, 66, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+AirPods+Pro+2...', 'Apple AirPods Pro 2 (USB-C) image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(43, 67, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Sony+WH-1000XM5', 'Sony WH-1000XM5 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(44, 67, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Sony+WH-1000XM5', 'Sony WH-1000XM5 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(45, 67, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Sony+WH-1000XM5', 'Sony WH-1000XM5 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(46, 68, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Bose+QuietComfort+Ul...', 'Bose QuietComfort Ultra Headphones image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(47, 68, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Bose+QuietComfort+Ul...', 'Bose QuietComfort Ultra Headphones image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(48, 68, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Bose+QuietComfort+Ul...', 'Bose QuietComfort Ultra Headphones image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(49, 69, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=JBL+Charge+5', 'JBL Charge 5 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(50, 69, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=JBL+Charge+5', 'JBL Charge 5 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(51, 69, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=JBL+Charge+5', 'JBL Charge 5 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(52, 70, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Sony+PlayStation+5+S...', 'Sony PlayStation 5 Slim image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(53, 70, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Sony+PlayStation+5+S...', 'Sony PlayStation 5 Slim image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(54, 70, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Sony+PlayStation+5+S...', 'Sony PlayStation 5 Slim image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(55, 71, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Nike+Air+Max+270', 'Nike Air Max 270 image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(56, 71, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Nike+Air+Max+270', 'Nike Air Max 270 image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(57, 71, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Nike+Air+Max+270', 'Nike Air Max 270 image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(58, 72, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Nike+Dri-FIT+T-Shirt', 'Nike Dri-FIT T-Shirt image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(59, 72, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Nike+Dri-FIT+T-Shirt', 'Nike Dri-FIT T-Shirt image 2', NULL, 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(60, 72, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Nike+Dri-FIT+T-Shirt', 'Nike Dri-FIT T-Shirt image 3', NULL, 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(61, 73, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Nike+Running+Shorts', 'Nike Running Shorts image 1', NULL, 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(62, 73, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Nike+Running+Shorts', 'Nike Running Shorts image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(63, 73, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Nike+Running+Shorts', 'Nike Running Shorts image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(64, 74, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Adidas+Ultraboost+Li...', 'Adidas Ultraboost Light image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(65, 74, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Adidas+Ultraboost+Li...', 'Adidas Ultraboost Light image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(66, 74, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Adidas+Ultraboost+Li...', 'Adidas Ultraboost Light image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(67, 75, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Adidas+Track+Pants', 'Adidas Track Pants image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(68, 75, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Adidas+Track+Pants', 'Adidas Track Pants image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(69, 75, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Adidas+Track+Pants', 'Adidas Track Pants image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(70, 76, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Philips+Air+Fryer+XX...', 'Philips Air Fryer XXL image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(71, 76, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Philips+Air+Fryer+XX...', 'Philips Air Fryer XXL image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(72, 76, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Philips+Air+Fryer+XX...', 'Philips Air Fryer XXL image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(73, 77, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Walton+Refrigerator...', 'Walton Refrigerator 265L image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(74, 77, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Walton+Refrigerator...', 'Walton Refrigerator 265L image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(75, 77, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Walton+Refrigerator...', 'Walton Refrigerator 265L image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(76, 78, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Walton+Washing+Machi...', 'Walton Washing Machine 10kg image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(77, 78, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Walton+Washing+Machi...', 'Walton Washing Machine 10kg image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(78, 78, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Walton+Washing+Machi...', 'Walton Washing Machine 10kg image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(79, 79, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=L%27Oreal+Paris+Revita...', 'L\'Oreal Paris Revitalift Cream image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(80, 79, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=L%27Oreal+Paris+Revita...', 'L\'Oreal Paris Revitalift Cream image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(81, 79, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=L%27Oreal+Paris+Revita...', 'L\'Oreal Paris Revitalift Cream image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(82, 80, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Nivea+Sun+Protect+SP...', 'Nivea Sun Protect SPF 50 image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(83, 80, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Nivea+Sun+Protect+SP...', 'Nivea Sun Protect SPF 50 image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(84, 80, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Nivea+Sun+Protect+SP...', 'Nivea Sun Protect SPF 50 image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(85, 81, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Nike+Brasilia+Backpa...', 'Nike Brasilia Backpack image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(86, 81, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Nike+Brasilia+Backpa...', 'Nike Brasilia Backpack image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(87, 81, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Nike+Brasilia+Backpa...', 'Nike Brasilia Backpack image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(88, 82, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Yoga+Mat+Premium+6mm', 'Yoga Mat Premium 6mm image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(89, 82, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Yoga+Mat+Premium+6mm', 'Yoga Mat Premium 6mm image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(90, 82, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Yoga+Mat+Premium+6mm', 'Yoga Mat Premium 6mm image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(91, 83, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Apple+Watch+Series+9', 'Apple Watch Series 9 image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(92, 83, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Apple+Watch+Series+9', 'Apple Watch Series 9 image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(93, 83, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Apple+Watch+Series+9', 'Apple Watch Series 9 image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(94, 84, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Samsung+Galaxy+Watch...', 'Samsung Galaxy Watch 6 Classic image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(95, 84, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Samsung+Galaxy+Watch...', 'Samsung Galaxy Watch 6 Classic image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(96, 84, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Samsung+Galaxy+Watch...', 'Samsung Galaxy Watch 6 Classic image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(97, 85, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=LG+OLED+C3+55%22+4K+TV', 'LG OLED C3 55\" 4K TV image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(98, 85, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=LG+OLED+C3+55%22+4K+TV', 'LG OLED C3 55\" 4K TV image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(99, 85, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=LG+OLED+C3+55%22+4K+TV', 'LG OLED C3 55\" 4K TV image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(100, 86, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Samsung+55%22+QLED+4K...', 'Samsung 55\" QLED 4K TV image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(101, 86, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Samsung+55%22+QLED+4K...', 'Samsung 55\" QLED 4K TV image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(102, 86, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Samsung+55%22+QLED+4K...', 'Samsung 55\" QLED 4K TV image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(103, 87, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Sony+Bravia+XR+65%22+O...', 'Sony Bravia XR 65\" OLED image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(104, 87, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Sony+Bravia+XR+65%22+O...', 'Sony Bravia XR 65\" OLED image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(105, 87, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Sony+Bravia+XR+65%22+O...', 'Sony Bravia XR 65\" OLED image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(106, 88, NULL, 'https://placehold.co/600x600/f8f9fa/ffffff?text=Casio+G-Shock+GA-210...', 'Casio G-Shock GA-2100 image 1', NULL, 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(107, 88, NULL, 'https://placehold.co/600x600/e9ecef/ffffff?text=Casio+G-Shock+GA-210...', 'Casio G-Shock GA-2100 image 2', NULL, 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(108, 88, NULL, 'https://placehold.co/600x600/dee2e6/ffffff?text=Casio+G-Shock+GA-210...', 'Casio G-Shock GA-2100 image 3', NULL, 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `attributes` json DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `name`, `sku`, `barcode`, `price`, `stock`, `attributes`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 53, '256GB Titanium Black', 'SAM-S24U-256-256gb-titanium-black', NULL, '139999.00', 20, '{\"Color\": \"Titanium Black\", \"Storage\": \"256GB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(2, 53, '256GB Titanium Gray', 'SAM-S24U-256-256gb-titanium-gray', NULL, '139999.00', 15, '{\"Color\": \"Titanium Gray\", \"Storage\": \"256GB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(3, 53, '512GB Titanium Black', 'SAM-S24U-256-512gb-titanium-black', NULL, '159999.00', 10, '{\"Color\": \"Titanium Black\", \"Storage\": \"512GB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(4, 53, '1TB Titanium Violet', 'SAM-S24U-256-1tb-titanium-violet', NULL, '189999.00', 5, '{\"Color\": \"Titanium Violet\", \"Storage\": \"1TB\"}', 3, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(5, 54, '256GB Onyx Black', 'SAM-S24P-256gb-onyx-black', NULL, '99999.00', 30, '{\"Color\": \"Onyx Black\", \"Storage\": \"256GB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(6, 54, '256GB Cobalt Violet', 'SAM-S24P-256gb-cobalt-violet', NULL, '99999.00', 25, '{\"Color\": \"Cobalt Violet\", \"Storage\": \"256GB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(7, 54, '512GB Amber Yellow', 'SAM-S24P-512gb-amber-yellow', NULL, '114999.00', 15, '{\"Color\": \"Amber Yellow\", \"Storage\": \"512GB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(8, 56, '256GB Natural Titanium', 'APL-15PM-256gb-natural-titanium', NULL, '159999.00', 12, '{\"Color\": \"Natural Titanium\", \"Storage\": \"256GB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(9, 56, '256GB Blue Titanium', 'APL-15PM-256gb-blue-titanium', NULL, '159999.00', 10, '{\"Color\": \"Blue Titanium\", \"Storage\": \"256GB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(10, 56, '512GB Black Titanium', 'APL-15PM-512gb-black-titanium', NULL, '189999.00', 8, '{\"Color\": \"Black Titanium\", \"Storage\": \"512GB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(11, 56, '1TB White Titanium', 'APL-15PM-1tb-white-titanium', NULL, '219999.00', 5, '{\"Color\": \"White Titanium\", \"Storage\": \"1TB\"}', 3, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(12, 57, '128GB Black', 'apple-iphone-15-128gb-black', NULL, '109999.00', 20, '{\"Color\": \"Black\", \"Storage\": \"128GB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(13, 57, '128GB Blue', 'apple-iphone-15-128gb-blue', NULL, '109999.00', 15, '{\"Color\": \"Blue\", \"Storage\": \"128GB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(14, 57, '256GB Green', 'apple-iphone-15-256gb-green', NULL, '124999.00', 10, '{\"Color\": \"Green\", \"Storage\": \"256GB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(15, 60, '36GB / 1TB Space Black', 'APL-MBP16-M3-36gb-1tb-space-black', NULL, '319999.00', 8, '{\"RAM\": \"36GB\", \"Color\": \"Space Black\", \"Storage\": \"1TB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(16, 60, '48GB / 1TB Silver', 'APL-MBP16-M3-48gb-1tb-silver', NULL, '379999.00', 5, '{\"RAM\": \"48GB\", \"Color\": \"Silver\", \"Storage\": \"1TB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(17, 60, '128GB / 2TB Space Black', 'APL-MBP16-M3-128gb-2tb-space-black', NULL, '519999.00', 3, '{\"RAM\": \"128GB\", \"Color\": \"Space Black\", \"Storage\": \"2TB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(18, 62, 'i7 / 16GB / 512GB', 'dell-xps-15-2024-i7-16gb-512gb', NULL, '189999.00', 8, '{\"RAM\": \"16GB\", \"Storage\": \"512GB\", \"Processor\": \"Core Ultra 7\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(19, 62, 'i9 / 32GB / 1TB', 'dell-xps-15-2024-i9-32gb-1tb', NULL, '249999.00', 5, '{\"RAM\": \"32GB\", \"Storage\": \"1TB\", \"Processor\": \"Core Ultra 9\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(20, 64, 'RTX 4060 / 16GB', 'asus-rog-zephyrus-g16-rtx-4060-16gb', NULL, '179999.00', 10, '{\"GPU\": \"RTX 4060\", \"RAM\": \"16GB\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(21, 64, 'RTX 4070 / 32GB', 'asus-rog-zephyrus-g16-rtx-4070-32gb', NULL, '209999.00', 8, '{\"GPU\": \"RTX 4070\", \"RAM\": \"32GB\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(22, 64, 'RTX 4080 / 32GB', 'asus-rog-zephyrus-g16-rtx-4080-32gb', NULL, '279999.00', 4, '{\"GPU\": \"RTX 4080\", \"RAM\": \"32GB\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(23, 67, 'Black', 'sony-wh-1000xm5-black', NULL, '37999.00', 40, '{\"Color\": \"Black\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(24, 67, 'Silver', 'sony-wh-1000xm5-silver', NULL, '37999.00', 30, '{\"Color\": \"Silver\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(25, 67, 'Midnight Blue', 'sony-wh-1000xm5-midnight-blue', NULL, '39999.00', 15, '{\"Color\": \"Midnight Blue\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(26, 69, 'Black', 'jbl-charge-5-black', NULL, '16999.00', 50, '{\"Color\": \"Black\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(27, 69, 'Blue', 'jbl-charge-5-blue', NULL, '16999.00', 40, '{\"Color\": \"Blue\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(28, 69, 'Red', 'jbl-charge-5-red', NULL, '16999.00', 30, '{\"Color\": \"Red\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(29, 69, 'Teal', 'jbl-charge-5-teal', NULL, '16999.00', 20, '{\"Color\": \"Teal\"}', 3, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(30, 70, 'Digital Edition', 'sony-playstation-5-slim-digital-edition', NULL, '47999.00', 25, '{\"Edition\": \"Digital\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(31, 70, 'Disc Edition', 'sony-playstation-5-slim-disc-edition', NULL, '54999.00', 25, '{\"Edition\": \"Disc\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(32, 71, 'Black/White - 40', 'NIKE-AM270-blackwhite-40', NULL, '14999.00', 20, '{\"Size\": \"40\", \"Color\": \"Black/White\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(33, 71, 'Black/White - 42', 'NIKE-AM270-blackwhite-42', NULL, '14999.00', 25, '{\"Size\": \"42\", \"Color\": \"Black/White\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(34, 71, 'Black/White - 44', 'NIKE-AM270-blackwhite-44', NULL, '14999.00', 15, '{\"Size\": \"44\", \"Color\": \"Black/White\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(35, 71, 'White/Blue - 42', 'NIKE-AM270-whiteblue-42', NULL, '14999.00', 20, '{\"Size\": \"42\", \"Color\": \"White/Blue\"}', 3, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(36, 71, 'Red/Black - 42', 'NIKE-AM270-redblack-42', NULL, '14999.00', 15, '{\"Size\": \"42\", \"Color\": \"Red/Black\"}', 4, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(37, 72, 'Black / S', 'NIKE-DRIFIT-black-s', NULL, '2999.00', 40, '{\"Size\": \"S\", \"Color\": \"Black\"}', 0, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(38, 72, 'Black / M', 'NIKE-DRIFIT-black-m', NULL, '2999.00', 50, '{\"Size\": \"M\", \"Color\": \"Black\"}', 1, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(39, 72, 'Black / L', 'NIKE-DRIFIT-black-l', NULL, '2999.00', 45, '{\"Size\": \"L\", \"Color\": \"Black\"}', 2, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(40, 72, 'Black / XL', 'NIKE-DRIFIT-black-xl', NULL, '2999.00', 30, '{\"Size\": \"XL\", \"Color\": \"Black\"}', 3, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(41, 72, 'White / M', 'NIKE-DRIFIT-white-m', NULL, '2999.00', 40, '{\"Size\": \"M\", \"Color\": \"White\"}', 4, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(42, 72, 'Navy / L', 'NIKE-DRIFIT-navy-l', NULL, '2999.00', 35, '{\"Size\": \"L\", \"Color\": \"Navy\"}', 5, '2026-08-19 19:39:07', '2026-08-19 19:39:07'),
(43, 74, 'Core Black / 42', 'ADI-UBL-core-black-42', NULL, '17999.00', 25, '{\"Size\": \"42\", \"Color\": \"Core Black\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(44, 74, 'Core Black / 44', 'ADI-UBL-core-black-44', NULL, '17999.00', 20, '{\"Size\": \"44\", \"Color\": \"Core Black\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(45, 74, 'White / 42', 'ADI-UBL-white-42', NULL, '17999.00', 15, '{\"Size\": \"42\", \"Color\": \"White\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(46, 75, 'Black / S', 'adidas-track-pants-black-s', NULL, '5999.00', 30, '{\"Size\": \"S\", \"Color\": \"Black\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(47, 75, 'Black / M', 'adidas-track-pants-black-m', NULL, '5999.00', 40, '{\"Size\": \"M\", \"Color\": \"Black\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(48, 75, 'Black / L', 'adidas-track-pants-black-l', NULL, '5999.00', 35, '{\"Size\": \"L\", \"Color\": \"Black\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(49, 75, 'Navy / M', 'adidas-track-pants-navy-m', NULL, '5999.00', 25, '{\"Size\": \"M\", \"Color\": \"Navy\"}', 3, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(50, 81, 'Black - Small (20L)', 'nike-brasilia-backpack-black-small-20l', NULL, '4999.00', 30, '{\"Size\": \"20L\", \"Color\": \"Black\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(51, 81, 'Black - Medium (26L)', 'nike-brasilia-backpack-black-medium-26l', NULL, '5999.00', 25, '{\"Size\": \"26L\", \"Color\": \"Black\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(52, 81, 'Navy - Medium (26L)', 'nike-brasilia-backpack-navy-medium-26l', NULL, '5999.00', 20, '{\"Size\": \"26L\", \"Color\": \"Navy\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(53, 82, 'Purple', 'yoga-mat-premium-6mm-purple', NULL, '2499.00', 40, '{\"Color\": \"Purple\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(54, 82, 'Teal', 'yoga-mat-premium-6mm-teal', NULL, '2499.00', 35, '{\"Color\": \"Teal\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(55, 82, 'Pink', 'yoga-mat-premium-6mm-pink', NULL, '2499.00', 30, '{\"Color\": \"Pink\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(56, 83, '41mm GPS Midnight', 'APL-AW9-41mm-gps-midnight', NULL, '44999.00', 20, '{\"Size\": \"41mm\", \"Type\": \"GPS\", \"Color\": \"Midnight\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(57, 83, '45mm GPS Midnight', 'APL-AW9-45mm-gps-midnight', NULL, '49999.00', 15, '{\"Size\": \"45mm\", \"Type\": \"GPS\", \"Color\": \"Midnight\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(58, 83, '45mm GPS+Cellular Starlight', 'APL-AW9-45mm-gpscellular-starlight', NULL, '59999.00', 10, '{\"Size\": \"45mm\", \"Type\": \"GPS+Cellular\", \"Color\": \"Starlight\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(59, 84, '47mm Bluetooth Black', 'samsung-galaxy-watch-6-classic-47mm-bluetooth-black', NULL, '34999.00', 20, '{\"Size\": \"47mm\", \"Color\": \"Black\", \"Connectivity\": \"Bluetooth\"}', 0, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(60, 84, '47mm LTE Black', 'samsung-galaxy-watch-6-classic-47mm-lte-black', NULL, '39999.00', 15, '{\"Size\": \"47mm\", \"Color\": \"Black\", \"Connectivity\": \"LTE\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(61, 88, 'All Black', 'casio-g-shock-ga-2100-all-black', NULL, '12999.00', 35, '{\"Color\": \"All Black\"}', 0, '2026-08-19 19:39:08', '2026-08-19 20:01:23'),
(62, 88, 'Navy Blue', 'casio-g-shock-ga-2100-navy-blue', NULL, '12999.00', 25, '{\"Color\": \"Navy Blue\"}', 1, '2026-08-19 19:39:08', '2026-08-19 19:39:08'),
(63, 88, 'Green', 'casio-g-shock-ga-2100-green', NULL, '12999.00', 20, '{\"Color\": \"Green\"}', 2, '2026-08-19 19:39:08', '2026-08-19 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-08-19 19:00:53', '2026-08-19 19:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `key`, `value`, `group`, `label`, `type`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'MyVoucher', 'general', 'Site Name', 'text', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(2, 'site_description', 'Your trusted online shopping destination for quality products at great prices in Bangladesh.', 'general', 'Site Description', 'textarea', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(3, 'contact_phone', '+880 1700-123456', 'contact', 'Contact Phone', 'text', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(4, 'contact_email', 'support@myvoucher.com', 'contact', 'Contact Email', 'text', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(5, 'contact_address', 'Gulshan-2, Dhaka 1212, Bangladesh', 'contact', 'Contact Address', 'text', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(6, 'footer_quick_links', '[{\"label\":\"About Us\",\"url\":\"\\/page\\/about-us\"},{\"label\":\"Contact\",\"url\":\"\\/contact\"},{\"label\":\"Products\",\"url\":\"\\/products\"},{\"label\":\"Categories\",\"url\":\"\\/categories\"}]', 'footer', 'Footer Quick Links', 'json', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(7, 'footer_customer_service_links', '[{\"label\":\"FAQ\",\"url\":\"\\/page\\/faq\"},{\"label\":\"Shipping Policy\",\"url\":\"\\/page\\/shipping-policy\"},{\"label\":\"Return Policy\",\"url\":\"\\/page\\/return-policy\"},{\"label\":\"Privacy Policy\",\"url\":\"\\/page\\/privacy-policy\"},{\"label\":\"Terms & Conditions\",\"url\":\"\\/page\\/terms\"}]', 'footer', 'Footer Customer Service Links', 'json', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(8, 'trust_features', '[{\"icon\":\"\\ud83d\\ude9a\",\"title\":\"Free Shipping\",\"description\":\"Free delivery on orders over \\u09f35,000\",\"color\":\"emerald\"},{\"icon\":\"\\ud83d\\udd12\",\"title\":\"Secure Payment\",\"description\":\"100% secure checkout with SSL encryption\",\"color\":\"blue\"},{\"icon\":\"\\u21a9\\ufe0f\",\"title\":\"Easy Returns\",\"description\":\"7-day hassle-free return policy\",\"color\":\"amber\"},{\"icon\":\"\\ud83d\\udcac\",\"title\":\"24\\/7 Support\",\"description\":\"Round-the-clock customer support via phone & chat\",\"color\":\"violet\"}]', 'content', 'Trust Features', 'json', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(9, 'trusted_brands', '[\"Walton\",\"Samsung\",\"Apple\",\"Xiaomi\",\"Sony\",\"Philips\"]', 'content', 'Trusted Brands', 'json', '2026-08-19 18:58:40', '2026-08-19 19:04:28'),
(10, 'logo', '', 'branding', 'Site Logo', 'image', '2026-08-19 18:58:40', '2026-08-19 18:58:40'),
(11, 'favicon', '', 'branding', 'Favicon', 'image', '2026-08-19 18:58:40', '2026-08-19 18:58:40'),
(12, 'og_image', '', 'branding', 'OG Image (Social Share)', 'image', '2026-08-19 18:58:40', '2026-08-19 18:58:40'),
(17, 'tax_rate', '0', 'checkout', 'Tax Rate (%)', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(18, 'free_shipping_threshold', '5000', 'checkout', 'Free Shipping Threshold ($)', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(19, 'shipping_rate', '100', 'checkout', 'Standard Shipping Rate ($)', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(20, 'currency_symbol', '৳', 'currency', 'Currency Symbol', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(21, 'currency_code', 'BDT', 'currency', 'Currency Code', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(22, 'currency_position', 'before', 'currency', 'Currency Position', 'text', '2026-08-19 18:58:41', '2026-08-19 18:58:41'),
(23, 'currency_decimals', '0', 'currency', 'Decimal Places', 'text', '2026-08-19 18:58:41', '2026-08-19 19:04:28'),
(24, 'currency_thousand_separator', ',', 'currency', 'Thousand Separator', 'text', '2026-08-19 18:58:41', '2026-08-19 18:58:41'),
(25, 'currency_decimal_separator', '.', 'currency', 'Decimal Separator', 'text', '2026-08-19 18:58:41', '2026-08-19 18:58:41'),
(26, 'active_theme', 'modern', 'appearance', 'Homepage Theme', 'select', '2026-08-19 18:58:41', '2026-08-19 19:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `stock_audits`
--

CREATE TABLE `stock_audits` (
  `id` bigint UNSIGNED NOT NULL,
  `audit_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `system_stock` int NOT NULL DEFAULT '0',
  `physical_stock` int NOT NULL DEFAULT '0',
  `difference` int NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `audited_by` bigint UNSIGNED DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `audited_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_reservations`
--

CREATE TABLE `stock_reservations` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `reserved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `released_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `transfer_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_warehouse_id` bigint UNSIGNED NOT NULL,
  `to_warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transferred_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `requested_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `avatar`, `bio`, `date_of_birth`, `gender`, `timezone`, `locale`, `email_verified_at`, `password`, `remember_token`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Shahadat Hossain', 'shahadat@asiancoder.com', NULL, NULL, NULL, NULL, NULL, NULL, 'UTC', 'en', '2026-08-19 19:00:23', '$2y$12$/rthXYaRRwcRa0KKY5bRM.4u5rK9QQMPfg2RU/gwLzcqR7Wf7A5.y', '0ZOuS04eK4', 'staff', '2026-08-19 19:00:24', '2026-08-19 19:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `address_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `locked_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','frozen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('credit','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('commission','withdrawal','refund','bonus','purchase','admin_adjustment','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `code`, `address`, `city`, `state`, `country`, `zip_code`, `phone`, `email`, `manager_name`, `is_default`, `status`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Rajshahi', 'Raj', NULL, 'Rajshahi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'active', NULL, 1, '2026-08-19 19:35:18', '2026-08-19 19:35:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_product_category_id_product_id_unique` (`category_id`,`product_id`),
  ADD KEY `category_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_created_by_foreign` (`created_by`),
  ADD KEY `coupons_updated_by_foreign` (`updated_by`),
  ADD KEY `coupons_status_valid_from_valid_until_index` (`status`,`valid_from`,`valid_until`),
  ADD KEY `coupons_type_index` (`type`),
  ADD KEY `coupons_priority_index` (`priority`);

--
-- Indexes for table `coupon_categories`
--
ALTER TABLE `coupon_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_categories_coupon_id_category_id_is_excluded_unique` (`coupon_id`,`category_id`,`is_excluded`),
  ADD KEY `coupon_categories_category_id_index` (`category_id`);

--
-- Indexes for table `coupon_customers`
--
ALTER TABLE `coupon_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_customers_coupon_id_user_id_unique` (`coupon_id`,`user_id`),
  ADD KEY `coupon_customers_user_id_index` (`user_id`);

--
-- Indexes for table `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_products_coupon_id_product_id_is_excluded_unique` (`coupon_id`,`product_id`,`is_excluded`),
  ADD KEY `coupon_products_product_id_index` (`product_id`);

--
-- Indexes for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_usages_coupon_id_index` (`coupon_id`),
  ADD KEY `coupon_usages_user_id_index` (`user_id`),
  ADD KEY `coupon_usages_order_id_index` (`order_id`),
  ADD KEY `coupon_usages_created_at_index` (`created_at`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD KEY `customers_email_index` (`email`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_profiles_customer_code_unique` (`customer_code`),
  ADD KEY `customer_profiles_user_id_foreign` (`user_id`),
  ADD KEY `customer_profiles_status_index` (`status`),
  ADD KEY `customer_profiles_customer_code_index` (`customer_code`),
  ADD KEY `customer_profiles_customer_id_index` (`customer_id`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_zones_type_unique` (`type`);

--
-- Indexes for table `delivery_zone_districts`
--
ALTER TABLE `delivery_zone_districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_zone_districts_name_unique` (`name`),
  ADD KEY `delivery_zone_districts_delivery_zone_id_index` (`delivery_zone_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_unique` (`product_id`,`warehouse_id`,`product_variant_id`),
  ADD KEY `inventory_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `inventory_warehouse_id_index` (`warehouse_id`),
  ADD KEY `inventory_available_stock_index` (`available_stock`),
  ADD KEY `inventory_sku_index` (`sku`),
  ADD KEY `inventory_barcode_index` (`barcode`);

--
-- Indexes for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_adjustments_inventory_id_foreign` (`inventory_id`),
  ADD KEY `inventory_adjustments_product_id_foreign` (`product_id`),
  ADD KEY `inventory_adjustments_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `inventory_adjustments_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `inventory_adjustments_user_id_foreign` (`user_id`),
  ADD KEY `inventory_adjustments_approved_by_foreign` (`approved_by`),
  ADD KEY `inventory_adjustments_type_index` (`type`),
  ADD KEY `inventory_adjustments_status_index` (`status`),
  ADD KEY `inventory_adjustments_created_at_index` (`created_at`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_transactions_inventory_id_foreign` (`inventory_id`),
  ADD KEY `inventory_transactions_product_id_foreign` (`product_id`),
  ADD KEY `inventory_transactions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `inventory_transactions_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `inventory_transactions_user_id_foreign` (`user_id`),
  ADD KEY `inventory_transactions_type_index` (`type`),
  ADD KEY `inventory_transactions_reference_type_index` (`reference_type`),
  ADD KEY `inventory_transactions_reference_id_index` (`reference_id`),
  ADD KEY `inventory_transactions_created_at_index` (`created_at`),
  ADD KEY `inventory_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_type_index` (`type`),
  ADD KEY `media_sort_order_index` (`sort_order`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_order_number_index` (`order_number`),
  ADD KEY `orders_created_at_index` (`created_at`),
  ADD KEY `orders_status_index` (`status`),
  ADD KEY `orders_payment_status_index` (`payment_status`),
  ADD KEY `orders_shipping_status_index` (`shipping_status`),
  ADD KEY `orders_guest_email_index` (`guest_email`),
  ADD KEY `orders_customer_id_index` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_product_id_index` (`product_id`),
  ADD KEY `order_items_order_id_index` (`order_id`);

--
-- Indexes for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_histories_order_id_index` (`order_id`),
  ADD KEY `order_status_histories_to_status_index` (`to_status`),
  ADD KEY `order_status_histories_created_at_index` (`created_at`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_attributes_slug_unique` (`slug`);

--
-- Indexes for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`);

--
-- Indexes for table `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_galleries_product_id_foreign` (`product_id`),
  ADD KEY `product_galleries_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_product_id_sku_unique` (`product_id`,`sku`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_settings_key_unique` (`key`);

--
-- Indexes for table `stock_audits`
--
ALTER TABLE `stock_audits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_audits_audit_number_unique` (`audit_number`),
  ADD KEY `stock_audits_product_id_foreign` (`product_id`),
  ADD KEY `stock_audits_audited_by_foreign` (`audited_by`),
  ADD KEY `stock_audits_verified_by_foreign` (`verified_by`),
  ADD KEY `stock_audits_warehouse_id_index` (`warehouse_id`),
  ADD KEY `stock_audits_status_index` (`status`);

--
-- Indexes for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_reservations_inventory_id_foreign` (`inventory_id`),
  ADD KEY `stock_reservations_product_id_foreign` (`product_id`),
  ADD KEY `stock_reservations_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_reservations_user_id_foreign` (`user_id`),
  ADD KEY `stock_reservations_order_id_index` (`order_id`),
  ADD KEY `stock_reservations_status_index` (`status`),
  ADD KEY `stock_reservations_order_id_status_index` (`order_id`,`status`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_transfers_transfer_number_unique` (`transfer_number`),
  ADD KEY `stock_transfers_product_id_foreign` (`product_id`),
  ADD KEY `stock_transfers_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `stock_transfers_requested_by_foreign` (`requested_by`),
  ADD KEY `stock_transfers_approved_by_foreign` (`approved_by`),
  ADD KEY `stock_transfers_received_by_foreign` (`received_by`),
  ADD KEY `stock_transfers_from_warehouse_id_index` (`from_warehouse_id`),
  ADD KEY `stock_transfers_to_warehouse_id_index` (`to_warehouse_id`),
  ADD KEY `stock_transfers_status_index` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_type_index` (`type`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `user_addresses_address_type_index` (`address_type`),
  ADD KEY `user_addresses_customer_id_index` (`customer_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_user_id_foreign` (`user_id`),
  ADD KEY `wallets_status_index` (`status`),
  ADD KEY `wallets_customer_id_index` (`customer_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_transactions_transaction_code_unique` (`transaction_code`),
  ADD KEY `wallet_transactions_user_id_foreign` (`user_id`),
  ADD KEY `wallet_transactions_created_by_foreign` (`created_by`),
  ADD KEY `wallet_transactions_wallet_id_created_at_index` (`wallet_id`,`created_at`),
  ADD KEY `wallet_transactions_type_category_index` (`type`,`category`),
  ADD KEY `wallet_transactions_type_index` (`type`),
  ADD KEY `wallet_transactions_category_index` (`category`),
  ADD KEY `wallet_transactions_customer_id_index` (`customer_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouses_code_unique` (`code`),
  ADD KEY `warehouses_created_by_foreign` (`created_by`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlist_unique` (`customer_id`,`product_id`,`product_variant_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`),
  ADD KEY `wishlists_product_variant_id_foreign` (`product_variant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `category_product`
--
ALTER TABLE `category_product`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_categories`
--
ALTER TABLE `coupon_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_customers`
--
ALTER TABLE `coupon_customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_products`
--
ALTER TABLE `coupon_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_zone_districts`
--
ALTER TABLE `delivery_zone_districts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product_galleries`
--
ALTER TABLE `product_galleries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `stock_audits`
--
ALTER TABLE `stock_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coupons_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_categories`
--
ALTER TABLE `coupon_categories`
  ADD CONSTRAINT `coupon_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_categories_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_customers`
--
ALTER TABLE `coupon_customers`
  ADD CONSTRAINT `coupon_customers_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD CONSTRAINT `coupon_products_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coupon_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD CONSTRAINT `customer_profiles_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `delivery_zone_districts`
--
ALTER TABLE `delivery_zone_districts`
  ADD CONSTRAINT `delivery_zone_districts_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD CONSTRAINT `inventory_adjustments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_adjustments_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_adjustments_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_transactions_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
  ADD CONSTRAINT `order_status_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `product_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD CONSTRAINT `product_galleries_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_galleries_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_audits`
--
ALTER TABLE `stock_audits`
  ADD CONSTRAINT `stock_audits_audited_by_foreign` FOREIGN KEY (`audited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_audits_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_audits_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_audits_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  ADD CONSTRAINT `stock_reservations_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_reservations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_reservations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_reservations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
