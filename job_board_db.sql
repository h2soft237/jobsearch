-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 15 déc. 2025 à 17:35
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `job_board_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `alertes_emploi`
--

CREATE TABLE `alertes_emploi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mots_cles` varchar(255) NOT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `type_contrat` varchar(50) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `alertes_emploi`
--

INSERT INTO `alertes_emploi` (`id`, `user_id`, `mots_cles`, `lieu`, `type_contrat`, `date_creation`) VALUES
(1, 44, 'Marketing', 'Paris', 'CDI', '2025-12-15 17:34:42'),
(2, 49, 'Commercial', NULL, 'CDI', '2025-12-15 17:34:42');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `candidat_id` int(11) NOT NULL,
  `offre_id` int(11) NOT NULL,
  `cv_chemin` varchar(255) NOT NULL,
  `lettre_motivation` text DEFAULT NULL,
  `statut` enum('en attente','examiné','accepté','refusé') NOT NULL DEFAULT 'en attente',
  `date_candidature` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `candidat_id`, `offre_id`, `cv_chemin`, `lettre_motivation`, `statut`, `date_candidature`) VALUES
(1, 45, 1, '', NULL, '', '2025-11-10 19:24:05'),
(2, 46, 1, '', NULL, '', '2025-12-09 11:39:12'),
(3, 49, 1, '', NULL, '', '2025-10-04 17:05:58'),
(4, 45, 2, '', NULL, '', '2025-11-05 11:56:14'),
(5, 49, 2, '', NULL, '', '2025-12-04 08:22:07'),
(6, 42, 2, '', NULL, 'en attente', '2025-10-09 07:41:27'),
(7, 42, 3, '', NULL, '', '2025-12-07 06:21:38'),
(8, 49, 3, '', NULL, '', '2025-12-02 23:09:05'),
(9, 46, 3, '', NULL, '', '2025-12-01 21:08:34'),
(10, 43, 4, '', NULL, '', '2025-11-20 16:15:17'),
(11, 46, 4, '', NULL, 'en attente', '2025-11-24 04:23:55'),
(12, 47, 4, '', NULL, 'en attente', '2025-12-11 23:40:30'),
(13, 41, 5, '', NULL, '', '2025-11-12 21:51:08'),
(14, 42, 5, '', NULL, '', '2025-12-08 12:27:52'),
(15, 47, 5, '', NULL, '', '2025-09-21 12:19:08'),
(16, 50, 6, '', NULL, '', '2025-09-11 11:19:54'),
(17, 47, 6, '', NULL, '', '2025-08-27 03:51:00'),
(18, 49, 6, '', NULL, '', '2025-08-08 16:23:20'),
(19, 48, 7, '', NULL, '', '2025-12-15 05:02:46'),
(20, 49, 7, '', NULL, 'en attente', '2025-11-12 03:01:06'),
(21, 45, 7, '', NULL, 'en attente', '2025-09-06 19:51:04'),
(22, 45, 8, '', NULL, 'en attente', '2025-11-07 02:48:36'),
(23, 43, 8, '', NULL, '', '2025-10-14 03:05:57'),
(24, 44, 8, '', NULL, '', '2025-11-06 12:41:21'),
(25, 45, 9, '', NULL, '', '2025-12-01 01:20:15'),
(26, 48, 9, '', NULL, '', '2025-12-06 17:44:40'),
(27, 46, 9, '', NULL, '', '2025-11-17 00:27:55'),
(28, 49, 10, '', NULL, '', '2025-11-23 16:17:51'),
(29, 44, 10, '', NULL, 'en attente', '2025-11-30 21:00:42'),
(30, 43, 10, '', NULL, 'en attente', '2025-12-02 01:24:52'),
(31, 45, 11, '', NULL, '', '2025-08-19 00:59:54'),
(32, 42, 11, '', NULL, '', '2025-11-15 13:45:41'),
(33, 46, 11, '', NULL, '', '2025-11-14 19:21:06'),
(34, 47, 12, '', NULL, '', '2025-11-18 10:00:43'),
(35, 46, 12, '', NULL, '', '2025-11-09 08:24:22'),
(36, 50, 12, '', NULL, 'en attente', '2025-10-14 16:58:26'),
(37, 44, 13, '', NULL, '', '2025-11-27 15:16:21'),
(38, 47, 13, '', NULL, '', '2025-11-28 22:40:46'),
(39, 41, 13, '', NULL, '', '2025-11-28 06:36:10'),
(40, 44, 14, '', NULL, 'en attente', '2025-12-06 00:38:38'),
(41, 41, 14, '', NULL, '', '2025-12-04 22:13:52'),
(42, 50, 14, '', NULL, '', '2025-12-06 14:45:59'),
(43, 44, 15, '', NULL, '', '2025-12-07 09:15:48'),
(44, 41, 15, '', NULL, 'en attente', '2025-07-05 04:38:51'),
(45, 49, 15, '', NULL, 'en attente', '2025-09-20 02:48:06'),
(46, 41, 16, '', NULL, 'en attente', '2025-10-05 15:36:43'),
(47, 50, 16, '', NULL, '', '2025-07-26 22:03:04'),
(48, 46, 16, '', NULL, 'en attente', '2025-08-10 12:01:53'),
(49, 43, 17, '', NULL, 'en attente', '2025-09-26 15:29:49'),
(50, 45, 17, '', NULL, 'en attente', '2025-11-16 11:54:45'),
(51, 48, 17, '', NULL, 'en attente', '2025-10-27 23:05:53'),
(52, 42, 18, '', NULL, '', '2025-12-04 04:06:24'),
(53, 50, 18, '', NULL, '', '2025-11-24 09:00:37'),
(54, 46, 18, '', NULL, '', '2025-11-23 17:15:37'),
(55, 45, 19, '', NULL, '', '2025-12-14 12:22:42'),
(56, 49, 19, '', NULL, '', '2025-12-11 20:17:48'),
(57, 48, 19, '', NULL, '', '2025-12-12 06:03:35'),
(58, 47, 20, '', NULL, '', '2025-09-04 06:28:34'),
(59, 50, 20, '', NULL, 'en attente', '2025-11-25 03:01:43'),
(60, 44, 20, '', NULL, 'en attente', '2025-12-10 04:13:51'),
(61, 44, 21, '', NULL, '', '2025-12-07 20:32:51'),
(62, 46, 21, '', NULL, '', '2025-10-06 06:55:17'),
(63, 49, 21, '', NULL, '', '2025-09-05 00:47:26'),
(64, 41, 22, '', NULL, 'en attente', '2025-09-06 06:12:33'),
(65, 46, 22, '', NULL, '', '2025-11-25 19:18:49'),
(66, 49, 22, '', NULL, '', '2025-09-21 11:50:16'),
(67, 47, 23, '', NULL, '', '2025-08-15 13:39:35'),
(68, 49, 23, '', NULL, '', '2025-10-09 02:31:39'),
(69, 44, 23, '', NULL, 'en attente', '2025-10-22 02:22:57'),
(70, 47, 24, '', NULL, '', '2025-10-03 20:15:44'),
(71, 46, 24, '', NULL, 'en attente', '2025-10-28 04:21:08'),
(72, 50, 24, '', NULL, 'en attente', '2025-10-06 02:01:11'),
(73, 48, 25, '', NULL, '', '2025-12-10 05:28:04'),
(74, 41, 25, '', NULL, 'en attente', '2025-08-20 05:45:43'),
(75, 44, 25, '', NULL, '', '2025-11-14 10:58:27');

-- --------------------------------------------------------

--
-- Structure de la table `offres_emploi`
--

CREATE TABLE `offres_emploi` (
  `id` int(11) NOT NULL,
  `employeur_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `salaire` varchar(100) DEFAULT NULL,
  `type_contrat` enum('CDI','CDD','Intérim','Stage','Freelance') NOT NULL DEFAULT 'CDI',
  `date_publication` datetime NOT NULL DEFAULT current_timestamp(),
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `offres_emploi`
--

