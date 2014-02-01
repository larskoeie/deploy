#!/bin/sh
### change the values below where needed.....
DB="ks"
HOST="--host=localhost"
USER="--user=ks"
BACKUP_DIR="dump/"

#### you can change these values but they are optional....
OPTIONS="--default-character-set=latin1 --complete-insert -q"
DATE=`date '+%y%m%d_%H%M%S'`

	unset PASSWORD
	echo "DB password  : "
	read PASSWORD
	echo
		


#### make no changes after this....
#### start script ####

for TABLE in `mysql $HOST $USER -p$PASSWORD $DB -e 'show tables' | egrep -v 'Tables_in_' `; do
    TABLENAME=$(echo $TABLE|awk '{ printf "%s", $0 }')
    FILENAME="${TABLENAME}.sql"
    echo Dumping $TABLENAME
    mysqldump $OPTIONS $HOST $USER -p$PASSWORD $DB $TABLENAME > ${BACKUP_DIR}${FILENAME}
    echo "source $FILENAME" >> ${BACKUP_DIR}__restore.sql 
done

echo making tar...
tar -cf ${DB}_${DATE}.tar ${BACKUP_DIR}*.sql  > /dev/null 2>&1

echo compressing...
gzip -9 ${DB}_${DATE}.tar > /dev/null 2>&1


echo "done with " $DB

echo "=========================================="
echo "            done with all database!       "
echo "=========================================="
