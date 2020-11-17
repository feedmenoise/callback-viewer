FROM mariadb:latest
ADD . /opt
WORKDIR /opt
ENV MYSQL_ALLOW_EMPTY_PASSWORD=yes
EXPOSE 3306
CMD mysqld
#RUN mysql -uroot < db_init.sql
