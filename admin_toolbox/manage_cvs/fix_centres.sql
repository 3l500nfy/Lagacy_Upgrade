-- First, backup the existing data
CREATE TABLE tblcentres_backup AS SELECT * FROM tblcentres;

-- Drop the existing table
DROP TABLE IF EXISTS tblcentres;

-- Recreate the table with proper constraints
CREATE TABLE `tblcentres` (
  `centreid` int(11) NOT NULL AUTO_INCREMENT,
  `centrename` varchar(100) NOT NULL,
  `centrelocation` varchar(255) DEFAULT NULL,
  `centrecode` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`centreid`),
  UNIQUE KEY `centrename` (`centrename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert the data back with new IDs
INSERT INTO tblcentres (centrename, centrelocation, centrecode, status, created_at)
SELECT centrename, centrelocation, centrecode, status, created_at
FROM tblcentres_backup;

-- Update the venue table to use the new centre IDs
UPDATE tblvenue v
JOIN tblcentres c ON v.centreid = 0 AND c.centrename = 'Centre1'
SET v.centreid = c.centreid
WHERE v.centreid = 0;

-- Drop the backup table
DROP TABLE IF EXISTS tblcentres_backup; 