ALTER TABLE host_hardware_disk
    MODIFY COLUMN total_sectors BIGINT(20) NOT NULL DEFAULT 0;

INSERT INTO windows_schema_migration
    (schema_version, migration_time)
VALUES (3, NOW());
