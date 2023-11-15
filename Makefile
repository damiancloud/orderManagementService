CONTAINER_NAME = merece-container

start:
	docker-compose build
	composer install
	
stop:
	docker stop $(CONTAINER_NAME) 
	docker stop $(CONTAINER_NAME)-nginex
	docker stop $(CONTAINER_NAME)-postgres

up:
	docker-compose up --force-recreate

console: 
	docker exec -it $(CONTAINER_NAME) bash

down:
	docker-compose down

remove:
	docker stop $(CONTAINER_NAME) 
	docker stop $(CONTAINER_NAME)-nginex
	docker stop $(CONTAINER_NAME)-postgres
	docker container rm $(CONTAINER_NAME) 
	docker container rm $(CONTAINER_NAME)-nginex
	docker container rm $(CONTAINER_NAME)-postgres
