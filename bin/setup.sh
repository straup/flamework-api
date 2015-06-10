#!/bin/sh

WHOAMI=`python -c 'import os, sys; print os.path.realpath(sys.argv[1])' $0`

WHEREAMI=`dirname $WHOAMI`
API=`dirname $WHEREAMI`

PROJECT=$1

echo "copying application files to ${PROJECT}"
cp ${API}/www/*.php ${PROJECT}/www/

echo "copying css files to ${PROJECT}; you will need to include them in your templates manually"
cp ${API}/www/css/*.css ${PROJECT}/www/css/

echo "copying javascript files to ${PROJECT}; you will need to include them in your templates manually"
cp ${API}/www/javascript/*.css ${PROJECT}/www/javascript/

echo "copying templates to ${PROJECT}"
cp ${API}/www/templates/*.txt ${PROJECT}/www/templates/

echo "copying library code to ${PROJECT}"
cp ${API}/www/include/*.php ${PROJECT}/www/include/

YMD=`date "+%Y%m%d"`
mkdir ${PROJECT}/schema/alters

echo "copying database schemas to ${PROJECT}; you will still need to run database alters manually"

cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/db_main.schema
cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/alters/${YMD}.db_main.schema

echo "" >> ${PROJECT}/www/.htaccess
cat ${API}/www/.htaccess.api >> ${PROJECT}/www/.htaccess
echo "" >> ${PROJECT}/www/.htaccess

echo "" >> ${PROJECT}/www/include/config.php
cat ${API}/www/include/config.php.api >> ${PROJECT}/www/include/config.php
echo "" >> ${PROJECT}/www/include/config.php
