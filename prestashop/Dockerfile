FROM prestashop/prestashop:1.6

ENV DB_PREFIX ps_
ENV PS_SHOP_NAME PrestaShop

VOLUME /var/www/html

ADD files /

RUN chmod +x /tmp/install.sh

RUN /tmp/install.sh