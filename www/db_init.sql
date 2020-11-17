
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--
-- База данных: `asterisk`
--
CREATE DATABASE IF NOT EXISTS `asterisk` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `asterisk`;

-- --------------------------------------------------------

--
-- Структура таблицы `alarm`
--

CREATE TABLE IF NOT EXISTS `alarm` (
  `id` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `filename` varchar(30) CHARACTER SET ucs2 COLLATE ucs2_unicode_ci NOT NULL,
  `flag` tinyint(1) NOT NULL,
  `company` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `callbacklog`
--

CREATE TABLE IF NOT EXISTS `callbacklog` (
  `id` int(11) NOT NULL,
  `time_1` datetime DEFAULT NULL,
  `time_2` datetime DEFAULT NULL,
  `callback_callid` char(64) DEFAULT NULL,
  `callid` char(64) DEFAULT NULL,
  `queuename` char(64) DEFAULT NULL,
  `number` char(32) DEFAULT NULL,
  `agent` char(64) DEFAULT NULL,
  `flag` char(64) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `alarm`
--
ALTER TABLE `alarm`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `filename` (`filename`);

--
-- Индексы таблицы `callbacklog`
--
ALTER TABLE `callbacklog`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `alarm`
--
ALTER TABLE `alarm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `callbacklog`
--
ALTER TABLE `callbacklog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
