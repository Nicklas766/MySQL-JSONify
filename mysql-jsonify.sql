
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `MYSQL-TABLE-NAME` (
  `id` int(11) NOT NULL,
  `tableName` varchar(1800) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `MYSQL-TABLE-NAME` (`id`, `tableName`) VALUES
(1, 'MYSQL-TABLE-NAME'),
(2, 'Shop_Products'),
(3, 'users');


CREATE TABLE `Shop_Products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(500) COLLATE utf8_bin NOT NULL,
  `price` int(255) NOT NULL,
  `category` varchar(500) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `Shop_Products` (`id`, `user_id`, `product_name`, `price`, `category`) VALUES
(1, 1, 'Samsung Galaxy S7 Edge', 600, 'Mobile Phone'),
(2, 3, 'Google nexus', 450, 'Mobile Phone'),
(3, 2, 'Apple IPhone 6', 630, 'Mobile Phone'),
(4, 1, 'Sony Vio', 1200, 'Laptop'),
(5, 5, 'Samsung T.V', 900, 'T.V'),
(6, 4, 'Apple IPAD', 710, 'Tablet'),
(7, 1, 'MacBook Pro', 1000, 'Laptop'),
(8, 2, 'Dell Laptop', 950, 'Laptop'),
(9, 3, 'Canon EOS 700D DSLR Camera', 550, 'Camera'),
(10, 4, 'Nikon D7100 DSLR Camera', 670, 'Camera');


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `authority` varchar(120) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `users` (`id`, `username`, `password`, `authority`) VALUES
(1, 'Nicklas766', 'pass1', '0'),
(2, 'Rasmus Lerdorf', 'pass2', '0'),
(3, 'Jessica', 'pass3', '0'),
(4, 'Steve', 'pass4', '1'),
(5, 'Adam', 'pass5', '1');

ALTER TABLE `MYSQL-TABLE-NAME`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `Shop_Products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `MYSQL-TABLE-NAME`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `Shop_Products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
