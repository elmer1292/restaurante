-- Migration: add is_food column to categorias
-- Created: 2025-10-16
-- Purpose: Add a lightweight boolean column to classify categories as food (1) or drink (0)

-- IMPORTANT: make a backup before running!
-- Backup example (Windows PowerShell):
-- mysqldump -u root -p rest_bar categorias > backups/backup_categorias_20251016.sql

START TRANSACTION;

-- Add column (tinyint used for maximum compatibility)
ALTER TABLE categorias
  ADD COLUMN is_food TINYINT(1) NOT NULL DEFAULT 1;

-- Optional: mark common beverage categories as drink (0)
-- Edit the list below to match your actual category names (case-insensitive)
UPDATE categorias
SET is_food = 0
WHERE LOWER(Nombre_Categoria) IN ('bebidas','licores','cockteles','cervezas','vino','cocteles');

COMMIT;

-- Rollback (if needed):
-- ALTER TABLE categorias DROP COLUMN is_food;
