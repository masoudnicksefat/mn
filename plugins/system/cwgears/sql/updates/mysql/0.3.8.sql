--
-- Convert all tables to utf8mb4 character set with utf8mb4_unicode_ci collation
--
ALTER TABLE `#__cwgears` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__cwgears_schedule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Set default character set and collation for all tables
--
ALTER TABLE `#__cwgears` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__cwgears_schedule` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;