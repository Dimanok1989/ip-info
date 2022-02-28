--
-- Триггеры `queue_requests`
--
DELIMITER $$
CREATE TRIGGER `count_requests` AFTER INSERT ON `queue_requests` FOR EACH ROW BEGIN

IF EXISTS(SELECT * from `statistics` where `ip` = NEW.ip and `date` = DATE(NOW())) THEN

	UPDATE `statistics`
    SET `requests` = `requests` + 1
    WHERE ip = NEW.ip AND date = DATE(NOW())
    LIMIT 1;

ELSE

	INSERT INTO `statistics` SET
    `ip` = NEW.ip,
    `date` = DATE(NOW()),
    `requests` = 1;

END IF;

END
$$
DELIMITER ;