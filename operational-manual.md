# CampusFix Cloud — Operational Manual

## 1. Project

CampusFix Cloud is a secure multi-tier campus IT incident and equipment management system.

## 2. Architecture

Windows Host → VirtualBox → Ubuntu Server → UFW → Docker → PHP/Apache + MySQL → Persistent MySQL Volume.

## 3. Deployment

1. Install Ubuntu Server.
2. Install Docker and Docker Compose.
3. Copy the project to the server.
4. Run `docker compose up -d --build`.
5. Verify containers with `docker ps`.
6. Test the application through the server IP and configured port.

## 4. Database Persistence

MySQL uses the named Docker volume `campusfix_mysql_data`. The volume is not removed by a normal container restart.

## 5. Security

- UFW default incoming policy: deny.
- SSH is explicitly allowed.
- The web application port is explicitly allowed.
- MySQL is not published to the host.
- Application database queries use PDO prepared statements.
- Passwords use PHP `password_hash()` and `password_verify()`.
- Sessions regenerate after login.
- Forms use CSRF tokens.

## 6. Backup

`automation/backup.sh` uses `mysqldump` inside the MySQL container and stores SQL backups.

Example cron schedule:

```cron
0 23 * * * /opt/campusfix/automation/backup.sh >> /opt/campusfix/backups/backup.log 2>&1
```

## 7. Maintenance

Check:

```bash
docker ps
docker compose logs --tail=100
df -h
sudo ufw status verbose
```

## 8. Recovery

Restore a SQL backup only after confirming the correct backup file and stopping application writes as appropriate for the incident.

## 9. Defense Demonstration

Demonstrate:

1. Login.
2. Dashboard.
3. Equipment record.
4. Create incident.
5. Assign technician.
6. Update status.
7. Add repair note.
8. Resolve incident.
9. Show Docker containers.
10. Show persistent volume.
11. Show UFW.
12. Run backup.
13. Show Cron configuration.
14. Restart containers and prove database data remains.
