-- =====================================================
-- DATABASE: pcc_inventory
-- FULL IMPLEMENTATION WITH ALL FOREIGN KEYS
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- SYSTEM TABLES (Laravel internal)
-- =====================================================

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default users
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_admin`) VALUES
(1, 'Test User', 'test@example.com', '2026-04-14 21:35:41', '$2y$12$7r5pJQYMIBZoXlWYWdqJ8.I9Frjut8Puk4Ur2aN6uc98gE.kQQisC', 'z3UcoBvX1y', '2026-04-14 21:35:42', '2026-04-14 21:35:42', 0),
(2, 'Admin', 'admin@pcclsu.com', '2026-04-14 21:35:42', '$2y$12$HX0fb32QzClbl8/Sh5pSYOa5Sv1iuAsYMUvFKlKn2EXZ0ec8JRGxm', 'paT9s7BVmi', '2026-04-14 21:35:42', '2026-04-14 21:35:42', 1);

-- Insert migrations
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_24_081607_add_is_admin_to_users_table', 1),
(5, '2026_02_24_081609_create_products_table', 1),
(6, '2026_02_24_081610_create_items_table', 1),
(7, '2026_02_25_000000_create_sessions_table', 1),
(8, '2026_02_25_010000_add_default_quantity_to_items_table', 1),
(9, '2026_02_25_020000_add_image_to_items_table', 1),
(10, '2026_02_25_072924_add_default_quantity_to_items_table', 1),
(11, '2026_03_02_004814_add_image_to_products_table', 1),
(12, '2026_03_05_115925_add_new_columns_to_products_table', 1),
(13, '2026_03_11_033621_create_categories_table', 1),
(14, '2026_03_11_033622_create_ingredients_table', 1),
(15, '2026_03_24_073600_add_inventory_tracking_to_ingredients_table', 1),
(16, '2026_03_26_032903_add_ending_inventory_to_ingredients_table', 1),
(17, '2026_03_27_090800_create_inventory_trackings_table', 1),
(18, '2026_04_13_000001_add_container_size_to_products_table', 1),
(19, '2026_04_13_000002_create_production_schedules_table', 1),
(22, '2026_04_16_010124_create_flavor_layouts_table', 4),
(23, '2026_04_16_020111_add_type_to_products_table', 5),
(24, '2026_04_16_022324_drop_add_overalls_table', 6),
(26, '2026_04_16_023835_truncate_products_and_schedules', 7),
(27, '2026_02_26_120000_create_item_product_table', 8),
(28, '2026_04_21_070000_create_column_configs_table', 9),
(29, '2026_04_21_081702_create_product_flavors_table', 10),
(31, '2026_04_21_084813_add_batch_quantities_to_product_flavors_table', 11),
(32, '2026_04_23_160001_alter_products_table_for_new_schema', 12),
(33, '2026_04_23_160002_alter_product_flavors_table_for_new_schema', 12),
(34, '2026_04_23_160003_alter_ingredients_table_for_new_schema', 12),
(35, '2026_04_23_160004_create_product_sizes_table', 12),
(36, '2026_04_23_160005_create_ingredient_batches_table', 12),
(37, '2026_04_23_160006_create_product_recipe_items_table', 12),
(38, '2026_04_23_160007_alter_production_schedules_table_for_new_schema', 12),
(39, '2026_04_23_160008_create_inventory_transactions_table', 12),
(40, '2026_04_23_160009_create_production_consumption_table', 12),
(41, '2026_04_23_160010_create_current_ingredient_stock_view', 12);

-- =====================================================
-- COLUMN CONFIGS TABLE (App-specific config - KEEP)
-- =====================================================

CREATE TABLE IF NOT EXISTS `column_configs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `column_name` varchar(255) NOT NULL,
  `column_label` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `column_configs_type_column_name_unique` (`type`,`column_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `column_configs` (`id`, `type`, `column_name`, `column_label`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bottle', 'batches', 'Batcheasdfas', 1, 1, '2026-04-20 23:01:45', '2026-04-20 23:11:53'),
(2, 'Bottle', '200ml', '200ml', 2, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(3, 'Bottle', '500ml', '500ml', 3, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(4, 'Bottle', 'sarmiento', 'justine', 4, 1, '2026-04-20 23:01:45', '2026-04-20 23:14:52'),
(5, 'Sachet', 'batch', 'Batch', 1, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(6, 'Sachet', '50ml', '50ml', 2, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(7, 'Sachet', '100ml', '100ml', 3, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(8, 'Cup', 'batch', 'Batch', 1, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(9, 'Cup', '150ml', '150ml', 2, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(10, 'Cup', '250ml', '250ml', 3, 1, '2026-04-20 23:01:45', '2026-04-20 23:01:45'),
(11, 'Cup', '350ml', '350ml', 4, 1, '2026-04-20 23:06:07', '2026-04-20 23:06:07'),
(13, 'Bottle', 'yogurt', 'ahey', 5, 1, '2026-04-21 18:34:55', '2026-04-21 18:34:55');

-- =====================================================
-- 1. CATEGORIES TABLE (Para sa grouping ng ingredients)
-- =====================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Category name e.g., Fruits, Vegetables, Dairy',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'URL-friendly name e.g., fruits, vegetables',
    `description` TEXT NULL COMMENT 'Optional category description',
    `color` VARCHAR(255) NULL COMMENT 'Color code for UI (#FF5733)',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. PRODUCTS TABLE (Master products list)
-- =====================================================
CREATE TABLE IF NOT EXISTS `products` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `code` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Product code/SKU (P-001)',
    `name` VARCHAR(255) NOT NULL COMMENT 'Product name (Orange Juice)',
    `category` VARCHAR(255) NOT NULL COMMENT 'Product category (Juice, Soda, Water)',
    `type` VARCHAR(255) NULL COMMENT 'Product type (Carbonated, Non-carbonated)',
    `unit` VARCHAR(255) NOT NULL COMMENT 'Unit of sale (bottle, can, pack)',
    `container_size_ml` INT NULL COMMENT 'Default container size in ml',
    `description` TEXT NULL COMMENT 'Product description',
    `image` VARCHAR(255) NULL COMMENT 'Product image path',
    `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Is product still available?',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. PRODUCT FLAVORS TABLE (Mga flavors ng bawat product)
-- =====================================================
CREATE TABLE IF NOT EXISTS `product_flavors` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to products table',
    `flavor_name` VARCHAR(255) NOT NULL COMMENT 'Flavor name (Orange, Mango, Grape)',
    `measurement` VARCHAR(255) NOT NULL COMMENT 'Unit of measurement (ml, L, oz)',
    `ingredients_text` TEXT NULL COMMENT 'Text description of ingredients',
    `batch` INT(11) NOT NULL DEFAULT 0 COMMENT 'Batch number tracking',
    `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Is this flavor still available?',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. PRODUCT SIZES TABLE (Iba't ibang laki ng bawat flavor)
-- =====================================================
CREATE TABLE IF NOT EXISTS `product_sizes` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `product_flavor_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to product_flavors',
    `size_ml` INT NOT NULL COMMENT 'Size in milliliters (200, 500, 1000, 1500)',
    `price` DECIMAL(10,2) NULL COMMENT 'Selling price for this size',
    `sku` VARCHAR(255) NULL COMMENT 'Size-specific SKU (JUC-OR-500ML)',
    `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Is this size available?',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_flavor_id`) REFERENCES `product_flavors`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_flavor_size` (`product_flavor_id`, `size_ml`) COMMENT 'Same flavor cannot have duplicate sizes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. INGREDIENTS TABLE (Raw materials / sangkap)
-- =====================================================
CREATE TABLE IF NOT EXISTS `ingredients` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Ingredient name (Sugar, Water, Orange Extract)',
    `sku` VARCHAR(255) UNIQUE NULL COMMENT 'Ingredient SKU (ING-SUG-001)',
    `category_id` BIGINT UNSIGNED NULL COMMENT 'Foreign key to categories',
    `unit_of_measurement` VARCHAR(255) NOT NULL DEFAULT 'kg' COMMENT 'Unit (kg, L, pcs, g, ml)',
    `minimum_stock` DECIMAL(10,2) DEFAULT 0 COMMENT 'Minimum stock before reorder',
    `cost_per_unit` DECIMAL(10,2) NULL COMMENT 'Current average cost per unit',
    `supplier` VARCHAR(255) NULL COMMENT 'Main supplier name',
    `location` VARCHAR(255) NULL COMMENT 'Storage location (Warehouse A, Shelf 3)',
    `description` TEXT NULL COMMENT 'Ingredient description',
    `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Is this ingredient still used?',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. INGREDIENT BATCHES TABLE (Batch tracking with expiry)
