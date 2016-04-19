<VirtualHost *:80>
	ServerName "<?= $site; ?>"
	DocumentRoot "<?= $documentRoot; ?>"

	<Directory "<?= $documentRoot; ?>">
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>
</VirtualHost>
