PHPCONF=/usr/local/etc/php/php.ini
sed -i '/^; max_input_vars/c\max_input_vars = 2000' $PHPCONF