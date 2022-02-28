--
-- Структура таблицы `automatic_blocks`
--

CREATE TABLE `automatic_blocks` (
  `id` int NOT NULL,
  `ip` varchar(100) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `blocks`
--

CREATE TABLE `blocks` (
  `id` int NOT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_hostname` int NOT NULL DEFAULT '0',
  `is_block` int NOT NULL DEFAULT '0' COMMENT '0 - Разблокирован, 1 - Заблокирован',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `statistics`
--

CREATE TABLE `statistics` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL COMMENT 'Дата посещения',
  `ip` varchar(150) DEFAULT NULL COMMENT 'IP адрес',
  `visits` int NOT NULL DEFAULT '0' COMMENT 'Количество посещений',
  `requests` int NOT NULL DEFAULT '0' COMMENT 'Количество попыток оставить заявку',
  `visits_drops` int NOT NULL DEFAULT '0' COMMENT 'Количество блокированных посещений'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `visits`
--

CREATE TABLE `visits` (
  `id` int NOT NULL,
  `ip` varchar(150) DEFAULT NULL,
  `is_blocked` int NOT NULL DEFAULT '0' COMMENT '1 - Блокированный вход',
  `page` text,
  `method` varchar(50) DEFAULT NULL,
  `referer` text,
  `user_agent` text,
  `request_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `automatic_blocks`
--
ALTER TABLE `automatic_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_date` (`ip`,`date`);

--
-- Индексы таблицы `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `host_is_block` (`host`,`is_block`);

--
-- Индексы таблицы `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_ip` (`date`,`ip`);

--
-- Индексы таблицы `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `automatic_blocks`
--
ALTER TABLE `automatic_blocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
--
-- AUTO_INCREMENT для таблицы `blocks`
--
ALTER TABLE `blocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT для таблицы `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT для таблицы `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;