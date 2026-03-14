-- ============================================================
--  EduShield — Student Safety & Attendance System
--  Database: edushield
--  Compatible: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

CREATE DATABASE IF NOT EXISTS edushield CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edushield;

-- ──────────────────────────────────────────────
--  ADMIN USERS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(80)  NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  full_name   VARCHAR(120) NOT NULL,
  role        ENUM('super_admin','admin','teacher','security') DEFAULT 'admin',
  email       VARCHAR(150),
  last_login  DATETIME,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: admin / admin123
INSERT INTO admin_users (username, password, full_name, role, email) VALUES
('admin',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin', 'admin@school.edu'),
('teacher1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Mwangi',           'teacher',     'j.mwangi@school.edu'),
('security','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Security Officer',      'security',    'security@school.edu');

-- ──────────────────────────────────────────────
--  CLASSES
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS classes (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(50) NOT NULL UNIQUE,
  stream     VARCHAR(10),
  teacher_id INT,
  capacity   INT DEFAULT 40,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO classes (class_name, stream, capacity) VALUES
('Form 1A','A',40),('Form 1B','B',40),('Form 1C','C',38),('Form 1D','D',36),
('Form 2A','A',42),('Form 2B','B',40),('Form 2C','C',39),('Form 2D','D',37),
('Form 3A','A',41),('Form 3B','B',40),('Form 3C','C',38),
('Form 4A','A',43),('Form 4B','B',41),('Form 4C','C',39);

-- ──────────────────────────────────────────────
--  STUDENTS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  reg_number      VARCHAR(30)  NOT NULL UNIQUE,
  full_name       VARCHAR(120) NOT NULL,
  class_id        INT,
  gender          ENUM('Male','Female','Other') DEFAULT 'Male',
  date_of_birth   DATE,
  guardian_name   VARCHAR(120),
  guardian_phone  VARCHAR(20),
  guardian_email  VARCHAR(150),
  rfid_tag        VARCHAR(30)  UNIQUE,
  qr_token        VARCHAR(64)  UNIQUE,
  photo_url       VARCHAR(255),
  is_active       TINYINT(1) DEFAULT 1,
  safety_flag     TINYINT(1) DEFAULT 0,
  flag_reason     TEXT,
  enrolled_at     DATE,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

INSERT INTO students (reg_number,full_name,class_id,gender,guardian_name,guardian_phone,rfid_tag,qr_token,is_active,safety_flag,enrolled_at) VALUES
('STU-2024-001','Amara Ochieng',      9,'Female','Grace Ochieng',     '+254712001001','T-1042',SHA2('STU-2024-001',256),1,0,'2024-01-15'),
('STU-2024-002','Brian Wanjiku',      6,'Male',  'Peter Wanjiku',      '+254712001002','T-1043',SHA2('STU-2024-002',256),1,1,'2024-01-15'),
('STU-2024-003','Catherine Muthoni', 12,'Female','Samuel Muthoni',    '+254712001003','T-1044',SHA2('STU-2024-003',256),1,0,'2024-01-15'),
('STU-2024-004','David Kamau',        3,'Male',  'Ann Kamau',          '+254712001004','T-1045',SHA2('STU-2024-004',256),1,0,'2024-01-15'),
('STU-2024-005','Esther Nafula',      10,'Female','Moses Nafula',      '+254712001005','T-1046',SHA2('STU-2024-005',256),1,0,'2024-01-15'),
('STU-2024-006','Felix Otieno',       5,'Male',  'Rose Otieno',        '+254712001006','T-1047',SHA2('STU-2024-006',256),1,0,'2024-01-15'),
('STU-2024-007','Grace Njeri',        13,'Female','James Njeri',       '+254712001007','T-1048',SHA2('STU-2024-007',256),1,0,'2024-01-15'),
('STU-2024-008','Hassan Ibrahim',     1,'Male',  'Fatuma Ibrahim',     '+254712001008','T-1049',SHA2('STU-2024-008',256),1,1,'2024-01-15'),
('STU-2024-009','Irene Anyango',      9,'Female','Victor Anyango',     '+254712001009','T-1050',SHA2('STU-2024-009',256),1,0,'2024-01-15'),
('STU-2024-010','James Kiprotich',    7,'Male',  'Naomi Kiprotich',    '+254712001010','T-1051',SHA2('STU-2024-010',256),1,0,'2024-01-15'),
('STU-2024-011','Karen Wambui',       12,'Female','Paul Wambui',       '+254712001011','T-1052',SHA2('STU-2024-011',256),1,0,'2024-01-15'),
('STU-2024-012','Liam Mutua',         2,'Male',  'Joyce Mutua',        '+254712001012','T-1053',SHA2('STU-2024-012',256),1,0,'2024-01-15'),
('STU-2024-013','Mary Adhiambo',      11,'Female','Charles Adhiambo',  '+254712001013','T-1054',SHA2('STU-2024-013',256),1,0,'2024-01-15'),
('STU-2024-014','Noah Cheruiyot',     5,'Male',  'Faith Cheruiyot',    '+254712001014','T-1055',SHA2('STU-2024-014',256),1,0,'2024-01-15'),
('STU-2024-015','Olivia Mumo',        13,'Female','Daniel Mumo',       '+254712001015','T-1056',SHA2('STU-2024-015',256),1,0,'2024-01-15'),
('STU-2024-016','Peter Auma',         4,'Male',  'Sarah Auma',         '+254712001016','T-1057',SHA2('STU-2024-016',256),1,0,'2024-01-15'),
('STU-2024-017','Queen Njoroge',      10,'Female','Eric Njoroge',      '+254712001017','T-1058',SHA2('STU-2024-017',256),1,0,'2024-01-15'),
('STU-2024-018','Ronald Kipchoge',    8,'Male',  'Alice Kipchoge',     '+254712001018','T-1059',SHA2('STU-2024-018',256),1,0,'2024-01-15'),
('STU-2024-019','Sarah Wairimu',      14,'Female','Michael Wairimu',   '+254712001019','T-1060',SHA2('STU-2024-019',256),1,0,'2024-01-15'),
('STU-2024-020','Timothy Odhiambo',   1,'Male',  'Esther Odhiambo',   '+254712001020','T-1061',SHA2('STU-2024-020',256),1,1,'2024-01-15'),
('STU-2024-021','Yvonne Karimi',      9,'Female','John Karimi',        '+254712001021','T-1062',SHA2('STU-2024-021',256),1,0,'2024-01-15'),
('STU-2024-022','Zack Onyango',       6,'Male',  'Mary Onyango',       '+254712001022','T-1063',SHA2('STU-2024-022',256),1,0,'2024-01-15'),
('STU-2024-023','Alice Maina',        10,'Female','Peter Maina',       '+254712001023','T-1064',SHA2('STU-2024-023',256),1,0,'2024-01-15'),
('STU-2024-024','Ben Korir',          3,'Male',  'Ruth Korir',         '+254712001024','T-1065',SHA2('STU-2024-024',256),1,0,'2024-01-15'),
('STU-2024-025','Clara Waweru',       12,'Female','Tom Waweru',        '+254712001025','T-1066',SHA2('STU-2024-025',256),1,0,'2024-01-15');

-- ──────────────────────────────────────────────
--  ATTENDANCE
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS attendance (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  student_id      INT NOT NULL,
  attendance_date DATE NOT NULL,
  status          ENUM('Present','Absent','Late','Excused') DEFAULT 'Absent',
  check_in_time   TIME,
  check_out_time  TIME,
  method          ENUM('QR','RFID','Manual','Biometric') DEFAULT 'Manual',
  location        VARCHAR(80),
  marked_by       INT,
  notes           TEXT,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE KEY unique_attendance (student_id, attendance_date)
);

-- Seed today's attendance
INSERT INTO attendance (student_id, attendance_date, status, check_in_time, method, location) VALUES
(1,  CURDATE(), 'Present', '07:42:11', 'QR',   'Main Gate'),
(2,  CURDATE(), 'Absent',  NULL,       'Manual',''),
(3,  CURDATE(), 'Present', '07:55:03', 'RFID', 'Main Gate'),
(4,  CURDATE(), 'Late',    '08:32:44', 'QR',   'Main Gate'),
(5,  CURDATE(), 'Present', '07:38:22', 'RFID', 'Main Gate'),
(6,  CURDATE(), 'Excused', NULL,       'Manual',''),
(7,  CURDATE(), 'Present', '07:50:19', 'RFID', 'Main Gate'),
(8,  CURDATE(), 'Absent',  NULL,       'Manual',''),
(9,  CURDATE(), 'Present', '07:41:55', 'QR',   'Main Gate'),
(10, CURDATE(), 'Present', '07:45:02', 'RFID', 'Main Gate'),
(11, CURDATE(), 'Late',    '08:15:33', 'QR',   'Main Gate'),
(12, CURDATE(), 'Present', '07:39:08', 'RFID', 'Main Gate'),
(13, CURDATE(), 'Absent',  NULL,       'Manual',''),
(14, CURDATE(), 'Present', '07:52:34', 'RFID', 'Main Gate'),
(15, CURDATE(), 'Present', '07:47:18', 'QR',   'Main Gate'),
(16, CURDATE(), 'Present', '07:44:27', 'RFID', 'Main Gate'),
(17, CURDATE(), 'Excused', NULL,       'Manual',''),
(18, CURDATE(), 'Present', '07:40:51', 'QR',   'Main Gate'),
(19, CURDATE(), 'Present', '07:53:09', 'RFID', 'Main Gate'),
(20, CURDATE(), 'Late',    '08:48:17', 'Manual','Main Gate'),
(21, CURDATE(), 'Present', '07:43:00', 'RFID', 'Main Gate'),
(22, CURDATE(), 'Present', '07:49:11', 'QR',   'Main Gate'),
(23, CURDATE(), 'Present', '07:36:44', 'RFID', 'Main Gate'),
(24, CURDATE(), 'Absent',  NULL,       'Manual',''),
(25, CURDATE(), 'Present', '07:51:28', 'RFID', 'Main Gate');

-- ──────────────────────────────────────────────
--  RFID READERS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rfid_readers (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  reader_code  VARCHAR(20) NOT NULL UNIQUE,
  location     VARCHAR(100) NOT NULL,
  description  TEXT,
  status       ENUM('Online','Offline','Degraded') DEFAULT 'Online',
  last_seen    DATETIME,
  ip_address   VARCHAR(45),
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO rfid_readers (reader_code, location, status, last_seen, ip_address) VALUES
('READER-A', 'Main Gate',     'Online',   NOW(),                          '192.168.1.101'),
('READER-B', 'Library',       'Online',   NOW(),                          '192.168.1.102'),
('READER-C', 'Dormitory',     'Degraded', DATE_SUB(NOW(), INTERVAL 1 HOUR),'192.168.1.103'),
('READER-D', 'Lab Block East','Offline',  DATE_SUB(NOW(), INTERVAL 2 HOUR),'192.168.1.104');

-- ──────────────────────────────────────────────
--  RFID LOGS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rfid_logs (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  rfid_tag     VARCHAR(30) NOT NULL,
  student_id   INT,
  reader_id    INT,
  status       ENUM('Authorized','Unauthorized','Pending') DEFAULT 'Authorized',
  scanned_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
  FOREIGN KEY (reader_id)  REFERENCES rfid_readers(id) ON DELETE SET NULL
);

INSERT INTO rfid_logs (rfid_tag, student_id, reader_id, status, scanned_at) VALUES
('T-1042', 1,  1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 8 HOUR)),
('T-1043', 2,  1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 8 HOUR)),
('T-0049', NULL,1,'Unauthorized',  DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('T-1044', 3,  1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 7 HOUR)),
('T-1046', 5,  1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 8 HOUR)),
('T-0082', NULL,2,'Unauthorized',  DATE_SUB(NOW(), INTERVAL 4 HOUR)),
('T-1055', 14, 2, 'Authorized',   DATE_SUB(NOW(), INTERVAL 6 HOUR)),
('T-1056', 15, 1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 7 HOUR)),
('T-0031', NULL,3,'Unauthorized',  DATE_SUB(NOW(), INTERVAL 5 HOUR)),
('T-1051', 10, 1, 'Authorized',   DATE_SUB(NOW(), INTERVAL 7 HOUR));

