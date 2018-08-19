ALTER IGNORE TABLE `#__cwtraffic` 
ADD COLUMN `iphash` BINARY(20) NULL,
ADD COLUMN `continent_code` varchar(50) NULL,
ADD COLUMN `location_latitude` decimal(10,8) NULL,
ADD COLUMN `location_longitude` decimal(11,8) NULL,
ADD COLUMN `location_time_zone` varchar(50) NULL,
ADD COLUMN `useragent` varchar(1024) NULL,
ADD KEY `idx_ip` (`ip`),
ADD KEY `idx_iphash` (`iphash`),
ADD KEY `idx_tm` (`tm`),
ADD KEY `idx_iptm` (`ip`,`tm`),
ADD KEY `idx_iphashtm` (`iphash`,`tm`),
ADD UNIQUE KEY `unique_tm_iphash` (`tm`, `iphash`);