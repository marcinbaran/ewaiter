delete:
	@sudo docker-compose stop
	@sudo docker-compose rm -f
	@sudo rm -rf docker-compose/adguard/adguard
	@sudo rm -rf docker-compose/mysql/mysql

build:
	@bash init.sh

