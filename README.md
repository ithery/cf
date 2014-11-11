# CAPP Framework

Setting at "xampp\apache\conf\extra\httpd-vhosts.conf" &lt;br/&gt;

&lt;VirtualHost 127.0.0.1:80&gt; &lt;br/&gt;
    ServerAdmin webmaster@kab.local &lt;br/&gt;
    DocumentRoot "C:/xampp/htdocs_pippo/" &lt;br/&gt;
    ServerName capp.local &lt;br/&gt;
	&lt;Directory "C:/xampp/htdocs_capp"&gt; &lt;br/&gt;
		Options Indexes FollowSymLinks MultiViews ExecCGI &lt;br/&gt;
		AllowOverride All &lt;br/&gt;
		Order allow,deny &lt;br/&gt;
		Allow from all &lt;br/&gt;
	&lt;/Directory&gt; &lt;br/&gt;
&lt;/VirtualHost&gt; &lt;br/&gt;

Dont forget setting file hosts "Windows\System32\drivers\etc\hosts"
