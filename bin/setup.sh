#!/bin/sh

WHOAMI=`python -c 'import os, sys; print os.path.realpath(sys.argv[1])' $0`

WHEREAMI=`dirname $WHOAMI`
API=`dirname $WHEREAMI`

PROJECT=$1

echo "copying application files to ${PROJECT}"
cp ${API}/www/*.php ${PROJECT}/www/

echo "copying css files to ${PROJECT}"
cp ${API}/www/css/*.css ${PROJECT}/www/css/

echo "" > ${PROJECT}/www/css/main.css
echo "@import url('/css/api.css');" > ${PROJECT}/www/css/main.css

echo "[NOTE] a CSS @import stamement for '/css/api.css' has been added to main.css; adjust as needed"

echo "copying templates to ${PROJECT}"
cp ${API}/www/templates/*.txt ${PROJECT}/www/templates/

echo "copying library code to ${PROJECT}"
cp ${API}/www/include/*.php ${PROJECT}/www/include/

echo "copying script (bin) files to ${PROJECT}"
cp ${API}/www/bin/*.php ${PROJECT}/www/bin/

YMD=`date "+%Y%m%d"`
mkdir ${PROJECT}/schema/alters

echo "copying database schemas to ${PROJECT}; you will still need to run database alters manually"

cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/db_main.schema
cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/alters/${YMD}.db_main.schema

echo "[NOTE] please add/update the contents of '${API}/www/.htaccess' to your ${PROJECT}/www/.htacess file"

echo "[NOTE] please add/update the contents of '${API}/include/config.php.example' to your ${PROJECT}/www/include/config.php file"