INSERT INTO `offres_emploi` (`id`, `employeur_id`, `titre`, `description`, `lieu`, `salaire`, `type_contrat`, `date_publication`, `est_actif`) VALUES
(1, 36, 'Le droit d\'atteindre vos buts plus simplement', 'Facilis ut voluptatum soluta. Autem blanditiis qui quo vero vel repudiandae. Voluptas qui adipisci et error eveniet officiis aut. Minima nemo accusantium dolorem tempore et ipsam.\n\nQui iste quisquam error laudantium id molestiae quia suscipit. Qui et consequuntur odit est modi optio. Voluptatem est eius incidunt molestiae. Non et possimus eius dolores debitis.\n\nAut eveniet aliquid accusantium. Eveniet officia placeat ab dolore qui non. Aut beatae expedita pariatur debitis.\n\nVoluptatem est ut non id. Cum veritatis sed molestiae optio magnam doloremque deleniti. Modi aut et ab tenetur laborum ab perspiciatis.\n\nAut quo qui quam repellendus. Aspernatur rerum repudiandae consequatur vero placeat fugiat sint.', 'Meyer', NULL, 'Intérim', '2025-09-04 10:45:05', 0),
(2, 36, 'L\'art de changer sans soucis', 'Et sed repellendus dolorum blanditiis. Quis inventore expedita culpa ut rerum. Nulla distinctio ipsam sit aut dicta omnis reiciendis. Perspiciatis eum laboriosam voluptatum at.\n\nCum dolorem nihil fugit. Vel illo autem dolor voluptatem. Ut sint architecto libero et distinctio aut. Veniam ex error inventore dolorem reprehenderit voluptate.\n\nDolor voluptate suscipit eaque voluptatem maiores a vero. Et consectetur dolores sint voluptatem et dolor illo veritatis. Omnis dolorum amet occaecati distinctio qui molestiae.\n\nAut aut ipsum minima quam. Asperiores ut voluptatem autem non mollitia. Sed et qui totam a non in in.\n\nRerum possimus quod porro et. Laudantium deserunt sit excepturi dolores quis. Quisquam et consequatur ducimus voluptate.', 'Navarro-sur-Mer', NULL, 'CDI', '2025-10-01 12:43:26', 1),
(3, 36, 'L\'art d\'innover sans soucis', 'Blanditiis nobis dolorem voluptates. Molestiae similique voluptas qui ad exercitationem unde rerum. Eligendi est sint velit saepe veritatis architecto sequi voluptas. Qui debitis voluptate non dolorum deleniti aspernatur nobis ipsa.\n\nDicta porro aliquam quasi at suscipit. Assumenda consequatur quo eum architecto possimus.\n\nSuscipit nihil sunt velit magni doloribus fuga consequatur. Vitae et nostrum itaque animi eos consequatur. Mollitia ipsum accusamus tempore sed aut omnis. Quibusdam in numquam cum ipsam. Libero enim officiis fugiat sunt dicta iste.\n\nAccusantium officiis ullam itaque quia assumenda sapiente inventore. Id voluptatem aspernatur nesciunt ex dignissimos sit unde. Qui ad doloremque eum vel.\n\nExplicabo natus quis aspernatur pariatur. Non iusto reiciendis eaque et doloremque adipisci. Ipsum aut aperiam non sapiente quidem sint. Dolor illum doloremque fugiat possimus.', 'Lecoq', NULL, 'CDD', '2025-11-02 13:38:07', 1),
(4, 36, 'La simplicité de concrétiser vos projets à l\'état pur', 'Dolorum ut sunt similique voluptatum dolores. Cumque dicta facere rem distinctio eveniet delectus est odit. Consequatur omnis aut quaerat aspernatur aut est vero. Officia iure velit vitae consequatur. Est rerum maxime earum pariatur velit.\n\nSed quasi sit in sit nobis rem mollitia. Et omnis officiis libero. Aut omnis aliquam ea nostrum eligendi temporibus. Omnis et iure officia hic ut.\n\nPerferendis aperiam non autem debitis praesentium facilis error sint. Omnis earum quibusdam tenetur tempore quia pariatur et. Qui qui amet velit et ut sunt natus.\n\nEnim sed itaque est voluptate officiis sit eveniet. Dolores expedita consequuntur sunt consequuntur reprehenderit. Fugit quis reprehenderit animi quo sunt. Dolorem impedit consequatur at dolor tempora nisi.\n\nDelectus quae omnis architecto nobis facilis voluptates. Suscipit ut assumenda alias tempora sunt. Occaecati iure tenetur doloremque autem ut cumque. Tenetur quas qui omnis quisquam et et earum.', 'Weberdan', NULL, 'CDI', '2025-09-27 10:46:36', 1),
(5, 36, 'Le droit d\'innover plus simplement', 'Non in tenetur nemo porro voluptas sit voluptas. Non mollitia dolore tempora qui ipsum magnam autem. Inventore sint itaque est beatae quia voluptas. Qui animi impedit rerum quibusdam atque. Beatae ea voluptatem maxime enim rem alias vel.\n\nUllam sed sed delectus nulla dolorum. Laborum quia illum nobis natus totam voluptatibus. Voluptas rerum voluptas reiciendis non. Maiores enim quia saepe atque temporibus ipsa adipisci.\n\nAccusamus ipsa possimus dolorem necessitatibus. Quasi enim repellat itaque velit qui molestiae laudantium. Doloribus aut omnis aut provident quis magnam itaque qui.\n\nMolestiae quos ut itaque saepe architecto. Quae aut assumenda libero quisquam laudantium. Quam animi magnam totam similique est. Quisquam ratione repudiandae ad hic magni.\n\nArchitecto voluptates sed cupiditate ex. Architecto aut expedita id laborum rem. Et ut debitis ea quia aut voluptatem ea. Saepe quas porro et porro ducimus vero.', 'Mahedan', NULL, 'Intérim', '2025-09-16 20:53:19', 0),
(6, 37, 'Le pouvoir de louer naturellement', 'Unde vel ut rerum magnam recusandae. Corrupti ullam error voluptate vel ducimus voluptatum.\n\nSint ipsa voluptatibus exercitationem qui qui consequatur. Quo in est cumque ut ad laboriosam. Hic molestiae libero iste omnis impedit excepturi rerum. Eveniet sequi eum laboriosam perferendis.\n\nIn eveniet quis suscipit. Minus animi cumque ut culpa ea totam. Praesentium voluptatem esse voluptatibus quasi debitis sit.\n\nDolorem sit adipisci molestiae et. Illum a et possimus et ut et et. Aperiam rerum ducimus quia dolor non rem officiis.\n\nDolorem dolorem fugiat quidem. Cumque unde totam nesciunt dolorem cumque expedita accusantium quibusdam. Doloremque recusandae cum et id nostrum. Facere error animi nobis aut quasi et excepturi.', 'Diaz', NULL, 'CDD', '2025-08-02 11:23:40', 0),
(7, 37, 'La possibilité de louer naturellement', 'Libero ex sit labore nostrum distinctio. Beatae et consequatur eveniet sequi quia aut. Voluptatem molestiae repellat occaecati tenetur ex et quisquam impedit. Quam tenetur placeat recusandae soluta minus illo.\n\nQui necessitatibus nihil magni blanditiis sit ipsa nostrum dolorem. Doloribus mollitia expedita est quia voluptatibus praesentium cum. Labore corrupti illum cum. Nostrum sed et molestiae vel quia dicta distinctio.\n\nEst aut fugit dolorem autem consectetur. Aut cumque rerum nesciunt suscipit porro ea incidunt quasi. Quo ut reprehenderit provident provident non asperiores quo.\n\nAut non voluptas quidem qui nisi unde. Esse non aperiam sapiente explicabo. Rerum eius ut qui ratione ipsa asperiores omnis.\n\nQuod nesciunt porro impedit ullam delectus. Magnam similique eius in. Repellat molestiae voluptatem architecto et in in. Animi sunt dolores hic inventore.', 'Henry', NULL, 'CDI', '2025-09-02 14:19:52', 1),
(8, 37, 'L\'art de rouler plus rapidement', 'Odit et ab dolor ut dignissimos temporibus dolorum. Magnam temporibus beatae sint aut eum autem. Praesentium hic quis facere quo tenetur facere. Sunt consequatur iusto repudiandae aut iste placeat velit.\n\nLabore non dolores omnis non ea omnis. Necessitatibus aperiam mollitia rem et impedit. Quaerat harum eius eos aspernatur corrupti.\n\nProvident natus ad aliquam maiores eos tempore id asperiores. Minus in est quia voluptatum. Qui molestiae ab perspiciatis architecto vel totam. Porro ut ea iste est aspernatur commodi ducimus.\n\nVoluptatem eum quae corporis ratione earum aut. Animi perferendis aut voluptatem omnis. Quaerat quisquam est sapiente velit inventore occaecati.\n\nMagni dolores dolorem hic adipisci. Velit impedit explicabo est vero error. Et aut sequi ut. Ut neque est dolores rerum.', 'Imbert-la-Forêt', NULL, 'Stage', '2025-10-14 00:32:46', 1),
(9, 37, 'Le plaisir d\'innover en toute tranquilité', 'Excepturi eum qui vel beatae. Aut soluta ullam maxime ducimus unde velit cum quasi. Sed veniam doloremque et ipsum voluptatem autem. Delectus minima eius aut molestiae et provident.\n\nEaque et quia aut eveniet ut. Recusandae amet qui nam sit expedita. Minima voluptas pariatur ad perspiciatis vel quas modi adipisci.\n\nCum repellendus minima nisi consequatur et sint molestias unde. Ut nemo non enim natus voluptatem autem. Pariatur nesciunt nesciunt quos provident quam. Consequatur est dignissimos et autem dolor.\n\nVel possimus placeat aut fugiat aperiam quam. Repellat qui odit fugiat ex tempora. Sit minima accusamus dignissimos non illum et. Quia deleniti possimus sit sed sit necessitatibus.\n\nId corporis dolor similique sunt. Mollitia cupiditate quidem at explicabo aspernatur. Illum vel laudantium ratione eum delectus expedita fugiat.', 'Lacroix-la-Forêt', NULL, 'CDD', '2025-11-02 21:07:02', 1),
(10, 37, 'La possibilité d\'évoluer à l\'état pur', 'Dolor et nostrum magni at qui molestias. Modi suscipit et qui ipsum. Dolorum reprehenderit laboriosam eos esse officiis eos.\n\nId vel eveniet quis id velit ea qui. Quis ea est quibusdam sed. Voluptatem non autem quasi est fugit quidem.\n\nNemo dolorem vel maiores. Maiores deleniti ex dolorem quaerat vero. Corrupti maiores itaque architecto est et voluptatem. Cum harum accusamus aut aliquid.\n\nOmnis officia est ea voluptatum autem veritatis molestiae. Error voluptatem velit nam qui omnis corporis et aut. Molestias ut similique tempora sint est dolores ullam.\n\nCorporis sit expedita explicabo aut aspernatur. Aut consequuntur et ut magni. Et corporis sunt cumque est quos animi dolore.', 'Simon', NULL, 'CDD', '2025-11-09 20:52:09', 0),
(11, 38, 'L\'assurance de changer naturellement', 'Possimus facere minima magni consequatur quia. Nisi facere enim maxime libero qui cum autem. Accusantium culpa accusamus voluptatum incidunt veniam.\n\nExcepturi voluptatum quis nisi optio. Ratione molestiae ut et molestiae nulla omnis velit. Voluptate qui modi eos atque.\n\nSunt et quaerat aperiam mollitia sit at et. Hic aspernatur sit et. Corrupti consectetur sit iure vitae et ut.\n\nRepellat vitae nostrum impedit consequatur vitae veniam aut. Adipisci eos vitae hic et voluptas. Nihil placeat est incidunt vel dolore.\n\nSint qui corrupti tenetur nam. Quaerat sit molestiae delectus laboriosam. Incidunt rem in tempora. Nisi perspiciatis et ipsum nihil eius architecto.', 'Meyer-la-Forêt', NULL, 'Intérim', '2025-07-24 09:56:55', 1),
(12, 38, 'Le confort de louer en toute sécurité', 'Qui ex iure odio et quia. Et eius quod odit laboriosam. Repellendus non est eos eos est aut.\n\nDistinctio molestiae rem nisi maxime nostrum. Tempore non quibusdam hic temporibus necessitatibus id quibusdam qui. Laborum aut consequuntur dolores aliquam aut et. Quod deleniti laborum repudiandae quis.\n\nNeque sed corporis ipsa dolore quis. Molestiae beatae possimus culpa suscipit nostrum aut. Earum optio et est voluptatum. Ea quae culpa esse.\n\nLaborum rerum soluta non maiores qui consequuntur. Fuga et non dolorem ipsam autem velit harum. Vitae nam velit non hic porro culpa aut et. Sunt sint necessitatibus qui. Non consequatur sint occaecati vel qui qui amet.\n\nQuidem vero ea quam accusantium iste. Amet repellat voluptates rerum molestiae itaque laborum. Commodi unde corrupti perferendis aliquam ratione. Recusandae optio fugit quidem nihil natus.', 'Marty-la-Forêt', NULL, 'CDD', '2025-07-09 11:04:24', 1),
(13, 38, 'Le droit d\'évoluer avant-tout', 'Officia rerum dolore esse aliquam cupiditate consequatur. In natus temporibus deleniti. Quia delectus necessitatibus porro reprehenderit.\n\nRepellat eaque aliquid occaecati nam non voluptate aut. Laboriosam hic soluta porro ut.\n\nRepellat atque quia rerum quis laboriosam necessitatibus. Sint sed impedit excepturi minus eum modi. Sed qui rerum voluptates. Sit illum nesciunt dolore natus aut velit.\n\nHic officiis dolores qui ut expedita dicta eos. Cupiditate ut at non dolorem. Consequatur sint odio sint nisi qui autem voluptatum.\n\nSint voluptatem dolore autem cumque quas est. Autem dolorem vitae pariatur. Dicta aspernatur sed accusantium odio ab.', 'Adam-sur-Mer', NULL, 'Intérim', '2025-11-21 14:46:13', 1),
(14, 38, 'La possibilité d\'atteindre vos buts en toute sécurité', 'Aut sapiente ut rerum adipisci dolor facere minus. Natus consequatur voluptas illo assumenda dolor.\n\nNihil aut nam eum quod atque dolores consequatur. Natus quas numquam aperiam delectus dolor rem. Ut ab consequuntur nam laboriosam. Et commodi sit quae assumenda quos et ullam. Labore reprehenderit illum autem est velit maxime laborum.\n\nAutem libero quos eius quae veniam voluptatem. Ut aliquid praesentium aut nihil omnis vel placeat. Eaque aspernatur hic non et molestias totam distinctio.\n\nIllo qui consequatur ipsum repudiandae est quae. Et dicta debitis debitis minus dolores officia et nisi.\n\nId sed exercitationem impedit. Sunt officia et iste. Dolore ut atque aspernatur architecto ut.', 'Becker', NULL, 'CDD', '2025-11-28 03:40:15', 1),
(15, 38, 'La possibilité de changer à l\'état pur', 'Ut amet molestiae autem qui ad. Aut eum dolorum facere quos ipsam. Voluptatem laudantium dignissimos qui velit.\n\nAlias ab deserunt fugit omnis illo. Hic dolores quia dolorem rerum. Non harum quam autem quod aliquid quas sunt.\n\nCupiditate doloribus voluptatem laboriosam rerum omnis laborum. Qui voluptas accusamus facere. Voluptate voluptatem est in eos voluptatem ratione. Officia adipisci temporibus consequuntur quidem.\n\nRerum aut dolores vitae nemo consectetur. Corporis sapiente explicabo quasi ipsa ut. In a atque alias sit eveniet ea libero quae. Sit voluptatem inventore dolorem neque et eligendi sit. Quo architecto ullam repudiandae distinctio non veritatis et.\n\nEst qui autem eaque cum beatae nobis. Alias repudiandae vero magni et asperiores quo laudantium velit. Quam quo est et vel repudiandae in ut. Aliquam porro mollitia quam saepe autem et.', 'Legrand', NULL, 'CDD', '2025-06-21 06:24:52', 1),
(16, 39, 'La sécurité de rouler de manière sûre', 'Sequi delectus expedita assumenda. Qui fugiat animi blanditiis magnam sint asperiores ducimus enim.\n\nEos iusto non reprehenderit consequatur ut. Hic ut occaecati laboriosam. Libero quia soluta quia incidunt natus.\n\nEst culpa eaque et deserunt sit. Soluta voluptatem cupiditate rem quasi repellat. Necessitatibus similique nesciunt aut illo.\n\nVoluptatem at consequatur quaerat ipsam tempore aut. Et nisi rerum voluptatibus corporis omnis nisi. Nihil odio eius et est quasi a officiis. Quo perspiciatis ipsam adipisci corrupti adipisci.\n\nEt ullam rerum est pariatur quo distinctio. Ipsam laboriosam deleniti rerum hic rerum. Dolore esse repellat et. Dolorem et repellendus at et autem.', 'Reynauddan', NULL, 'Stage', '2025-07-05 22:12:39', 0),
(17, 39, 'La possibilité d\'atteindre vos buts autrement', 'Perspiciatis ab officiis eum architecto adipisci. Vitae molestias quis repellendus quasi autem cupiditate officia. Minima fuga voluptates animi alias ut.\n\nDistinctio nemo perferendis velit exercitationem reiciendis. Doloremque facere quae at officiis itaque. Sunt quas voluptatem voluptate qui consequuntur.\n\nOfficia molestiae consectetur ducimus fuga saepe dolore est. Officiis consequatur accusamus eum corrupti voluptas repellendus consequatur aspernatur. Beatae quia exercitationem nostrum eos autem maiores distinctio.\n\nIste labore quasi temporibus eaque molestiae asperiores commodi. Vitae consequuntur veritatis vel maiores. Cupiditate asperiores culpa architecto. Laudantium soluta excepturi sint dolorem nihil voluptatem ex.\n\nDolor nostrum pariatur corrupti velit rerum qui et. Id aut officia officiis beatae labore adipisci reprehenderit. Sit architecto maiores ad architecto assumenda voluptatem.', 'Rochenec', NULL, 'Stage', '2025-07-07 05:50:18', 1),
(18, 39, 'La simplicité d\'atteindre vos buts avant-tout', 'A amet dicta optio voluptatem in quod et. Dolorem vitae veniam id autem ut sapiente beatae. Id fugit voluptatem delectus rerum aut minus fugiat.\n\nEsse accusantium tempore veritatis ad eos aut. Sit sed dolores perferendis in. Similique voluptatem architecto rerum. Expedita voluptate quia atque vero.\n\nRepudiandae quos dolores necessitatibus deleniti quis rem. Quaerat dolorem quia quia quidem natus. Architecto voluptatem aut beatae.\n\nSapiente optio vero aliquam suscipit non molestias. Et quia voluptates eum mollitia iure. Saepe id nulla facere ex sunt. Omnis delectus ab atque.\n\nTemporibus corporis earum voluptates nihil nemo odio rerum. Incidunt quo qui quos dolor repudiandae eveniet. Nemo nam laborum dolorem quia autem. Voluptate dolor natus assumenda id nesciunt reprehenderit.', 'Pineau-les-Bains', NULL, 'Intérim', '2025-08-21 07:13:03', 0),
(19, 39, 'Le confort de rouler à sa source', 'Accusamus voluptatem nesciunt iusto dolor sint. Nisi soluta ut minima ut quos provident reprehenderit at. Et voluptatum est harum laboriosam non explicabo temporibus. Non omnis hic quia voluptatem enim blanditiis.\n\nOmnis praesentium et reiciendis qui. Incidunt illo quam harum facilis et. Unde fugiat adipisci aut quam. Nihil et consequatur totam et dolorem.\n\nPariatur a et temporibus ut cupiditate vitae. Doloremque vero quo mollitia et iste. Quidem amet ut maiores asperiores impedit consectetur tenetur.\n\nImpedit quod dolor molestiae molestias nihil consectetur. Aut eligendi dolorum perferendis sit. Non sed molestiae reiciendis quia aut non perspiciatis. Voluptate rerum beatae rem voluptatibus.\n\nQuisquam mollitia et aliquid facere deleniti dolor repellat. Et quia blanditiis itaque quia itaque tempore. Aperiam eum et qui sit aut.', 'Albert-sur-Guillon', NULL, 'Intérim', '2025-12-11 12:13:21', 1),
(20, 39, 'La possibilité de concrétiser vos projets autrement', 'Ipsum velit vel in deserunt. Accusamus consequatur consequatur impedit quibusdam sit. Distinctio ut et nisi qui quia rerum impedit. Qui harum sit nemo tempora ea perspiciatis.\n\nIste ut itaque ea qui molestiae. Neque aut quis laudantium nisi sit est. Unde optio commodi unde deserunt sunt consequuntur magni et. Totam nihil itaque odio eos.\n\nEt omnis earum ut et ipsa dolore. Amet quidem et ut et soluta facilis. Qui qui autem laboriosam laudantium qui vitae rem. Autem animi illum veritatis eos minus sed.\n\nIllum sint deleniti beatae vel ut. Est rerum quae dolorum maiores sint. Perferendis quidem consequatur consequatur tempore odit fugit.\n\nItaque iusto laboriosam omnis. Magnam laudantium a architecto rerum repellendus repudiandae. Velit asperiores omnis dolores sed explicabo officia. Rem voluptatem aliquam nesciunt eos.', 'Fabre', NULL, 'CDD', '2025-09-04 00:21:34', 1),
(21, 40, 'L\'avantage de changer plus simplement', 'Autem soluta veritatis veniam laborum officiis error. Eligendi inventore aliquam tenetur qui eius id. Provident quia cumque ut. Quia id vel rerum nemo aut consequatur.\n\nEa aut fugiat laborum adipisci nulla et. Culpa et doloribus in est ab blanditiis error. Vero qui quia omnis. Non minima et doloremque quia itaque.\n\nUt magnam similique cupiditate ad tempore. Velit consectetur quos corrupti repellat. Eius et nobis perferendis nobis voluptatem et veniam. Non quod omnis repudiandae unde dignissimos dolorem animi. Placeat optio qui corporis cum dolorem.\n\nIpsa repudiandae dolorum explicabo esse. Sint suscipit et vero nisi ab corporis. Aut magni iure at non neque qui. Quas rem quis sint sed consequuntur delectus ab.\n\nSaepe quae reprehenderit eos est ad recusandae eveniet quo. Maxime magnam saepe ut sit reprehenderit. Dignissimos officiis dicta sed mollitia temporibus.', 'Roy', NULL, 'Intérim', '2025-08-10 02:08:32', 1),
(22, 40, 'Le plaisir de concrétiser vos projets de manière efficace', 'Incidunt dignissimos iure rerum. Totam molestiae qui eligendi molestiae temporibus. Voluptate odit deserunt amet placeat numquam rerum sed. Nesciunt et natus aut dolorum rerum provident.\n\nQuam voluptatum non est debitis. Sit voluptatibus quibusdam ullam dolorem consectetur sit in. Possimus vitae tempora libero sit est repellat est. Repudiandae autem natus ipsum doloremque et. Consequatur sint tenetur mollitia impedit.\n\nIpsa inventore praesentium sed tenetur nemo quos. Eligendi et expedita voluptatem porro. Unde aut autem necessitatibus quos voluptas. Et quibusdam ex eum sed vitae laboriosam est. Repellendus impedit vitae ea consequuntur.\n\nId aliquam necessitatibus dolore omnis. Fuga in aliquid et iste est. Facilis laudantium dolor dolorem qui sint. Hic exercitationem culpa quae non blanditiis et cum.\n\nEst labore eveniet voluptates consequatur qui dolore. Corporis quos veritatis nihil libero debitis. Reprehenderit repellendus voluptas reiciendis ad sed non. Aspernatur iure ex asperiores possimus quaerat ipsam.', 'Legendre', NULL, 'CDD', '2025-07-20 13:41:14', 1),
(23, 40, 'Le droit d\'atteindre vos buts à la pointe', 'Harum itaque eius et vero id itaque. Aperiam quam enim aperiam et ullam. Odit illo ut asperiores doloremque delectus fugiat.\n\nFacere pariatur pariatur optio sapiente. Quis quis minus autem ut.\n\nEius et aut ut sit perferendis consequatur omnis aut. Aliquid omnis dolorem perspiciatis. Qui fugit ratione et officia voluptas. Aliquid in officiis inventore voluptatem.\n\nTempora earum commodi est quia fugit deserunt quidem. Accusamus et corporis excepturi nisi magnam mollitia. Sequi est ut vel debitis quia. Et et sit at qui consequatur voluptas.\n\nMinus deserunt ut voluptatem. Quia explicabo reprehenderit corrupti deleniti porro cupiditate laudantium. Ducimus cum dolorum est. Culpa autem et est officiis.', 'Fontaine', NULL, 'CDD', '2025-07-24 14:43:16', 0),
(24, 40, 'Le plaisir de concrétiser vos projets sans soucis', 'Ratione molestiae asperiores aut officia hic tenetur. Ipsum labore corrupti dolorem voluptas. Suscipit dolor unde minus pariatur.\n\nVeritatis tempora optio eum debitis aliquid. Aut quasi modi ut ut quibusdam libero dolore. Numquam reiciendis accusantium sit tenetur ex odit. Ipsum quod consequuntur excepturi deserunt quis repellendus.\n\nPossimus adipisci esse commodi aut inventore nemo. Fugit sint earum et alias vero dolorem consequatur. Qui doloribus asperiores rerum.\n\nQuia temporibus repellendus animi facere omnis expedita. A est eum enim. Esse expedita non est saepe et.\n\nEt aut sed laborum. Sunt ipsa sed eligendi debitis fuga natus quia autem. Qui eligendi ipsum ut sequi ducimus et. At enim voluptates sint.', 'Reynaudnec', NULL, 'CDD', '2025-09-26 06:05:32', 0),
(25, 40, 'Le plaisir de concrétiser vos projets à la pointe', 'Enim dolor iste omnis et odit. Eligendi asperiores corporis quis voluptas deleniti consequuntur. Et quia qui sit impedit. Quia et quas id deserunt velit et et.\n\nDolor provident sint amet sunt. Error minima dolores est blanditiis laborum vel. Autem consequuntur sed ut illum reprehenderit minus. Voluptates laudantium asperiores suscipit optio quidem in.\n\nProvident qui ut aperiam consequatur ab rerum. Cum magni mollitia dolor enim voluptatem. Blanditiis ipsum ut nesciunt voluptas id.\n\nAutem provident et molestiae dolorem. Eveniet quisquam unde dolore modi eum. Eveniet rem dolores fuga laborum possimus.\n\nAdipisci est voluptates ut ut veniam. Accusamus reprehenderit et vitae ratione. Quam est ut sed in et ut debitis.', 'ColasVille', NULL, 'Intérim', '2025-07-31 23:07:32', 1);

