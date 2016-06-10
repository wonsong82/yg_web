start from outside root which is parent of server
cd yg
yg> git clone https://boksil@bitbucket.org/ygpresents/web.git

this creates web folder and pulls source
yg>cd web

yg/web> tar -xvf latest.tar.gz --strip-components=1
this unzips core wordpress files
yg/web> git reset --hard
this resets and brings sources over written by previous unzip to up-to-date  

to setup local db user :
>create user yguser@'%' identified by '1gobaesong';

>grant all privileges on ygdb.* to 'yguser'@'*' identified by '1gobaesong';

>flush privileges;


below is the initial wp-config.php content.
https://bitbucket.org/ygpresents/web/downloads/wp-config.php