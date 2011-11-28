#!/bin/sh

WHOAMI=`readlink -f $0`
WHEREAMI=`dirname $WHOAMI`
API=`dirname $WHEREAMI`

PROJECT=$1

echo "copying application files to ${PROJECT}"
cp ${API}/www/*.php ${PROJECT}/www/

echo "copying templates to ${PROJECT}"
cp ${API}/www/templates/*.txt ${PROJECT}/www/templates/

echo "copying library code to ${PROJECT}"
cp ${API}/www/include/*.php ${PROJECT}/www/include/

echo "copying library configs to ${PROJECT}"
cp ${API}/www/include/*.json ${PROJECT}/www/include/

echo "copying database schemas to ${PROJECT}; you will still need to run database alters manually"

YMD=`date "+%Y%m%d"`
mkdir ${PROJECT}/schema/alters

cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/db_main.schema
cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/alters/${YMD}.db_main.schema
