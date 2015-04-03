USE mydatabase;

DROP TABLE IF EXISTS `boats_questions`;
CREATE TABLE IF NOT EXISTS `boats_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `question` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;


INSERT INTO `boats_questions` (`id`, `title`, `question`, `userid`, `rank`, `created`, `updated`, `deleted`) VALUES
(50, 'Byta tankmätare i en Nidelv245', 'Någon som vet var man får tag på en **tankmätare** till en Nidelv 24?', 12, 0, '2015-03-18 22:20:35', '2015-03-29 23:04:56', NULL),
(53, 'Spirbom', 'Någon som har en spirbom liggande?', 1, 1, '2015-03-23 13:25:59', '2015-03-29 20:47:43', NULL),
(63, 'Backslags-problem!', 'Har ett gammalt penta-backslag som slirar. Går det byta kon-kopplingen i den??!', 2, 0, '2015-03-29 14:08:34', '2015-03-30 15:26:09', NULL),
(64, 'Hur byter man topp på en MD5', 'Behöver skruva loss toppen pga dålig kompression. Något särskilt att tänka på ??', 1, 0, '2015-03-29 20:59:50', '2015-03-29 21:12:50', '2015-04-02 07:22:36'),
(65, 'Slitna vant', 'Tänkte byta vant på en **Sunwind 26**. Vilken dimension ska det vara? Tror inte det är original på båten?!?', 1, 0, '2015-03-29 21:01:13', '2015-03-29 22:26:39', NULL),
(88, 'Läckage genom förluckan', 'Hej\r\nLuckan i **förpiken** läcker oavsett hur mycket silikon jag tätar med. Var kommer vattnet ifrån?', 3, 1, '2015-03-30 15:30:13', '2015-04-02 09:21:02', NULL);



DROP TABLE IF EXISTS `boats_answers`;
CREATE TABLE IF NOT EXISTS `boats_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answer` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `questionid` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `vote` varchar(80) DEFAULT NULL,
  `accepted` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;



INSERT INTO `boats_answers` (`id`, `answer`, `userid`, `questionid`, `rank`, `vote`, `accepted`, `created`, `updated`, `deleted`) VALUES
(38, 'Ja jag har en överbliven från gamla båten.', 3, 53, 0, NULL, NULL, '2015-03-28 21:03:13', NULL, NULL),
(40, 'Mjae, det är tveksamt. Såvitt jag vet tillverkas det inga sådana delar längre. Däremot kan det finnas **utbytesbackslag**. Lämna ditt gamla och få ett renoverat.', 1, 63, 2, NULL, '2015-03-30 22:51:16', '2015-03-30 15:05:31', NULL, NULL),
(41, 'Byt luckan. Det kostar inte jättemycket och på köpet blir det tätt i många år!', 2, 88, 1, NULL, '2015-03-31 22:08:40', '2015-03-30 17:49:42', NULL, NULL),
(42, 'Passa på att rensa kylkanalerna i cylindern, speciellt om båten har gått på västkusten.', 2, 64, 4, NULL, '2015-03-31 22:47:43', '2015-03-30 20:40:17', NULL, NULL),
(45, 'Hör med **Nidelv** i Norge. Eller med [Hjertmans](www.hjertmans.se).', 1, 50, 1, NULL, '2015-03-30 22:59:29', '2015-03-30 22:56:13', '2015-03-30 22:58:59', NULL);



DROP TABLE IF EXISTS `boats_comments`;
CREATE TABLE IF NOT EXISTS `boats_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `questionid` int(11) DEFAULT NULL,
  `answerid` int(11) DEFAULT NULL,
  `rank` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;


