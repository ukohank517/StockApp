all:

rdb:
	php artisan migrate:refresh --seed
	mysql -u purpleotter7 -p purpleotter7_app < ini.sql
dump:
	mysqldump -u purpleotter7 -p -h mysql706.db.sakura.ne.jp purpleotter7_app> ini.sql

