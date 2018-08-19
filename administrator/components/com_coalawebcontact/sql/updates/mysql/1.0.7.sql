ALTER IGNORE TABLE `#__cwcontact_customfields` 
ADD COLUMN  `plugin_ids` varchar(255) NOT NULL,
ADD COLUMN  `email_location` tinyint(3) NOT NULL DEFAULT '1';