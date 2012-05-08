#!/bin/sh

if test $OSTYPE = "FreeBSD"
then
    WHOAMI=`realpath $0`
elif test $OSTYPE = "darwin"
then
    WHOAMI=`python -c 'import os, sys; print os.path.realpath(sys.argv[1])' $0`
else
    WHOAMI=`readlink -f $0`    
fi

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

cp -R ${API}/www/include/config.api.examples ${PROJECT}/www/include/

echo "copying database schemas to ${PROJECT}; you will still need to run database alters manually"

YMD=`date "+%Y%m%d"`
mkdir ${PROJECT}/schema/alters

cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/db_main.schema
cat ${API}/schema/db_main.schema >> ${PROJECT}/schema/alters/${YMD}.db_main.schema

# TO DO: update the project files themselves

cat ${API}/www/.htaccess
cat ${API}/www/include/config.php.example
