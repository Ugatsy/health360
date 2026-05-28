/*
 Navicat Premium Data Transfer

 Source Server         : Localhost
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : health360

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 27/05/2026 13:36:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ai_responses
-- ----------------------------
DROP TABLE IF EXISTS `ai_responses`;
CREATE TABLE `ai_responses`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `symptom_entry_id` bigint UNSIGNED NOT NULL,
  `raw_ai_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `possible_explanations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `home_remedies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `when_to_see_doctor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `ai_risk_level` enum('emergency','high','medium','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `web_sources` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `reviewed_by_doctor_id` bigint UNSIGNED NULL DEFAULT NULL,
  `doctor_approved` tinyint(1) NOT NULL DEFAULT 0,
  `doctor_modified_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `doctor_reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ai_responses_reviewed_by_doctor_id_foreign`(`reviewed_by_doctor_id` ASC) USING BTREE,
  INDEX `ai_responses_symptom_entry_id_index`(`symptom_entry_id` ASC) USING BTREE,
  INDEX `ai_responses_ai_risk_level_index`(`ai_risk_level` ASC) USING BTREE,
  INDEX `ai_responses_doctor_approved_index`(`doctor_approved` ASC) USING BTREE,
  CONSTRAINT `ai_responses_reviewed_by_doctor_id_foreign` FOREIGN KEY (`reviewed_by_doctor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ai_responses_symptom_entry_id_foreign` FOREIGN KEY (`symptom_entry_id`) REFERENCES `symptom_entries` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for body_regions
-- ----------------------------
DROP TABLE IF EXISTS `body_regions`;
CREATE TABLE `body_regions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_region_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_medical` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `threejs_mesh_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bounding_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `icd10_code_prefix` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_critical_region` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `body_regions_parent_region_id_index`(`parent_region_id` ASC) USING BTREE,
  INDEX `body_regions_is_critical_region_index`(`is_critical_region` ASC) USING BTREE,
  CONSTRAINT `body_regions_parent_region_id_foreign` FOREIGN KEY (`parent_region_id`) REFERENCES `body_regions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for doctor_reviews
-- ----------------------------
DROP TABLE IF EXISTS `doctor_reviews`;
CREATE TABLE `doctor_reviews`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint UNSIGNED NOT NULL,
  `ai_response_id` bigint UNSIGNED NOT NULL,
  `review_decision` enum('approved','modified','rejected','flagged_for_human') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `review_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `modified_remedies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `modified_risk_level` enum('emergency','high','medium','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `modified_advice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `doctor_license_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `doctor_license_state` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `doctor_reviews_doctor_id_index`(`doctor_id` ASC) USING BTREE,
  INDEX `doctor_reviews_ai_response_id_index`(`ai_response_id` ASC) USING BTREE,
  INDEX `doctor_reviews_review_decision_index`(`review_decision` ASC) USING BTREE,
  CONSTRAINT `doctor_reviews_ai_response_id_foreign` FOREIGN KEY (`ai_response_id`) REFERENCES `ai_responses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `doctor_reviews_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for emergency_alerts
-- ----------------------------
DROP TABLE IF EXISTS `emergency_alerts`;
CREATE TABLE `emergency_alerts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `symptom_session_id` bigint UNSIGNED NOT NULL,
  `trigger_keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_symptom_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_taken` enum('displayed_emergency_message','sent_sms_alert','called_emergency_contact','displayed_911') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_notified` tinyint(1) NOT NULL DEFAULT 0,
  `emergency_contact_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `resolution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `emergency_alerts_symptom_session_id_foreign`(`symptom_session_id` ASC) USING BTREE,
  INDEX `emergency_alerts_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `emergency_alerts_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `emergency_alerts_symptom_session_id_foreign` FOREIGN KEY (`symptom_session_id`) REFERENCES `symptom_sessions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `emergency_alerts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for emergency_contacts
-- ----------------------------
DROP TABLE IF EXISTS `emergency_contacts`;
CREATE TABLE `emergency_contacts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `emergency_contacts_user_id_index`(`user_id` ASC) USING BTREE,
  CONSTRAINT `emergency_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for medical_profiles
-- ----------------------------
DROP TABLE IF EXISTS `medical_profiles`;
CREATE TABLE `medical_profiles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `has_heart_condition` tinyint(1) NOT NULL DEFAULT 0,
  `has_diabetes` tinyint(1) NOT NULL DEFAULT 0,
  `has_high_blood_pressure` tinyint(1) NOT NULL DEFAULT 0,
  `has_asthma` tinyint(1) NOT NULL DEFAULT 0,
  `has_autoimmune_disorder` tinyint(1) NOT NULL DEFAULT 0,
  `allergies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `current_medications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `consent_to_store_symptoms` tinyint(1) NOT NULL DEFAULT 0,
  `consent_to_ai_processing` tinyint(1) NOT NULL DEFAULT 0,
  `consent_to_share_with_doctor` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `medical_profiles_user_id_index`(`user_id` ASC) USING BTREE,
  CONSTRAINT `medical_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `notifications_notifiable_type_notifiable_id_index`(`notifiable_type` ASC, `notifiable_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for symptom_entries
-- ----------------------------
DROP TABLE IF EXISTS `symptom_entries`;
CREATE TABLE `symptom_entries`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `body_region_id` bigint UNSIGNED NOT NULL,
  `symptom_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pain_type` enum('sharp','dull','burning','throbbing','pressure') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pain_intensity` int NULL DEFAULT NULL,
  `pain_duration` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `additional_symptoms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `symptom_started_at` datetime NULL DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `symptom_entries_session_id_index`(`session_id` ASC) USING BTREE,
  INDEX `symptom_entries_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `symptom_entries_body_region_id_index`(`body_region_id` ASC) USING BTREE,
  INDEX `symptom_entries_pain_intensity_index`(`pain_intensity` ASC) USING BTREE,
  INDEX `symptom_entries_recorded_at_index`(`recorded_at` ASC) USING BTREE,
  CONSTRAINT `symptom_entries_body_region_id_foreign` FOREIGN KEY (`body_region_id`) REFERENCES `body_regions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `symptom_entries_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `symptom_sessions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `symptom_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for symptom_sessions
-- ----------------------------
DROP TABLE IF EXISTS `symptom_sessions`;
CREATE TABLE `symptom_sessions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `session_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','completed','abandoned','emergency_routed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `device_type` enum('web','ios','android') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `app_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `highest_risk_level` enum('emergency','high','medium','low','unknown') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `was_emergency_detected` tinyint(1) NOT NULL DEFAULT 0,
  `emergency_recommendation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `symptom_sessions_session_uuid_unique`(`session_uuid` ASC) USING BTREE,
  INDEX `symptom_sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `symptom_sessions_status_index`(`status` ASC) USING BTREE,
  INDEX `symptom_sessions_session_uuid_index`(`session_uuid` ASC) USING BTREE,
  CONSTRAINT `symptom_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_feedback
-- ----------------------------
DROP TABLE IF EXISTS `user_feedback`;
CREATE TABLE `user_feedback`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `ai_response_id` bigint UNSIGNED NOT NULL,
  `was_helpful` tinyint(1) NULL DEFAULT NULL,
  `was_accurate` tinyint(1) NULL DEFAULT NULL,
  `feedback_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `consulted_actual_doctor` tinyint(1) NOT NULL DEFAULT 0,
  `doctor_diagnosis_matched_ai` tinyint(1) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_feedback_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `user_feedback_ai_response_id_index`(`ai_response_id` ASC) USING BTREE,
  INDEX `user_feedback_was_helpful_index`(`was_helpful` ASC) USING BTREE,
  CONSTRAINT `user_feedback_ai_response_id_foreign` FOREIGN KEY (`ai_response_id`) REFERENCES `ai_responses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `date_of_birth` date NULL DEFAULT NULL,
  `biological_sex` enum('male','female','other','prefer_not_to_say') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','unknown') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `emergency_contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `emergency_contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `emergency_contact_relationship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `role` enum('patient','doctor','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'patient',
  `doctor_license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `doctor_specialty` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_uuid_unique`(`uuid` ASC) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  INDEX `users_email_index`(`email` ASC) USING BTREE,
  INDEX `users_uuid_index`(`uuid` ASC) USING BTREE,
  INDEX `users_role_index`(`role` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
