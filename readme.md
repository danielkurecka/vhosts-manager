# Vhosts Manager
This is a tool for managing virtual hosts of the Apache server in Debian based distributions.

It automates these things:
- creates virtual host from predefined template
- adds entry to /etc/hosts file
- reloades apache configuration

**Installation:**

Atleast PHP 5.4 is required.

Clone the repository somewhere (eg. to `/opt/vhosts-manager`):

`git clone https://github.com/danielkurecka/vhosts-manager`

You may optionally create a symlink in `/usr/bin` for the executable:

`sudo ln -s /opt/vhosts-manager/bin/vhm /usr/bin/vhm`

**Usage:**

Create a new virtual host:

`sudo vhm add example.local path/to/my-project`

Remove virtual host:

`sudo vhm remove example.local`