-- =====================================================
CREATE TABLE IF NOT EXISTS `ingredient_batches` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `ingredient_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to ingredients',
    `batch_number` VARCHAR(255) NOT NULL COMMENT 'Batch number from supplier (BATCH-2024-001)',
    `quantity` DECIMAL(10,2) NOT NULL COMMENT 'Original quantity received',
    `remaining_quantity` DECIMAL(10,2) NOT NULL COMMENT 'Quantity still available',
    `cost_per_unit` DECIMAL(10,2) NOT NULL COMMENT 'Cost for this specific batch',
    `received_date` DATE NOT NULL COMMENT 'Date received',
    `expiry_date` DATE NULL COMMENT 'Expiry date (if applicable)',
    `supplier` VARCHAR(255) NULL COMMENT 'Supplier for this batch',
    `status` ENUM('available', 'partial', 'expired', 'depleted') DEFAULT 'available' COMMENT 'Current batch status',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE CASCADE,
    INDEX (`expiry_date`),
    INDEX (`status`),
    INDEX (`batch_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. PRODUCT RECIPE ITEMS (Bill of Materials - BOM)
-- =====================================================
CREATE TABLE IF NOT EXISTS `product_recipe_items` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `product_flavor_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to product_flavors',
    `ingredient_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to ingredients',
    `quantity_required` DECIMAL(10,2) NOT NULL COMMENT 'Dami ng ingredient per 1 unit of product',
    `unit_of_measurement` VARCHAR(255) NOT NULL COMMENT 'Unit for this ingredient in recipe',
    `waste_percentage` DECIMAL(5,2) DEFAULT 0 COMMENT 'Expected waste percentage (e.g., 5% spoilage)',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_flavor_id`) REFERENCES `product_flavors`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT,
    UNIQUE KEY `unique_recipe` (`product_flavor_id`, `ingredient_id`) COMMENT 'One ingredient per flavor only once'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. INVENTORY TRANSACTIONS TABLE (Lahat ng galaw ng stock)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `ingredient_batch_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to ingredient_batches',
    `transaction_type` ENUM('received', 'released', 'adjusted', 'wasted', 'returned') NOT NULL COMMENT 'Type of transaction',
    `quantity` DECIMAL(10,2) NOT NULL COMMENT 'Quantity affected',
    `reference_type` VARCHAR(255) NULL COMMENT 'What caused this? (production_schedule, purchase_order, adjustment)',
    `reference_id` BIGINT UNSIGNED NULL COMMENT 'ID of the reference (production_schedule.id, etc.)',
    `previous_balance` DECIMAL(10,2) NOT NULL COMMENT 'Balance before this transaction',
    `new_balance` DECIMAL(10,2) NOT NULL COMMENT 'Balance after this transaction',
    `notes` TEXT NULL COMMENT 'Additional notes',
    `created_by` BIGINT UNSIGNED NULL COMMENT 'User who created this transaction',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`ingredient_batch_id`) REFERENCES `ingredient_batches`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX (`transaction_type`),
    INDEX (`reference_type`, `reference_id`),
    INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. PRODUCTION SCHEDULES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `production_schedules` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `product_flavor_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to product_flavors',
    `product_size_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to product_sizes',
    `production_date` DATE NOT NULL COMMENT 'Target production date',
    `batch_quantity` INT NOT NULL COMMENT 'Number of units to produce',
    `status` ENUM('planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'planned' COMMENT 'Production status',
    `actual_start_date` DATETIME NULL COMMENT 'When production actually started',
    `actual_end_date` DATETIME NULL COMMENT 'When production actually finished',
    `notes` TEXT NULL COMMENT 'Production notes',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_flavor_id`) REFERENCES `product_flavors`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_size_id`) REFERENCES `product_sizes`(`id`) ON DELETE CASCADE,
    INDEX (`production_date`),
    INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. PRODUCTION CONSUMPTION TABLE (Actual ingredients used)
