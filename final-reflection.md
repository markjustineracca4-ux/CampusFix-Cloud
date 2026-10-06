# Final Reflection

Mission 10 brought together the Linux, networking, containerization, security, automation, and cloud infrastructure concepts developed throughout CCM101. Instead of deploying a simple standalone website, I designed CampusFix Cloud as a multi-tier application with a PHP/Apache web tier and a separate MySQL database tier.

The project demonstrated why persistent storage is important in containerized environments. I also implemented host-based firewall controls and avoided exposing the database directly to the network. Password hashing, prepared statements, session controls, and CSRF protection were included at the application layer.

Automation was implemented through a Bash database-backup script and Cron scheduling. This showed how routine operational tasks can be performed consistently without manual intervention.

The most important lesson from this mission was that cloud architecture is not only about making an application run. A usable architecture must also consider persistence, security, recovery, maintainability, monitoring, and documentation. The final system therefore represents an integration of development and infrastructure skills rather than only a website project.
