-- ============================================
-- GUARDEE PLATFORM - Database Schema
-- ============================================

-- --------------------------------------------
-- 1. ROLES
-- --------------------------------------------
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `label` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `roles` (`id`, `name`, `label`, `created_at`) VALUES
(1, 'admin', 'Platform Admin', NOW()),
(2, 'brand', 'Brand / Unternehmen', NOW()),
(3, 'influencer', 'Influencer / Creator', NOW()),
(4, 'influencer_manager', 'Influencer Manager', NOW()),
(5, 'agency', 'Agentur', NOW());

-- --------------------------------------------
-- 2. USERS
-- --------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT UNSIGNED NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `avatar` VARCHAR(500) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `status` ENUM('active','inactive','pending') DEFAULT 'pending',
  `email_verified_at` DATETIME DEFAULT NULL,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role_id`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 3. INFLUENCER PROFILES
-- --------------------------------------------
CREATE TABLE `influencer_profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `display_name` VARCHAR(150) NOT NULL,
  `bio` TEXT DEFAULT NULL,
  `niche` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(3) DEFAULT NULL,
  `instagram_handle` VARCHAR(100) DEFAULT NULL,
  `tiktok_handle` VARCHAR(100) DEFAULT NULL,
  `youtube_handle` VARCHAR(100) DEFAULT NULL,
  `followers_count` INT UNSIGNED DEFAULT 0,
  `engagement_rate` DECIMAL(5,2) DEFAULT NULL,
  `managed_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_influencer_niche` (`niche`),
  INDEX `idx_influencer_country` (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 4. BRAND PROFILES
-- --------------------------------------------
CREATE TABLE `brand_profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `company_name` VARCHAR(200) NOT NULL,
  `industry` VARCHAR(100) DEFAULT NULL,
  `website` VARCHAR(500) DEFAULT NULL,
  `logo` VARCHAR(500) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `managed_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 5. CAMPAIGNS
-- --------------------------------------------
CREATE TABLE `campaigns` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand_id` INT UNSIGNED NOT NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `budget` DECIMAL(12,2) NOT NULL,
  `target_audience` VARCHAR(500) DEFAULT NULL,
  `countries` VARCHAR(500) DEFAULT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `briefing_file` VARCHAR(500) DEFAULT NULL,
  `status` ENUM('draft','pending_approval','approved','active','completed','cancelled') DEFAULT 'draft',
  `approved_by` INT UNSIGNED DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`brand_id`) REFERENCES `brand_profiles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_campaigns_status` (`status`),
  INDEX `idx_campaigns_brand` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 6. CAMPAIGN INVITATIONS
-- --------------------------------------------
CREATE TABLE `campaign_invitations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` INT UNSIGNED NOT NULL,
  `influencer_id` INT UNSIGNED NOT NULL,
  `invited_by` INT UNSIGNED NOT NULL,
  `status` ENUM('pending','interested','declined','withdrawn') DEFAULT 'pending',
  `responded_at` DATETIME DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`influencer_id`) REFERENCES `influencer_profiles`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`invited_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY `uk_campaign_influencer` (`campaign_id`, `influencer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 7. OFFERS
-- --------------------------------------------
CREATE TABLE `offers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` INT UNSIGNED NOT NULL,
  `invitation_id` INT UNSIGNED NOT NULL,
  `brand_id` INT UNSIGNED NOT NULL,
  `influencer_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'EUR',
  `message` TEXT DEFAULT NULL,
  `status` ENUM('pending','accepted','declined','revised') DEFAULT 'pending',
  `terms_accepted` TINYINT(1) DEFAULT 0,
  `terms_accepted_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`invitation_id`) REFERENCES `campaign_invitations`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`brand_id`) REFERENCES `brand_profiles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`influencer_id`) REFERENCES `influencer_profiles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 8. ASSETS (Content Files)
-- --------------------------------------------
CREATE TABLE `assets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` INT UNSIGNED NOT NULL,
  `offer_id` INT UNSIGNED DEFAULT NULL,
  `uploaded_by` INT UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `asset_type` ENUM('briefing','content','revision') DEFAULT 'content',
  `status` ENUM('pending_review','approved','revision_requested','replaced') DEFAULT 'pending_review',
  `reviewed_by` INT UNSIGNED DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `review_notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_assets_campaign` (`campaign_id`),
  INDEX `idx_assets_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 9. APPROVALS (polymorphic)
-- --------------------------------------------
CREATE TABLE `approvals` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `approvable_type` VARCHAR(50) NOT NULL,
  `approvable_id` INT UNSIGNED NOT NULL,
  `approved_by` INT UNSIGNED NOT NULL,
  `action` ENUM('approved','rejected') NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_approvals_entity` (`approvable_type`, `approvable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 10. ACTIVITY LOGS
-- --------------------------------------------
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) DEFAULT NULL,
  `entity_id` INT UNSIGNED DEFAULT NULL,
  `metadata` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  INDEX `idx_logs_user` (`user_id`),
  INDEX `idx_logs_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------
-- 11. NOTIFICATIONS
-- --------------------------------------------
CREATE TABLE `notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `link` VARCHAR(500) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `read_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_notifications_user` (`user_id`),
  INDEX `idx_notifications_unread` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ============================================
-- SEED DATA - Test Users
-- Password for all: demo123 (bcrypt)
-- ============================================

-- BCrypt hash of 'demo123'
SET @pw = '$2y$12$o9VYg.ciWMfEX7uMQxJbAOWKuL5kwPdNqglT/oCiOt2O/jC10mpw2';

-- 1. Admin
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `status`, `email_verified_at`, `created_at`) VALUES
(1, 1, 'admin@guardee.io', @pw, 'Platform', 'Admin', 'active', NOW(), NOW());

-- 2. Brand
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `status`, `email_verified_at`, `created_at`) VALUES
(2, 2, 'brand@demo.com', @pw, 'Demo', 'Brand', 'active', NOW(), NOW());

INSERT INTO `brand_profiles` (`user_id`, `company_name`, `industry`, `website`, `created_at`) VALUES
(2, 'Demo Brand GmbH', 'Fashion', 'https://demo-brand.com', NOW());

-- 3. Influencer
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `status`, `email_verified_at`, `created_at`) VALUES
(3, 3, 'influencer@demo.com', @pw, 'Demo', 'Creator', 'active', NOW(), NOW());

INSERT INTO `influencer_profiles` (`user_id`, `display_name`, `bio`, `niche`, `country`, `instagram_handle`, `followers_count`, `engagement_rate`, `created_at`) VALUES
(3, 'DemoCreator', 'Lifestyle & Fashion Creator', 'Fashion', 'DE', '@democreator', 150000, 3.50, NOW());

-- 4. Influencer Manager
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `status`, `email_verified_at`, `created_at`) VALUES
(4, 4, 'manager@demo.com', @pw, 'Demo', 'Manager', 'active', NOW(), NOW());

-- 5. Agency
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `status`, `email_verified_at`, `created_at`) VALUES
(5, 5, 'agency@demo.com', @pw, 'Demo', 'Agency', 'active', NOW(), NOW());
