# CAPP Framework

Setting at "xampp\apache\conf\extra\httpd-vhosts.conf"
<VirtualHost 127.0.0.1:80>
    ServerAdmin webmaster@kab.local
    DocumentRoot "C:/xampp/htdocs_pippo/"
    ServerName capp.local
	<Directory "C:/xampp/htdocs_capp">
		Options Indexes FollowSymLinks MultiViews ExecCGI
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>

Dont forget setting file hosts "Windows\System32\drivers\etc\hosts"
