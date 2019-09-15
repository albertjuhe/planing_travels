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

## References

https://stackoverflow.com/questions/50615860/how-to-remove-index-php-from-symfony4-router

https://support.rackspace.com/how-to/set-up-apache-virtual-hosts-on-ubuntu/

https://www.digitalocean.com/community/tutorials/como-configurar-virtual-hosts-de-apache-en-ubuntu-16-04-es