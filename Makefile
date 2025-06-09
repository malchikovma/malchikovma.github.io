# Установить тему
git-install-submodules:
	git submodule init

# Запустить локальный сервер для разработки
docker-dev-server:
	docker run --rm \
	  --name mysite \
	  -v ${PWD}:/src \
	  -p 1313:1313 \
	  -v ${HOME}/hugo_cache:/tmp/hugo_cache \
	  hugomods/hugo:exts-non-root \
	  server
