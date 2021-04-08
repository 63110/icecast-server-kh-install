# icecastinstall
sudo apt install -y build-essential libxml2-dev libxslt1-dev libcurl4-openssl-dev libvorbis-dev libtheora-dev libssl-dev openssl

wget https://github.com/karlheyes/icecast-kh/archive/icecast-2.4.0-kh15.tar.gz && \
tar xvf icecast-2.4.0-kh15.tar.gz && \
cd icecast-kh-icecast-2.4.0-kh15 && \
./configure --prefix=/usr --with-curl --with-openssl && \
make && \
sudo make install && \
cd ../ && \
rm -rf icecast-kh-icecast-2.4.0-kh15 && \ 
rm -rf icecast-2.4.0-kh15.tar.gz && \ 
/usr/bin/icecast -v



#!/bin/sh
/usr/bin/icecast -b -c /usr/etc/icecast.xml

chmod 755 icecast_start.sh


