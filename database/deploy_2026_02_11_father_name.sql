-- Deploy: Add father_name to students table
-- Run this in phpMyAdmin if migration doesn't run automatically
-- Date: 2026-02-11

ALTER TABLE `students` ADD COLUMN `father_name` VARCHAR(255) NULL AFTER `full_name`;
