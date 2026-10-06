# CampusFix Cloud

**CampusFix Cloud: A Secure Multi-Tier Campus IT Incident and Equipment Management System**

CCM101 Mission 10 — Enterprise Cloud Architect

## Stack

- VirtualBox
- Ubuntu Server
- Docker
- Docker Compose
- PHP 8.2 + Apache
- MySQL 8
- UFW
- Bash + Cron

## Features

- Secure login with password hashing
- Role-based users
- Equipment inventory
- IT incident/ticket management
- Technician assignment
- Incident status workflow
- Repair history
- Persistent MySQL storage
- Automated database backup

## Local development

```bash
docker compose up -d --build
```

Open:

http://localhost:8080

Initial login:

- Username: `admin`
- Password: `admin123`

After confirming the account works, delete `website/setup_admin.php`.

## Production/VM notes

Do not expose MySQL publicly. Only the web application and SSH should be allowed through the host firewall. Configure UFW with a default-deny incoming policy before final deployment.

## Repository deliverables

- architecture-diagram.png
- docker-compose.yml
- automation/backup.sh
- operational-manual.md
- final-reflection.md