-- ──────────────────────────────────────────────
--  QR SCAN LOGS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS qr_logs (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  student_id   INT,
  qr_token     VARCHAR(64),
  scan_result  ENUM('Granted','Denied','Expired') DEFAULT 'Granted',
  location     VARCHAR(80),
  scanned_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
);

INSERT INTO qr_logs (student_id, qr_token, scan_result, location, scanned_at) VALUES
(1,  SHA2('STU-2024-001',256), 'Granted', 'Main Gate', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(9,  SHA2('STU-2024-009',256), 'Granted', 'Main Gate', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(10, SHA2('STU-2024-010',256), 'Granted', 'Main Gate', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(15, SHA2('STU-2024-015',256), 'Granted', 'Main Gate', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(NULL, 'INVALID_TOKEN_001',   'Denied',  'Main Gate', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(14, SHA2('STU-2024-014',256), 'Granted', 'Main Gate', DATE_SUB(NOW(), INTERVAL 7 HOUR));

-- ──────────────────────────────────────────────
--  SAFETY ZONES
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS safety_zones (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  zone_name    VARCHAR(80) NOT NULL,
  icon         VARCHAR(10) DEFAULT '🏫',
  capacity     INT DEFAULT 100,
  current_count INT DEFAULT 0,
  status       ENUM('Secure','Crowded','Breach','Offline') DEFAULT 'Secure',
  camera_id    VARCHAR(20),
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO safety_zones (zone_name, icon, capacity, current_count, status, camera_id) VALUES
('Main Classroom Block', '🏫', 300, 243, 'Secure',  'CAM-02'),
('Library',              '📚', 100, 87,  'Secure',  'CAM-03'),
('Cafeteria',            '🍽', 280, 312, 'Crowded', 'CAM-05'),
('Lab Block East',       '🔬', 50,  1,   'Breach',  'CAM-04'),
('Sports Ground',        '🏃', 400, 64,  'Secure',  'CAM-06'),
('Dormitory A',          '🏠', 120, 0,   'Secure',  NULL),
('Main Entrance',        '🅿', 50,  18,  'Secure',  'CAM-01'),
('Workshop',             '🔧', 60,  34,  'Crowded', NULL);

-- ──────────────────────────────────────────────
--  SAFETY INCIDENTS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS safety_incidents (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  incident_ref VARCHAR(20) NOT NULL UNIQUE,
  type         ENUM('critical','warning','info') DEFAULT 'info',
  zone_id      INT,
  student_id   INT,
  title        VARCHAR(200) NOT NULL,
  description  TEXT,
  status       ENUM('Open','Resolved','Dismissed') DEFAULT 'Open',
  reported_by  INT,
  resolved_at  DATETIME,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (zone_id)    REFERENCES safety_zones(id) ON DELETE SET NULL,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
);

INSERT INTO safety_incidents (incident_ref, type, zone_id, title, description, status, created_at) VALUES
('INC-2024-047','critical',4,'Unauthorized RFID access — Lab Block East','RFID tag T-0049 attempted entry at 14:37:22. Security notified. Zone locked down.','Open',   DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('INC-2024-046','warning', 3,'Overcrowding detected in Cafeteria',       'Cafeteria currently at 312 persons, exceeding limit of 280.','Open',                          DATE_SUB(NOW(), INTERVAL 4 HOUR)),
('INC-2024-045','info',    4,'Camera CAM-04 anomaly',                     'Motion detection triggered with no registered RFID in zone.','Open',                          DATE_SUB(NOW(), INTERVAL 4 HOUR)),
('INC-2024-044','warning', NULL,'Student unaccounted for',                'STU-2024-008 Hassan Ibrahim not scanned. Guardian notified.','Open',                          DATE_SUB(NOW(), INTERVAL 6 HOUR)),
('INC-2024-043','info',    4,'RFID Reader D offline',                     'Reader D Lab Block East went offline. Maintenance ticket created.','Resolved',                DATE_SUB(NOW(), INTERVAL 7 HOUR)),
('INC-2024-042','critical',8,'Unauthorized entry attempt — Workshop',     'Tag T-0082 attempted side-exit access. Denied and logged.','Resolved',                        DATE_SUB(NOW(), INTERVAL 9 HOUR));

-- ──────────────────────────────────────────────
--  CAMERA FEEDS (metadata only)
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cameras (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  camera_code  VARCHAR(20) NOT NULL UNIQUE,
  location     VARCHAR(100) NOT NULL,
  zone_id      INT,
  status       ENUM('Online','Offline','Alert') DEFAULT 'Online',
  resolution   VARCHAR(20) DEFAULT '1080p',
  fps          INT DEFAULT 30,
  ip_address   VARCHAR(45),
  last_frame   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (zone_id) REFERENCES safety_zones(id) ON DELETE SET NULL
);

INSERT INTO cameras (camera_code, location, zone_id, status, ip_address) VALUES
('CAM-01','Main Gate',          7,'Online', '192.168.2.101'),
('CAM-02','Classroom Block A',  1,'Online', '192.168.2.102'),
('CAM-03','Library Entrance',   2,'Online', '192.168.2.103'),
('CAM-04','Lab Block East',     4,'Alert',  '192.168.2.104'),
('CAM-05','Cafeteria',          3,'Online', '192.168.2.105'),
('CAM-06','Sports Ground',      5,'Online', '192.168.2.106');

-- ──────────────────────────────────────────────
--  SYSTEM LOGS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS system_logs (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  action     VARCHAR(200) NOT NULL,
  details    TEXT,
  user_id    INT,
  ip_address VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ──────────────────────────────────────────────
--  NOTIFICATIONS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(200) NOT NULL,
  message      TEXT,
  type         ENUM('info','success','warning','danger') DEFAULT 'info',
  is_read      TINYINT(1) DEFAULT 0,
  user_id      INT,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO notifications (title, message, type, is_read) VALUES
('SMS sent to guardian of Brian Wanjiku',   'Absence notification sent automatically',         'warning', 0),
('Security alert escalated — Lab Block',    'Unauthorized access INC-2024-047 escalated',       'danger',  0),
('Daily attendance report generated',        'PDF report emailed to principal@school.edu',       'success', 1),
('RFID Reader D offline',                    'Maintenance ticket #T-0391 created automatically', 'warning', 0),
('Backup completed successfully',            'Full system backup 2.4GB — stored securely',       'success', 1);

-- ──────────────────────────────────────────────
--  VIEWS
-- ──────────────────────────────────────────────
CREATE OR REPLACE VIEW v_today_attendance AS
  SELECT s.reg_number, s.full_name, c.class_name,
         a.status, a.check_in_time, a.method, s.rfid_tag, s.safety_flag
  FROM students s
  LEFT JOIN attendance a ON a.student_id=s.id AND a.attendance_date=CURDATE()
  LEFT JOIN classes c ON c.id=s.class_id
  WHERE s.is_active=1;

CREATE OR REPLACE VIEW v_attendance_stats AS
  SELECT
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) AS present_count,
    SUM(CASE WHEN status='Absent'  THEN 1 ELSE 0 END) AS absent_count,
    SUM(CASE WHEN status='Late'    THEN 1 ELSE 0 END) AS late_count,
    SUM(CASE WHEN status='Excused' THEN 1 ELSE 0 END) AS excused_count,
    COUNT(*) AS total_count
  FROM attendance WHERE attendance_date=CURDATE();

DELIMITER ;;
CREATE PROCEDURE IF NOT EXISTS sp_mark_attendance(
  IN p_reg      VARCHAR(30),
  IN p_method   VARCHAR(20),
  IN p_status   VARCHAR(20),
  IN p_location VARCHAR(80)
)
BEGIN
  DECLARE v_id INT;
  SELECT id INTO v_id FROM students WHERE reg_number=p_reg AND is_active=1;
  IF v_id IS NOT NULL THEN
    INSERT INTO attendance (student_id,attendance_date,status,check_in_time,method,location)
    VALUES (v_id, CURDATE(), p_status, CURTIME(), p_method, p_location)
    ON DUPLICATE KEY UPDATE status=p_status, check_in_time=CURTIME(), method=p_method;
  END IF;
END;;
DELIMITER ;
