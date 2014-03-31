 #!/usr/bin/env bash

apt-get update

apt-get install -y apache2
apt-get install -y php5
apt-get install -y vim

rm -rf /var/www
ln -fs /vagrant/public /var/www

INI_FILE=/etc/php5/apache2/php.ini
ERROR_REPORTING="E_ALL"
DISPLAY_ERRORS=On

sed -i "s/error_reporting = .*/error_reporting = ${ERROR_REPORTING}/" ${INI_FILE}
sed -i "s/display_errors = .*/display_errors = ${DISPLAY_ERRORS}/" ${INI_FILE}

service apache2 restart