
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- atos_currency
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `atos_currency`;

CREATE TABLE `atos_currency`
(
    `code` VARCHAR(128) NOT NULL,
    `atos_code` INTEGER,
    `decimals` INTEGER,
    PRIMARY KEY (`code`)
) ENGINE=InnoDB;

INSERT INTO `atos_currency`(`code`,`atos_code`,`decimals`) VALUES
('EUR', '978', 2),
('USD', '840', 2),
('CHF', '756', 2),
('GBP', '826', 2),
('CAD', '124', 2),
('JPY', '392', 0),
('MXN', '484', 2),
('TRY', '949', 2),
('AUD', '036', 2),
('NZD', '554', 2),
('NOK', '578', 2),
('BRL', '986', 2),
('ARS', '032', 2),
('KHR', '116', 2),
('TWD', '901', 2),
('SEK', '752', 2),
('DKK', '208', 2),
('KRW', '410', 0),
('SGD', '702', 2),
('XPF', '953', 2),
('XAF', '952', 2);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
