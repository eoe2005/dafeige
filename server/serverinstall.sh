#!/bin/sh
SERVER_DIR=$(realpath $(dirname $0))
function php7(){
	dir=$1
	prefix=$dir/PHP7
	conffile=$dir/conf/php7.ini
	wget -O php-7.0.2.tar.gz http://cn2.php.net/get/php-7.0.2.tar.gz/from/this/mirror
	tar -xvf php-7.0.2.tar.gz
	cd php-7.0.2
	./configure --prefix=$prefix --enable-fpm --enable-phpdbg --enable-phpdbg-webhelper --with-config-file-path=$conffile   --enable-dtrace  --enable-calendar --enable-bcmath --enable-exif --enable-gd-native-ttf --enable-mbstring --enable-pcntl --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-zip --enable-mysqlnd --with-pcre-regex --with-zlib  --with-gd --with-mcrypt --with-pdo-mysql   --without-pdo-sqlite && make ZEND_EXTRA_LIBS='-liconv' -j 2 && make install && echo "error_log=${dir}/logs/php7.error" >> $conffile
	cd ../
	rm -Rf php-7.0.2*
}
function php5(){
	dir=$1
	prefix=$dir/PHP5
	conffile=$dir/conf/php5.ini
	wget http://cn2.php.net/distributions/php-5.6.17.tar.gz
	tar -xvf php-5.6.17.tar.gz
	cd php-5.6.17
	./configure --prefix=$prefix --enable-fpm --enable-phpdbg --enable-phpdbg-webhelper --with-config-file-path=$conffile   --enable-dtrace  --enable-calendar --enable-bcmath --enable-exif --enable-gd-native-ttf --enable-mbstring --enable-pcntl --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-zip --enable-mysqlnd --with-pcre-regex --with-zlib  --with-gd --with-mcrypt --with-pdo-mysql   --without-pdo-sqlite && make ZEND_EXTRA_LIBS='-liconv' -j 2 && make install && echo "error_log=${dir}/logs/php5.error" >> $conffile
	cd ../
	rm -Rf php-5.6.17*
	$prefix/bin/pecl install redis
	echo 'extension=redis.so' >> $conffile
	$prefix/bin/pecl install memcached
	echo 'extension=memcached.so' >> $conffile
}
function nginx(){
	dir=$1
	prefix=$dir/nginx1.9.9
	errorlog=$dir/logs/nginx.error
	acclog=$dir/logs/nginx.log
	wegt http://nginx.org/download/nginx-1.9.9.tar.gz
	tar -xfv nginx-1.9.9.tar.gz
	cd nginx-1.9.9
	./configure --prefix=$prefix --error-log-path=$errorlog --http-log-path=$acclog --with-poll_module --with-threads --with-file-aio --with-http_v2_module --with-http_gunzip_module --with-http_gzip_static_module --with-stream --with-stream_ssl_module && make -j 2 && make install
	cd ../
	rm -Rf nginx-1.9.9*
}
function mariadb10(){
	dir=$1
	wget http://mirrors.opencas.cn/mariadb//mariadb-10.1.10/source/mariadb-10.1.10.tar.gz
	tar -xvf mariadb-10.1.10.tar.gz
	cd mariadb-10.1.10
	cmake . -DCMAKE_INSTALL_PREFIX=$dir/mariadb10 -DMYSQL_DATADIR=$dir/data/mariadb10 -DDEFAULT_CHARSET=utf8-DDEFAULT_COLLATION=utf8_general_ci -DEXTRA_CHARSETS=all -DENABLED_LOCAL_INFILE=1 && make -j 2 && make
	cd ../
	rm -Rf mariadb-10.1.10*
}
function reids(){
	dir=$1
	cd $dir
	wget http://download.redis.io/releases/redis-3.0.6.tar.gz
	tar -xvf redis-3.0.6.tar.gz
	cd redis-3.0.6
	make -j 2
	cd ../
	mkdir -p redis3/bin
	mkdir -p redis3/conf
	mv redis-3.0.6/src/redis-cli 	redis3/bin/
	mv redis-3.0.6/src/redis-check-dump 	redis3/bin/
	mv redis-3.0.6/src/redis-server 	redis3/bin/
	mv redis-3.0.6/src/redis-check-aof 	redis3/bin/
	mv redis-3.0.6/src/redis-check-dump 	redis3/bin/
	mv redis-3.0.6/src/redis-benchmark	redis3/bin/
	mv redis-3.0.6/redis.conf	redis3/conf/
	rm -Rf redis-3.0.6*
}
function memcache(){
	dir=$1
	wget http://memcached.org/files/memcached-1.4.25.tar.gz
	tar -xvf memcached-1.4.25.tar.gz
	cd memcached-1.4.25
	./configure --prefix=$dir/memcached1.4.25 --enable-64bit && make -j 2 && make install
	cd ../
	rm -Rf memcached-1.4.25*
}
case "$1"
	php7)
		php7 $SERVER_DIR
	;;
	php5)
		php5 $SERVER_DIR
	;;
	nginx)
		nginx $SERVER_DIR
	;;
	mariadb10)
		mariadb10 $SERVER_DIR
	;;
	mysql)
		mysql $SERVER_DIR
	;;
	redis)
		reids $SERVER_DIR
	;;
	memcache)
		memcache $SERVER_DIR
		;;
	all)
	;;
	*)		
		echo "选择要安装的程序"
	;;
esac