-- =====================================================
CREATE TABLE IF NOT EXISTS `production_consumption` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `production_schedule_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to production_schedules',
    `ingredient_batch_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to ingredient_batches (kung saang batch kinuha)',
    `expected_quantity` DECIMAL(10,2) NOT NULL COMMENT 'Dapat na dami base sa recipe',
    `actual_quantity` DECIMAL(10,2) NOT NULL COMMENT 'Actual na nagamit',
    `waste_quantity` DECIMAL(10,2) DEFAULT 0 COMMENT 'Nasayang na dami',
    `notes` TEXT NULL COMMENT 'Notes about usage',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`production_schedule_id`) REFERENCES `production_schedules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ingredient_batch_id`) REFERENCES `ingredient_batches`(`id`) ON DELETE RESTRICT,
    INDEX (`production_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. CURRENT STOCK VIEW (Para madaling malaman ang current stock)
-- =====================================================
CREATE OR REPLACE VIEW `current_ingredient_stock` AS
SELECT 
    i.id AS ingredient_id,
    i.name AS ingredient_name,
    i.sku,
    i.unit_of_measurement,
    i.minimum_stock,
    COALESCE(SUM(ib.remaining_quantity), 0) AS current_stock,
    CASE 
        WHEN COALESCE(SUM(ib.remaining_quantity), 0) <= 0 THEN 'out_of_stock'
        WHEN COALESCE(SUM(ib.remaining_quantity), 0) <= i.minimum_stock THEN 'low_stock'
        ELSE 'in_stock'
    END AS status,
    COUNT(ib.id) AS active_batches_count,
    MIN(ib.expiry_date) AS nearest_expiry_date
FROM ingredients i
LEFT JOIN ingredient_batches ib ON i.id = ib.ingredient_id AND ib.status IN ('available', 'partial')
GROUP BY i.id, i.name, i.sku, i.unit_of_measurement, i.minimum_stock;

-- =====================================================
-- 12. SAMPLE DATA (Para ma-test mo agad)
-- =====================================================

-- Insert categories
INSERT INTO `categories` (`name`, `slug`, `description`, `color`) VALUES
('Fruits', 'fruits', 'Fresh and concentrated fruits', '#FF5733'),
('Sweeteners', 'sweeteners', 'Sugar, honey, artificial sweeteners', '#33FF57'),
('Acids', 'acids', 'Citric acid, ascorbic acid', '#3357FF'),
('Preservatives', 'preservatives', 'Preservatives and stabilizers', '#FF33F5'),
('Water', 'water', 'Purified water', '#33FFF5');

-- Insert products
INSERT INTO `products` (`code`, `name`, `category`, `type`, `unit`, `container_size_ml`, `description`, `is_active`) VALUES
('JUC-001', 'Orange Juice', 'Juice', 'Fresh', 'bottle', 1000, 'Freshly squeezed orange juice', 1),
('JUC-002', 'Apple Juice', 'Juice', 'Fresh', 'bottle', 1000, 'Fresh apple juice', 1),
('JUC-003', 'Mango Juice', 'Juice', 'Fresh', 'bottle', 1000, 'Sweet mango juice', 1);

-- Insert product flavors
INSERT INTO `product_flavors` (`product_id`, `flavor_name`, `measurement`, `batch`, `is_active`) VALUES
(1, 'Orange', 'ml', 1, 1),
(1, 'Orange with Pulp', 'ml', 1, 1),
(2, 'Apple', 'ml', 1, 1),
(3, 'Mango', 'ml', 1, 1);

-- Insert product sizes
INSERT INTO `product_sizes` (`product_flavor_id`, `size_ml`, `price`, `sku`, `is_active`) VALUES
(1, 200, 25.00, 'JUC-OR-200ML', 1),
(1, 500, 55.00, 'JUC-OR-500ML', 1),
(1, 1000, 99.00, 'JUC-OR-1000ML', 1),
(2, 500, 60.00, 'JUC-ORP-500ML', 1),
(3, 500, 55.00, 'JUC-AP-500ML', 1),
(4, 500, 65.00, 'JUC-MG-500ML', 1);

-- Insert ingredients
INSERT INTO `ingredients` (`name`, `sku`, `category_id`, `unit_of_measurement`, `minimum_stock`, `cost_per_unit`, `supplier`, `location`, `description`, `is_active`) VALUES
('Orange Concentrate', 'ING-ORC-001', 1, 'kg', 50, 250.00, 'Citrus Suppliers Inc.', 'Warehouse A - Shelf 1', 'Frozen orange concentrate', 1),
('Sugar', 'ING-SUG-001', 2, 'kg', 100, 45.00, 'Sugar Corp.', 'Warehouse B - Shelf 2', 'White refined sugar', 1),
('Citric Acid', 'ING-CIT-001', 3, 'kg', 10, 120.00, 'Acid Chemicals Co.', 'Warehouse A - Shelf 3', 'Food grade citric acid', 1),
('Purified Water', 'ING-WTR-001', 5, 'L', 500, 5.00, 'Water Systems Inc.', 'Tank 1', 'RO purified water', 1),
('Sodium Benzoate', 'ING-PRV-001', 4, 'kg', 5, 350.00, 'Preserve It Corp.', 'Warehouse A - Shelf 4', 'Food preservative', 1);

-- Insert ingredient batches
INSERT INTO `ingredient_batches` (`ingredient_id`, `batch_number`, `quantity`, `remaining_quantity`, `cost_per_unit`, `received_date`, `expiry_date`, `supplier`, `status`) VALUES
(1, 'ORC-2024-001', 500, 500, 250.00, '2024-01-15', '2024-12-15', 'Citrus Suppliers Inc.', 'available'),
(2, 'SUG-2024-001', 1000, 1000, 45.00, '2024-01-10', '2025-01-10', 'Sugar Corp.', 'available'),
(3, 'CIT-2024-001', 100, 100, 120.00, '2024-01-05', '2025-01-05', 'Acid Chemicals Co.', 'available'),
(4, 'WTR-2024-001', 10000, 10000, 5.00, '2024-01-01', NULL, 'Water Systems Inc.', 'available'),
(5, 'PRV-2024-001', 50, 50, 350.00, '2024-01-20', '2025-01-20', 'Preserve It Corp.', 'available');

-- Insert product recipe items (Orange Flavor - 1L = 1 unit)
INSERT INTO `product_recipe_items` (`product_flavor_id`, `ingredient_id`, `quantity_required`, `unit_of_measurement`, `waste_percentage`) VALUES
(1, 1, 0.15, 'kg', 2.00),  -- 150g orange concentrate per liter
(1, 2, 0.10, 'kg', 1.00),  -- 100g sugar per liter
(1, 3, 0.002, 'kg', 0.50), -- 2g citric acid per liter
(1, 4, 0.85, 'L', 1.00),   -- 850ml water per liter
(1, 5, 0.001, 'kg', 0.50); -- 1g preservative per liter

-- Insert production schedule
INSERT INTO `production_schedules` (`product_flavor_id`, `product_size_id`, `production_date`, `batch_quantity`, `status`) VALUES
(1, 3, CURDATE(), 100, 'planned');  -- Produce 100 units of Orange 1000ml

-- =====================================================
-- 13. VERIFY ALL FOREIGN KEYS (Tingnan kung tama ang relationships)
-- =====================================================

-- Query to see all foreign keys
SELECT 
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.CONSTRAINT_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
WHERE kcu.TABLE_SCHEMA = 'pcc_inventory'
  AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY kcu.TABLE_NAME, kcu.COLUMN_NAME;

-- =====================================================
-- 14. SAMPLE QUERIES PARA MA-TEST
-- =====================================================

-- Query 1: Show all products with their flavors and sizes
SELECT 
    p.name AS product_name,
    pf.flavor_name,
    ps.size_ml,
    ps.price
FROM products p
JOIN product_flavors pf ON p.id = pf.product_id
JOIN product_sizes ps ON pf.id = ps.product_flavor_id
ORDER BY p.name, pf.flavor_name, ps.size_ml;

-- Query 2: Show recipe for a specific product flavor
SELECT 
    pf.flavor_name,
    i.name AS ingredient_name,
    pri.quantity_required,
    pri.unit_of_measurement,
    pri.waste_percentage
FROM product_flavors pf
JOIN product_recipe_items pri ON pf.id = pri.product_flavor_id
JOIN ingredients i ON pri.ingredient_id = i.id
WHERE pf.flavor_name = 'Orange'
ORDER BY pri.quantity_required DESC;

-- Query 3: Check current stock levels
SELECT * FROM current_ingredient_stock;

-- Query 4: Calculate required ingredients for a production schedule
SELECT 
    ps.id AS schedule_id,
    pf.flavor_name,
    ps.batch_quantity AS units_to_produce,
    i.name AS ingredient_name,
    pri.quantity_required * ps.batch_quantity AS total_required,
    i.unit_of_measurement
FROM production_schedules ps
JOIN product_flavors pf ON ps.product_flavor_id = pf.id
JOIN product_recipe_items pri ON pf.id = pri.product_flavor_id
JOIN ingredients i ON pri.ingredient_id = i.id
WHERE ps.id = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