INSERT INTO `boats_comments` (`id`, `comment`, `userid`, `questionid`, `answerid`, `rank`, `created`, `updated`, `deleted`) VALUES
(94, 'Jag är också intresserad av den. Vad ska du ha?', 12, 53, 38, 0, '2015-03-28 21:04:54', NULL, NULL),
(95, 'Vad är det för typ du är intresserad av? Teleskop?', 5, 53, NULL, 0, '2015-03-28 21:06:15', NULL, NULL),
(98, 'Jag har en liggande!', 2, 50, NULL, -1, '2015-03-29 14:09:40', NULL, NULL),
(99, 'Det gjorde jag en gång och fick ett backslag som läckte. Hela båten höll på att sjunka! Se upp för skojare.', 12, 63, 40, 1, '2015-03-30 15:06:39', NULL, NULL),
(100, 'Det går inte att lura vattnet. FInns det en öppning så letar det sig in. Jag gissar att **packningarna** är för gamla.', 12, 88, NULL, 3, '2015-03-31 21:33:05', '2015-04-02 09:19:18', NULL),
(101, 'Fast några tusenlappar får man nog räkna med...', 12, 88, 41, 1, '2015-03-31 21:33:35', NULL, NULL),
(105, 'Jag har en liggande!', 2, 53, NULL, 0, '2015-04-01 16:43:55', NULL, NULL);


DROP TABLE IF EXISTS `boats_tags`;
CREATE TABLE IF NOT EXISTS `boats_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;



INSERT INTO `boats_tags` (`id`, `name`, `deleted`) VALUES
(3, 'Elsystem', NULL),
(5, 'Säkerhet', NULL),
(6, 'Inredning', NULL),
(7, 'Motor', NULL),
(9, 'Styrning', NULL),
(11, 'Gummibåt', NULL),
(12, 'Vinterförvaring', NULL),
(13, 'Värme & Kyla', NULL),
(14, 'Rigg', NULL),
(23, 'Motorbåt', NULL),
(24, 'Segelbåt', NULL),
(25, 'Däcksutrustning', NULL),
(26, 'Pentry', NULL),
(27, 'Elektronik', NULL),
(28, 'Instrument', NULL),
(29, 'Navigering', NULL);



DROP TABLE IF EXISTS `boats_tags2question`;
CREATE TABLE IF NOT EXISTS `boats_tags2question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=308 ;


INSERT INTO `boats_tags2question` (`id`, `tag_id`, `question_id`) VALUES
(149, 3, 53),
(150, 14, 53),
(151, 24, 53),
(169, 7, 64),
(170, 23, 64),
(218, 14, 65),
(219, 24, 65),
(223, 3, 50),
(224, 5, 50),
(225, 7, 50),
(291, 7, 63),
(292, 23, 63),
(305, 6, 88),
(306, 24, 88),
(307, 25, 88);



