--
-- Database: `pisongs`
--
CREATE DATABASE IF NOT EXISTS `pisongs` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `pisongs`;

-- --------------------------------------------------------

--
-- Table structure for table `pisongs`
--

CREATE TABLE IF NOT EXISTS `pisongs` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `song_artist` varchar(150) NOT NULL,
  `song_title` varchar(150) NOT NULL,
  PRIMARY KEY (`song_id`),
  UNIQUE KEY `song_id_UNIQUE` (`song_id`),
  UNIQUE KEY `song` (`song_artist`,`song_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pivotes`
--

CREATE TABLE IF NOT EXISTS `pivotes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `song_id` int(11) NOT NULL,
  `vote_amount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `vote_id_UNIQUE` (`vote_id`),
  UNIQUE KEY `song_id` (`song_id`),
  KEY `song_fk_idx` (`song_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pivotes`
--
ALTER TABLE `pivotes`
  ADD CONSTRAINT `song_fk` FOREIGN KEY (`song_id`) REFERENCES `pisongs` (`song_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