-- --------------------------------------------------------

--
-- Structure de la table `profils_entreprise`
--

CREATE TABLE `profils_entreprise` (
  `employeur_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `secteur_activite` varchar(100) DEFAULT NULL,
  `adresse_siege` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `profils_entreprise`
--

INSERT INTO `profils_entreprise` (`employeur_id`, `description`, `secteur_activite`, `adresse_siege`, `telephone`, `site_web`) VALUES
(36, 'Provident non unde cumque quos dolore consequatur et. Aut doloribus nobis voluptatem corporis. Est sunt doloremque et ut.\n\nAlias voluptatem at vel aliquid quia saepe. Sapiente ut delectus ad consequatur soluta. Consequuntur natus sapiente ea deserunt et quisquam dolorem.\n\nOptio quia nostrum blanditiis earum culpa qui qui ut. Nobis ex aperiam quia velit. Aut asperiores occaecati enim fugiat quia aliquid nihil.', 'Portier', '546, rue Jean\n24360 Hardy', '+33 (0)2 33 37 62 11', 'http://renard.com/voluptas-sed-dignissimos-illo'),
(37, 'Blanditiis fugit et rem. Ex minima repellat autem. Culpa consectetur corporis voluptatem recusandae optio exercitationem dolorum.\n\nEt non sint a possimus. Nostrum dicta assumenda dignissimos et. Recusandae autem vel repellendus et et est aut animi.\n\nNemo aliquid est facere non praesentium corrupti asperiores. Provident velit saepe quae adipisci voluptatem est. Excepturi et consectetur sit rem consectetur laborum numquam.', 'Eleveur de volailles', 'rue Michèle Antoine\n73419 Fontaine-sur-Roy', '+33 (0)4 98 85 23 89', 'http://gautier.fr/modi-labore-qui-culpa'),
(38, 'Consequuntur veritatis voluptatem eos rem. Aut provident fuga neque officiis voluptate praesentium. Atque eum autem cupiditate est ipsam autem. Sit architecto ut incidunt autem. Non quos id adipisci et molestias.\n\nDoloribus sed molestiae voluptas. Qui aut aut in earum. Accusamus saepe voluptas fugiat itaque. Et alias eos nulla aut. Earum temporibus consequatur dolores expedita dolor ab.\n\nVel omnis enim illum rerum. Ut est in et quam accusamus voluptas. Aut dolor non minus ipsum. Non atque distinctio ex adipisci minima doloremque esse natus.', 'Testeur informatique', 'chemin Germain\n53085 Marques', '07 73 52 42 12', 'https://pruvost.net/numquam-ipsam-tenetur-eius-similique-laboriosam-beatae.html'),
(39, 'Sint quo similique quisquam. Sint architecto nulla alias quae. Molestiae possimus et repudiandae ut. Iure aut et sunt velit.\n\nLibero debitis numquam praesentium debitis quas quo est. Sit ut eum doloremque eum sed. Nihil sed ut magni qui. Mollitia beatae qui ea odio eveniet est quae consequatur. Maxime saepe similique nisi consequuntur id et.\n\nIpsum iure est dolor quo. Voluptas numquam quisquam tempore saepe. Nihil quia unde neque rerum.', 'Scénariste', '16, impasse Morel\n26908 Martel', '01 94 02 93 89', 'https://www.michaud.com/similique-facilis-distinctio-sunt-quia-et'),
(40, 'Tempora sed quia quia placeat non alias quae. Quasi dolor eum eos.\n\nDolorem aut molestiae quam velit. Aperiam facilis qui consequuntur ipsa eos. Dicta quos dolorem et et aut blanditiis. Est ipsa dolor rerum beatae aliquid voluptas maxime.\n\nAd quis minus aut animi consequatur omnis adipisci. Non porro est sint delectus. Nostrum ipsa mollitia nihil necessitatibus eligendi quis illum.', 'Médecin scolaire', '719, rue Peron\n91083 Joubert-sur-David', '06 99 97 68 49', 'http://www.thibault.net/iste-praesentium-deleniti-nostrum');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('candidat','employeur','admin') NOT NULL DEFAULT 'candidat',
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `photo_profil_chemin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_inscription`, `photo_profil_chemin`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$rZaILUSh5.jZZ1yl9xcKyOOsFsf4ihhGGH5InH4tVK42t.KBrh0ke', 'admin', '2025-11-28 19:10:10', NULL),
(2, 'candidat', 'candidat@gmail.com', '$2y$10$rZaILUSh5.jZZ1yl9xcKyOOsFsf4ihhGGH5InH4tVK42t.KBrh0ke', 'candidat', '2025-11-28 20:49:29', NULL),
(3, 'employeur', 'employeur@gmail.com', '$2y$10$rZaILUSh5.jZZ1yl9xcKyOOsFsf4ihhGGH5InH4tVK42t.KBrh0ke', 'employeur', '2025-11-28 20:50:27', NULL),
(36, 'Gautier', 'isaac.daniel@bruneau.fr', '$2y$10$yYIjhXv089LT1LkOWTk4L.JSkYG2Sk1H0Ps16HwzyxpfTl5CHs7.S', 'employeur', '2025-03-12 14:20:06', NULL),
(37, 'Couturier SARL', 'marion.maurice@leroux.fr', '$2y$10$KGJ0RO5YZGJyouIZH/Z/tekEYFoZy9cYKci4pbawQlMuK/fj7KYCS', 'employeur', '2025-11-17 09:09:12', NULL),
(38, 'Gay Blin SARL', 'dominique98@joubert.fr', '$2y$10$93Jt8fJJHoCQ6PwGs2N/TeXdUFi.K.r.utJwTzQa45e6sXbpmvoLy', 'employeur', '2025-01-19 18:27:30', NULL),
(39, 'Rousset S.A.S.', 'adrien.laporte@bourgeois.fr', '$2y$10$ILSsIc7O0hxEfuy6f7WkT.Oi7hB5NT.DKbDjqI8oDI.ix3zknPGey', 'employeur', '2025-06-19 03:52:32', NULL),
(40, 'Etienne', 'gaudin.denis@merle.fr', '$2y$10$cIVvJ0lGE1UJs9DM9yKn6eiJRnY67F3qIqru1ngdVzjMyW0kekoYq', 'employeur', '2025-08-21 13:38:53', NULL),
(41, 'Christophe Dubois', 'margot40@delaunay.fr', '$2y$10$H/w6a2Oe6lS2.oC4U8PrNOeB.X2OM.YkcyRBVNMeIvUEd8NVFstQq', 'candidat', '2025-05-29 07:14:46', NULL),
(42, 'Margot Michel', 'alphonse32@orange.fr', '$2y$10$rfQzi8ZlaqkDdxHFDHF36.T4d8esOgfmy8erulkZrgbmy2bNXPUhW', 'candidat', '2025-07-03 10:17:03', NULL),
(43, 'Christophe Levy', 'mlemonnier@wanadoo.fr', '$2y$10$iklVRDAvCihFv3WWkjkQHOfct.J3GhUPYwFnBCGVQJ6vRH2/ZltYS', 'candidat', '2024-12-30 08:18:18', NULL),
(44, 'Charles Diaz', 'emorin@noos.fr', '$2y$10$LMhcXpqpmJMrnOGPxTNykufjD/.S4rfLVbgn5pw98aJhv4ZI2d12a', 'candidat', '2025-05-26 12:59:36', NULL),
(45, 'Lucy Reynaud', 'dijoux.nicole@live.com', '$2y$10$IkvUizbnZUt1KfjmEWJG/unp1OlTc9b/6ffiaI1aPj.NLynlvL6BO', 'candidat', '2025-03-20 22:16:32', NULL),
(46, 'Thibault Clement', 'ferreira.agnes@barre.org', '$2y$10$uK8bEE/MizZRGUBEp4teiuXlSRvRxlvIgyttshwNgW08UFp/bP2Aa', 'candidat', '2025-06-16 17:51:36', NULL),
(47, 'Thibault Remy', 'renard.roger@yahoo.fr', '$2y$10$FXeutDmZrlmxAGiV4lKvRO0bb9vJDuUPT3cdl4Ry.yu4oI0GFC7Le', 'candidat', '2025-12-10 18:06:40', NULL),
(48, 'Jeannine Chretien', 'ngregoire@garcia.org', '$2y$10$NLUHpQZl24soAvs99plqnuSBQFUAeVm5.QeS5c38FxM5LJox61aJe', 'candidat', '2025-02-25 21:39:35', NULL),
(49, 'Zoé Germain', 'daniel.daniel@raymond.com', '$2y$10$jdJkZXwOnKiMf5SQltdiGOYftVhGKZFMVE26Y7jf6Yo/C5m7o3UWS', 'candidat', '2025-02-23 20:51:03', NULL),
(50, 'Victoire Moulin', 'marcel.rossi@free.fr', '$2y$10$0PPEYn3t5f/r.TobYvf0eu3PqdPNJZs13py8VxxGdgpHkzqi6ZidC', 'candidat', '2025-11-25 14:19:32', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `alertes_emploi`
--
ALTER TABLE `alertes_emploi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_candidature_unique` (`candidat_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- Index pour la table `offres_emploi`
--
ALTER TABLE `offres_emploi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employeur_id` (`employeur_id`);

--
-- Index pour la table `profils_entreprise`
--
ALTER TABLE `profils_entreprise`
  ADD PRIMARY KEY (`employeur_id`);

--
-- Index pour la table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uk_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `alertes_emploi`
--
ALTER TABLE `alertes_emploi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT pour la table `offres_emploi`
--
ALTER TABLE `offres_emploi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `alertes_emploi`
--
ALTER TABLE `alertes_emploi`
  ADD CONSTRAINT `alertes_emploi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres_emploi` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offres_emploi`
--
ALTER TABLE `offres_emploi`
  ADD CONSTRAINT `offres_emploi_ibfk_1` FOREIGN KEY (`employeur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profils_entreprise`
--
ALTER TABLE `profils_entreprise`
  ADD CONSTRAINT `profils_entreprise_ibfk_1` FOREIGN KEY (`employeur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
