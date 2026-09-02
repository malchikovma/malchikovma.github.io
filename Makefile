# Установить тему
git-install-submodules:
	git submodule init
	git submodule update --init --recursive

# Сервер для разработки
dev-server:
	hugo server --buildDrafts

# Новый пост
new-post:
	read -r -p "Enter post name using characters [a-z0-9\-]: " POSTNAME && \
	TODAY=$$(date +%Y-%m-%d) && \
	hugo new content "content/post/$$TODAY-$$POSTNAME/index.md"

# Собрать продовую версию
build:
	hugo

# Загрузить на сервер
deploy:
	ssh malchikovma@malchikovma.ru "rm -rf /var/www/malchikovma.ru/public_old && [ -d /var/www/malchikovma.ru/public ] && mv -vf /var/www/malchikovma.ru/public /var/www/malchikovma.ru/public_old"
	rsync --archive --verbose public malchikovma@malchikovma.ru:/var/www/malchikovma.ru