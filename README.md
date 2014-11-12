# CAPP Framework

Setting at "xampp\apache\conf\extra\httpd-vhosts.conf" &lt;br/&gt;

&lt;VirtualHost 127.0.0.1:80&gt; 

    ServerAdmin webmaster@kab.local 
	
    DocumentRoot "C:/xampp/htdocs_pippo/" 
	
    ServerName capp.local &lt;br/&gt;
	&lt;Directory "C:/xampp/htdocs_capp"&gt; 
		Options Indexes FollowSymLinks MultiViews ExecCGI 
		
		AllowOverride All 
		
		Order allow,deny 
		
		Allow from all 
		
	&lt;/Directory&gt; 
	
&lt;/VirtualHost&gt; 

Dont forget setting file hosts "Windows\System32\drivers\etc\hosts"
