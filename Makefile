docker-up:
	docker compose --env-file ./backend/.env up -d
docker-stop:
	docker compose --env-file ./backend/.env stop
docker-down:
	docker compose --env-file ./backend/.env down
docker-build:
	docker compose --env-file ./backend/.env build