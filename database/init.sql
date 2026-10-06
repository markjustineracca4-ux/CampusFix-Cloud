USE campusfix;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin','technician','staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(50) NOT NULL UNIQUE,
    equipment_name VARCHAR(100) NOT NULL,
    laboratory VARCHAR(100) NOT NULL,
    brand VARCHAR(100),
    model VARCHAR(100),
    status ENUM('Working','Under Repair','Retired') DEFAULT 'Working',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_code VARCHAR(50) NOT NULL UNIQUE,
    equipment_id INT NULL,
    reported_by INT NULL,
    category VARCHAR(100) NOT NULL,
    priority ENUM('Low','Medium','High','Critical') DEFAULT 'Medium',
    description TEXT NOT NULL,
    status ENUM('Pending','In Progress','Resolved') DEFAULT 'Pending',
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE incident_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    user_id INT NOT NULL,
    update_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO equipment (asset_code,equipment_name,laboratory,brand,model,status) VALUES
('LAB-PC-001','Desktop Computer','Computer Laboratory 1','Dell','OptiPlex 3080','Working'),
('LAB-PC-002','Desktop Computer','Computer Laboratory 1','HP','ProDesk 400','Under Repair'),
('LAB-PC-003','Desktop Computer','Computer Laboratory 2','Lenovo','ThinkCentre M70','Working'),
('LAB-NET-001','Network Switch','Networking Laboratory','TP-Link','TL-SG1024','Working');

INSERT INTO users (username,password,full_name,role)
VALUES ('admin','TEMPORARY_PASSWORD','System Administrator','admin');
