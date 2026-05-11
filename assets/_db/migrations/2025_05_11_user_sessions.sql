-- Migration : sessions utilisateur
-- 2025-05-11

CREATE TABLE IF NOT EXISTS `user_session` (
    `token`         VARCHAR(128)    NOT NULL,
    `user_id`       INT             NOT NULL,
    `ip`            VARCHAR(45)     NOT NULL DEFAULT '',
    `country`       VARCHAR(64)     DEFAULT NULL,
    `browser`       VARCHAR(50)     NOT NULL DEFAULT '',
    `os`            VARCHAR(50)     NOT NULL DEFAULT '',
    `device`        VARCHAR(10)     NOT NULL DEFAULT 'Desktop',
    `user_agent`    TEXT            NOT NULL,
    `created_at`    INT UNSIGNED    NOT NULL,
    `last_activity` INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`token`),
    KEY `idx_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
