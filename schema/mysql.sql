CREATE TABLE windows_schema_migration (
  schema_version SMALLINT UNSIGNED NOT NULL,
  migration_time DATETIME NOT NULL,
  PRIMARY KEY(schema_version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_bin;

CREATE TABLE version(
  `schema` INT(3) NOT NULL default 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_list(
  `host_id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
	`host` VARCHAR(60) NOT NULL,
	`port` int(5) NOT NULL DEFAULT 5891,
	`address` VARCHAR(60) NOT NULL,
	`approved` int(1) NOT NULL DEFAULT 0,
	`os` VARCHAR(255) NOT NULL,
	`version` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`host_id`),
	UNIQUE KEY `host` (`host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_token_list(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `token` VARCHAR(100) NOT NULL,
  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id),
	UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE global_module_checks(
  `id` int(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(40) NOT NULL,
	`check_interval` int(10) NOT NULL default 60,
	`enabled` int(1) NOT NULL default 1,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_module_checks(
	`host_id` BIGINT(20) UNSIGNED NOT NULL,
	`name` VARCHAR(40) NOT NULL,
	`check_interval` int(10) NOT NULL default 60,
	`enabled` int(1) NOT NULL default 1,
	PRIMARY KEY (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_check_results(
	`host_id` BIGINT(20) UNSIGNED NOT NULL,
	`module` VARCHAR(40) NOT NULL DEFAULT '',
	`result` LONGTEXT NULL DEFAULT NULL,
	`timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE available_modules(
	`id` int(4) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_process_list(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `proc_name` VARCHAR(255) NOT NULL DEFAULT '',
  `proc_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `proc_priority` INT(2) UNSIGNED NOT NULL DEFAULT 0,
  `proc_threads` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `proc_processor_percent` DECIMAL(7,4) NOT NULL DEFAULT 0,
  `proc_processor_time` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `proc_pagefile` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `proc_used_memory` BIGINT(20) unsigned NOT NULL DEFAULT 0,
  `proc_required_memory` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `proc_cmd` LONGTEXT NULL DEFAULT NULL,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_pending_updates(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `name` TEXT NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `kbarticles` TEXT NULL DEFAULT NULL,
  `uninst_note` LONGTEXT NULL DEFAULT NULL,
  `support_url` VARCHAR(255) NOT NULL DEFAULT '',
  `require_reboot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `download_size` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `downloaded` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `superseded_ids` TEXT NULL DEFAULT NULL,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_update_history(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `name` TEXT NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `result` INT(1) NOT NULL,
  `support_url` VARCHAR(255) NOT NULL DEFAULT '',
  `installed_on` VARCHAR(100) NOT NULL DEFAULT '',
  `internal_type` INT(3) UNSIGNED NOT NULL DEFAULT 0,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_hotfix_history(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `id` VARCHAR(20) NOT NULL,
  `name` TEXT NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `status` TEXT NULL DEFAULT NULL,
  `install_date` VARCHAR(50) NOT NULL,
  `support_url` VARCHAR(255) NOT NULL DEFAULT '',
  `fix_comment` TEXT NULL DEFAULT NULL,
  `service_pack` TEXT NULL DEFAULT NULL,
  `installed_by` VARCHAR(100) NOT NULL DEFAULT '',
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_hardware_cpu(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `cpu_id` INT(3) NOT NULL DEFAULT 0,
  `architecture` INT(2) NOT NULL DEFAULT 0,
  `l2_cache_size` INT(5) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `processor_id` VARCHAR(255) NOT NULL DEFAULT '',
  `current_clock_speed` INT(6) NOT NULL DEFAULT 0,
  `max_clock_speed` INT(6) NOT NULL DEFAULT 0,
  `virtualization_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `number_cores` INT(3) NOT NULL DEFAULT 0,
  `number_logical_processors` INT(3) NOT NULL DEFAULT 0,
  `thread_count` INT(3) NOT NULL DEFAULT 1,
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  `caption` VARCHAR(255) NOT NULL DEFAULT '',
  `revision` INT(7) NOT NULL DEFAULT 0,
  `address_width` INT(2) NOT NULL DEFAULT 0,
  `family` INT(2) NOT NULL DEFAULT 0,
  `level` INT(2) NOT NULL DEFAULT 0,
  `cpu_status` INT(2) NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_hardware_memory(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `bank_label` VARCHAR(255) NOT NULL DEFAULT '',
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  `capacity` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `memory_tye` INT(3) UNSIGNED NOT NULL DEFAULT 0,
  `total_width` INT(6) UNSIGNED NOT NULL DEFAULT 0,
  `clock_speed` INT(7) UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NULL DEFAULT NULL,
  `tag` VARCHAR(255) NOT NULL DEFAULT '',
  `location` VARCHAR(255) NOT NULL DEFAULT '',
  `caption` VARCHAR(255) NOT NULL DEFAULT '',
  `serial` VARCHAR(255) NOT NULL DEFAULT '',
  `part_number` VARCHAR(255) NOT NULL DEFAULT '',
  `min_voltage` INT(6) UNSIGNED NOT NULL DEFAULT 0,
  `max_voltage` INT(6) UNSIGNED NOT NULL DEFAULT 0,
  `configured_voltage` INT(6) UNSIGNED NOT NULL DEFAULT 0,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_hardware_disk(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `disk_id` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `firmware_revision` VARCHAR(255) NOT NULL DEFAULT '',
  `drive_reference` VARCHAR(255) NOT NULL DEFAULT '',
  `caption` VARCHAR(255) NOT NULL DEFAULT '',
  `total_heads` INT(5) NOT NULL DEFAULT 0,
  `model` VARCHAR(255) NOT NULL DEFAULT '',
  `size` BIGINT(10) NOT NULL DEFAULT 0,
  `partitions` INT(2) NOT NULL DEFAULT 0,
  `serial_number` VARCHAR(255) NOT NULL DEFAULT '',
  `scsi_target_id` INT(2) NOT NULL DEFAULT 0,
  `scsi_logical_id` INT(2) NOT NULL DEFAULT 0,
  `scsi_port` INT(2) NOT NULL DEFAULT 0,
  `media_type` VARCHAR(255) NOT NULL DEFAULT '',
  `bytes_per_sector` INT(5) NOT NULL DEFAULT 0,
  `total_tracks` INT(8) NOT NULL DEFAULT 0,
  `total_cylinders` INT(8) NOT NULL DEFAULT 0,
  `scsi_bus` INT(1) NOT NULL DEFAULT 0,
  `signature` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `total_sectors` BIGINT(20) NOT NULL DEFAULT 0,
  `sectors_per_track` INT(8) NOT NULL DEFAULT 0,
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  `capabilities` TEXT NULL DEFAULT NULL,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_bios(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `embedded_controller_major_version` INT(6) NOT NULL DEFAULT 0,
  `embedded_controller_minor_version` INT(6) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `software_element_state` INT(3) NOT NULL DEFAULT 0,
  `smbios_major_version` INT(3) NOT NULL DEFAULT 0,
  `smbios_minor_version` INT(3) NOT NULL DEFAULT 0,
  `bios_characteristics` VARCHAR(255) NULL DEFAULT '',
  `system_bios_major_version` INT(3) NOT NULL DEFAULT 0,
  `system_bios_minor_version` INT(3) NULL DEFAULT 0,
  `version` VARCHAR(255) NOT NULL DEFAULT '',
  `smbios_version` VARCHAR(255) NOT NULL DEFAULT '',
  `primary_bios` TINYINT(1) NOT NULL DEFAULT 0,
  `smbios_present` TINYINT(1) NOT NULL DEFAULT 0,
  `current_language` VARCHAR(255) NOT NULL DEFAULT '',
  `available_languages` TEXT NULL DEFAULT NULL,
  `installable_languages` INT(2) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT '',
  `caption` VARCHAR(255) NOT NULL DEFAULT '',
  `release_date` VARCHAR(255) NOT NULL DEFAULT '',
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  `software_element_id` VARCHAR(255) NOT NULL DEFAULT '',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `serial_number` VARCHAR(255) NOT NULL DEFAULT '',
  `bios_version` TEXT NULL DEFAULT NULL,
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_system(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `system_device` VARCHAR(255) NOT NULL DEFAULT '',
  `os_language` INT(5) NOT NULL DEFAULT 0,
  `architecture` VARCHAR(255) NOT NULL DEFAULT '',
  `number_of_users` INT(5) NOT NULL DEFAULT 0,
  `root_dir` VARCHAR(255) NOT NULL DEFAULT '',
  `system_dir` VARCHAR(255) NOT NULL DEFAULT '',
  `os_type` INT(3) NOT NULL DEFAULT 0,
  `number_of_licensed_users` INT(6) NOT NULL DEFAULT 0,
  `system_name` VARCHAR(255) NOT NULL DEFAULT '',
  `build_type` VARCHAR(255) NOT NULL DEFAULT '',
  `service_pack_major_version` INT(5) NOT NULL DEFAULT 0,
  `service_pack_minor_version` INT(5) NOT NULL DEFAULT 0,
  `version` VARCHAR(255) NOT NULL DEFAULT '',
  `country_code` VARCHAR(255) NOT NULL DEFAULT '',
  `build_number` VARCHAR(255) NOT NULL DEFAULT '',
  `install_date` VARCHAR(255) NOT NULL DEFAULT '',
  `registered_user` VARCHAR(255) NOT NULL DEFAULT '',
  `serial_number` VARCHAR(255) NOT NULL DEFAULT '',
  `system_drive` VARCHAR(255) NOT NULL DEFAULT '',
  `os_sku` INT(4) NOT NULL DEFAULT 0,
  `local_datetime` VARCHAR(255) NOT NULL DEFAULT '',
  `caption` VARCHAR(255) NOT NULL DEFAULT '',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `encryption_level` INT(6) NOT NULL DEFAULT 0,
  `data_execution_prevention_available` TINYINT(1) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `boot_device` VARCHAR(255) NOT NULL DEFAULT '',
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  `code_set` VARCHAR(255) NOT NULL DEFAULT '',
  `name` TEXT NULL DEFAULT NULL,
  `languages` VARCHAR(255) NOT NULL DEFAULT '',
  `last_boot_time` VARCHAR(255) NOT NULL DEFAULT '',
  `locale` VARCHAR(255) NOT NULL DEFAULT '',
  INDEX (`host_id`),
	FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_perf_counter(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `category` VARCHAR(255) NOT NULL DEFAULT '',
  `instance` VARCHAR(255) NOT NULL DEFAULT '',
  `counter` VARCHAR(255) NOT NULL DEFAULT '',
  `raw_value` BIGINT(30) NOT NULL DEFAULT 0,
  `base_value` BIGINT(30) NOT NULL DEFAULT 0,
  `system_frequency` BIGINT(30) NOT NULL DEFAULT 0,
  `counter_frequency` BIGINT(30) NOT NULL DEFAULT 0,
  `counter_timestamp` BIGINT(30) NOT NULL DEFAULT 0,
  `timestamp` BIGINT(30) NOT NULL DEFAULT 0,
  `timestamp_100nsec` BIGINT(30) NOT NULL DEFAULT 0,
  `value` FLOAT(25,8) NOT NULL DEFAULT 0,
  `reference` VARCHAR(255) NOT NULL DEFAULT '',
  INDEX (`host_id`),
  FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_perf_counter_help(
  `counter` VARCHAR(255) NOT NULL DEFAULT '',
  `counter_type` BIGINT(30) NOT NULL DEFAULT 0,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `help` TEXT NULL DEFAULT NULL,
  INDEX (`counter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_interfaces(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `interface_index` INT(3) NOT NULL DEFAULT 0,
  `mac_address` VARCHAR(17) NOT NULL DEFAULT '',
  `physical_adapter` TINYINT(1) NOT NULL DEFAULT 0,
  `device_id` INT(3) NOT NULL DEFAULT 0,
  `net_connection_id` VARCHAR(255) NOT NULL DEFAULT '',
  `net_connection_status` INT(3) NOT NULL DEFAULT 0,
  `pnp_device_id` VARCHAR(255) NOT NULL DEFAULT '',
  `speed` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `service_name` VARCHAR(255) NOT NULL DEFAULT '',
  `network_addresses` TEXT NULL DEFAULT NULL,
  `adapter_type` VARCHAR(255) NOT NULL DEFAULT '',
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '',
  INDEX (`host_id`),
  FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE host_service_list(
  `host_id` BIGINT(20) UNSIGNED NOT NULL,
  `display_name` VARCHAR(255) NOT NULL DEFAULT '',
  `service_name` VARCHAR(255) NOT NULL DEFAULT '',
  `dependent_services` TEXT NULL DEFAULT NULL,
  `can_pause_and_continue` TINYINT(1) NOT NULL DEFAULT 0,
  `service_handle` TEXT NULL DEFAULT NULL,
  `depends_on` TEXT NULL DEFAULT NULL,
  `can_stop` TINYINT(1) NOT NULL DEFAULT 0,
  `service_type` INT(3) NOT NULL DEFAULT 0,
  `can_shutdown` TINYINT(1) NOT NULL DEFAULT 0,
  `status` INT(3) NOT NULL DEFAULT 0,
  `start_type` INT(3) NOT NULL DEFAULT 0,
  INDEX (`host_id`),
  FOREIGN KEY (host_id) REFERENCES host_list(host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO windows_schema_migration
    (schema_version, migration_time)
VALUES (3, NOW());