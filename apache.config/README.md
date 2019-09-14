# Apache Commands for domain

Create a config domain
````
sudo a2ensite travelexperience.com.conf
````
Reaload config domains and restart apache
````
systemctl reload apache2
systemctl restart apache2
````
Activate rewrite mode
````
a2enmod rewrite 
````

Activate domain /etc/hosts
````
127.0.0.1       travelexperience.com
````