DROP TABLE IF EXISTS `boats_user`;
CREATE TABLE IF NOT EXISTS `boats_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acronym` varchar(20) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `name` varchar(80) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `active` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `acronym` (`acronym`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;



INSERT INTO `boats_user` (`id`, `acronym`, `email`, `name`, `password`, `created`, `updated`, `deleted`, `active`) VALUES
(1, 'admin', 'admin@dbwebb.se', 'Administrator', '$2y$10$lSSX/D1m.AxALyQW6OkEZevyiOswxtu8I0vpUmC85gC2n/IRz4swG', '2015-03-23 00:00:00', '2015-04-02 20:29:19', NULL, '2015-04-02 20:29:19'),
(2, 'doe', 'doe@dbwebb.se', 'Jane Doe', '$2y$10$jJbmOxjvtJkhp.c6cJL4w.xKKNvEl/y56H5qZbEHb.2M8t7R3dGqq', '2015-03-15 00:00:00', '2015-04-02 20:29:31', NULL, '2015-04-02 20:29:31'),
(3, 'tompa', 'tommy@franskaord.se', 'Tomas Johansson', '$2y$10$v1VNF78.bx7l2wad4BJiOuv8n7wvp8WF9.2uL4/RtwOgQrN23teX.', '2015-03-09 00:00:00', '2015-04-02 20:29:54', NULL, '2015-04-02 20:29:54'),
(5, 'Kulan', 'kulan@me.com', 'Kulan', '$2y$10$ZZrp6di5OxU1nmAtfWtD3OAQ8eHTBkM5wuAM13pIqNbouE5X1oQDC', '2015-03-09 00:00:00', '2015-04-02 20:30:06', NULL, '2015-04-02 20:30:06'),
(8, 'tompan', 'tommy@franskaord.nu', 'Tommy', '$2y$10$cpx9CnzNzX7Q/9QUWy/kO.pwN1W5QN26Y2T5SLs23ECi4bML2FpC.', '2015-03-07 18:36:21', '2015-04-02 20:30:18', NULL, '2015-04-02 20:30:18'),
(9, 'nisse', 'nise@sdf-nu.nu', 'Nils', '$2y$10$qyETBoRuqp9bsbB52DogBuZpD4CbxF2D3QWAcbUOXVqmHblzEPzbG', '2015-03-11 00:00:00', '2015-04-02 20:30:37', NULL, '2015-04-02 20:30:37'),
(11, 'tommy', 'tommy@franskaord.org', 'Tommy', '$2y$10$NQjFDY9/NDegvE1tYp4mkeANlIKLIRZb6j.8FFYk5CVPq6PmQ6eES', '2015-03-06 00:00:00', '2015-04-02 20:30:52', NULL, '2015-04-02 20:30:52'),
(12, 'Marre', 'marja@trolldalen.se', 'Marja', '$2y$10$Ga8DLy3j3ux2r70tKs33G.Kz6rl.UtVqPFX/yviLSQF7kgEgMA5nW', '2015-03-01 00:00:00', '2015-04-02 20:31:14', NULL, '2015-04-02 20:31:14'),
(13, 'Mia', 'mia@epost.nu', 'Maria Hedlund', '$2y$10$yTOEplEhHQkAPHzxj64Gvu6moN5PC900H68ibAL5Tq1g945sGu3.u', '2015-03-17 17:33:15', '2015-04-02 20:31:42', NULL, '2015-04-02 20:31:42');


DROP TABLE IF EXISTS `boats_votes`;
CREATE TABLE IF NOT EXISTS `boats_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionid` int(11) DEFAULT NULL,
  `answerid` int(11) DEFAULT NULL,
  `commentid` int(11) DEFAULT NULL,
  `commentid_a` int(11) DEFAULT NULL,
  `votedby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

INSERT INTO `boats_votes` (`questionid`, `answerid`, `commentid`, `commentid_a`, `votedby`) VALUES
        (NULL, NULL, NULL, NULL, NULL)";

"CREATE OR REPLACE VIEW `boats_activeuser` AS (select `boats_comments`.`userid` AS `userid`,count(`boats_comments`.`userid`) AS `COUNT(boats_comments.userid)`,`boats_user`.`acronym` AS `acronym`,`boats_user`.`email` AS `email` from (`boats_comments` join `boats_user` on((`boats_comments`.`userid` = `boats_user`.`id`))) where `boats_comments`.`deleted` is null group by `boats_comments`.`userid` order by count(`boats_comments`.`userid`) desc) union all (select `boats_answers`.`userid` AS `userid`,count(`boats_answers`.`userid`) AS `COUNT(boats_answers.userid)`,`boats_user`.`acronym` AS `acronym`,`boats_user`.`email` AS `email` from (`boats_answers` join `boats_user` on((`boats_answers`.`userid` = `boats_user`.`id`))) where `boats_answers`.`deleted` is null group by `boats_answers`.`userid` order by count(`boats_answers`.`userid`) desc) union all (select `boats_questions`.`userid` AS `userid`,count(`boats_questions`.`userid`) AS `COUNT(boats_questions.userid)`,`boats_user`.`acronym` AS `acronym`,`boats_user`.`email` AS `email` from (`boats_questions` join `boats_user` on((`boats_questions`.`userid` = `boats_user`.`id`))) where `boats_questions`.`deleted` is null group by `boats_questions`.`userid` order by count(`boats_questions`.`userid`) desc);

