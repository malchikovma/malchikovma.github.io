+++
title = 'Как хостить сайт на GitHub с Hugo'
date = 2024-10-18T16:33:17+03:00
draft = true
image = 'hugo-logo.webp'
tags = ['Админитрирование', 'Руководства']
+++

## Введение

GitHub позволяет использовать репозиторий как статический веб-сайт. Существует такой инструмент, как генератор статического сайт (SSG). Так почему бы не подружить их?

> GitHub из коробки работает с Jekyll, но я уже потратил вечер на изучание Hugo.

Для этого нам понадобится знание git, Markdown и YAML. А при работе с репозиторием по ssh, еще и базовое умение работать с ключами шифрования.

Описываю свой опыт, так что все упоминания malchikovma осознанно заменяем на свои собственные.

## Создаем сайт на github.io

Базовый сценарий красиво описан на странице [pages.github.com](https://pages.github.com/). Я опишу чуть более подробно, с решением проблем, с которыми сам столкнулся.

Создаем репозиторий в GitHub с именем `malchikovma.github.io`.

Копируем его локально:

```sh
git clone git@github.com:malchikovma/malchikovma.github.io.git malchikovma.ru
```

Название директории я выбрал "malchikovma.ru" по названию своего домена. Можно указать любую: site, blog, etc.

Если возникла проблема с доступом, нужно настроить ssh-ключ и указать его в настройках github. Есть официальная инструкция, но она не переведена на русский на данный момент: ["Создание нового ключа SSH и его добавление в ssh-agent"](https://docs.github.com/ru/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent?platform=linux). Кратко опишу здесь.

1. Выполняем команду `ssh-keygen -t ed25519 -C "malchikovma@gmail.com"`. В процессе указываем название файла: `github_personal_ed25519`. Пароль можно не указывать.
В результате в директории `~/.ssh` получим два файла: `github_personal_ed25519` и `github_personal_ed25519.pub`.
1. Выполняем команду `ssh-add ~/.ssh/github_personal_ed25519`.
1. Добавляем содержимое файла `github_personal_ed25519.pub` в настройки профиля GitHub: Settings, SSH and GPG keys, new SSH key.

После этого проблема с доступами должна исчезнуть. Альтернативно, можно использовать протокол https:

```sh
git clone https://github.com/malchikovma/malchikovma.github.io.git malchikovma.ru
```

Переходим в новую директорию, открываем ~~Vim~~ свой любимый редактор и создаем файл `index.html` с содержимым "Hello GitHub Pages!".

Публикуем изменения и в течение минуты наше приветствие будет доступно по адресу [malchikovma.github.io](https://malchikovma.github.io).

```sh
git add .
git commit -m 'Initial commit'
git push -u origin main
```

При коммите git может запросить ваши данные. Указываем их:

```sh
git config user.email 'malchikovma@gmail.com'
git config user.name 'Mikhail Malchikov'
```
Если все получилось, идем дальше.

## Настройка Hugo

### Установка

Устанавливаем Hugo. На Ubuntu Linux это делается командой `sudo apt install hugo`. Для других систем смотрим официальное руководство: [Hugo - Installation](https://gohugo.io/installation/).

Так как Hugo не может создать новый сайт в уже существующей директории, создаем его рядом, задет перемещаем в существующий в созданный ранее репозиторий.

```sh
hugo new site malchikovma.ru.bak
cp malchikovma.ru.bak/* malchikovma.ru/
```

> Официальное руководство [Hugo - Quick Start](https://gohugo.io/getting-started/quick-start/) несколько отличается от нашего процесса. В частности тем, что там не учитывается интеграция с GitHub. Тем не менее, рекомендую использовать его.

Hugo использует [систему модулей git](https://git-scm.com/book/ru/v2/%D0%98%D0%BD%D1%81%D1%82%D1%80%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D1%8B-Git-%D0%9F%D0%BE%D0%B4%D0%BC%D0%BE%D0%B4%D1%83%D0%BB%D0%B8). Устанавливаем красивую тему [Stack](https://themes.gohugo.io/themes/hugo-theme-stack/) и указываем в конфиге, что используем ее:

```sh
git submodule add https://github.com/CaiJimmy/hugo-theme-stack.git themes/stack
echo "theme = 'stack'" >> hugo.toml
```

Запускаем локальный сервер: `hugo server --buildDrafts`. Сайт будет доступен по адресу [http://localhost:1313/](http://localhost:1313/). Он нужен для локальной разработки, посмотреть как выглядит страница перед публикацией. Hugo использует hot reload, при изменении файлов они автоматически перезагружаются в браузере. Иногда это не помогает и надо перезапустить сервер.

На данном этапе мы должны получить рабочий сайт без контента.

![Hugo с темой Stack без контента](hugo-fresh.webp)

### Пишем первый пост

Чтобы создать первый пост, используем команду `hugo new content content/post/my-first-post.md`. Она создаст файл по указанному пути. Можно создать его и вручную, но команда имеет преимущество, она создает так называемый [front matter](https://gohugo.io/content-management/front-matter/): метаданные о публикации в формате TOML. Пример, front matter этой статьи:

```md
+++
title = 'Как хостить сайт на GitHub с Hugo'
date = 2024-10-18T16:33:17+03:00
draft = true
image = 'hugo-logo.webp'
+++

Текст статьи...
```

Открываем файл в ~~Vim~~ [^1] своем любимом редакторе. Пишем текст в формате Markdown. При сохранении, изменения должны отражаться на сайте.

[^1]: Это скорее шутка, так как Vim известен своей недружелюбностью. Этот пост я пишу в [Gnome Text Editor](https://apps.gnome.org/ru/TextEditor/).

![Первый пост в Hugo](hugo-first-post.webp)

На этом знакомство с Hugo заканчиваем, но еще предстоит множество других изменений: язык, фото, виджеты, контакты, меню. Как это правильно сделать, как правило пишет автор темы. В нашем случае это [stack.jimmycai.com](https://stack.jimmycai.com/).

Одно последнее напутствие: [конвертируем формат конфигурации с TOML на YAML](https://transform.tools/toml-to-yaml). Это сохранит нам рассудок при изменении вложенных структур, таких как меню и виджеты. Соответственно, меняем расширение файла с `.toml` на `.yaml`. Hugo сам поймет, что формат изменился.

### Публикация на GitHub

Теперь при пуше изменений в GitHub мы не увидим наш прекрасный новый сайт. Дело в том, что GitHub по умолчанию берет содержимое корневой директории и отправляет его в сервис GitHub Pages. Это поведение можно изменить, указав в настройках репозитория (Settings, Pages) использовать директорию docs (другого выбора не дано, или root, или docs). В [конфигурации Hugo](https://gohugo.io/getting-started/configuration/#publishdir) указать, чтоб сайт создавался в директории docs вместо public: `publishdir: docs`. Можно использовать репозиторий в таком виде, а можно генерировать сайт на стороне GitHub через Actions.

## GitHub Actions

При детальном рассмотрении, мы увидим, что GitHub использует пайплайн сборки Jekyll для нашего сайта, даже если мы не хотим этого: зачем разворачивать образ операционной системы и устанавливать кучу программ просто чтоб загрузить куда-то HTML файлы? Звучит как тотальный оверинжиниринг. Но что поделать, используем или дефолтный, или свой.

Hugo предоставляет нам [готовый скрипт по сборке сайта в GitHub Pages](https://gohugo.io/hosting-and-deployment/hosting-on-github/). Директории `public` и `resources/_gen` можно теперь не отслеживать и добавить в .gitignore, они нам не понадобятся для публикации. 

Создаем файл `.github/workflows/hugo.yaml` и копируем в него содержимое:

```yaml
# Sample workflow for building and deploying a Hugo site to GitHub Pages
name: Deploy Hugo site to Pages

on:
  # Runs on pushes targeting the default branch
  push:
    branches:
      - main

  # Allows you to run this workflow manually from the Actions tab
  workflow_dispatch:

# Sets permissions of the GITHUB_TOKEN to allow deployment to GitHub Pages
permissions:
  contents: read
  pages: write
  id-token: write

# Allow only one concurrent deployment, skipping runs queued between the run in-progress and latest queued.
# However, do NOT cancel in-progress runs as we want to allow these production deployments to complete.
concurrency:
  group: "pages"
  cancel-in-progress: false

# Default to bash
defaults:
  run:
    shell: bash

jobs:
  # Build job
  build:
    runs-on: ubuntu-latest
    env:
      HUGO_VERSION: 0.134.2
    steps:
      - name: Install Hugo CLI
        run: |
          wget -O ${{ runner.temp }}/hugo.deb https://github.com/gohugoio/hugo/releases/download/v${HUGO_VERSION}/hugo_extended_${HUGO_VERSION}_linux-amd64.deb \
          && sudo dpkg -i ${{ runner.temp }}/hugo.deb          
      - name: Install Dart Sass
        run: sudo snap install dart-sass
      - name: Checkout
        uses: actions/checkout@v4
        with:
          submodules: recursive
          fetch-depth: 0
      - name: Setup Pages
        id: pages
        uses: actions/configure-pages@v5
      - name: Install Node.js dependencies
        run: "[[ -f package-lock.json || -f npm-shrinkwrap.json ]] && npm ci || true"
      - name: Build with Hugo
        env:
          HUGO_CACHEDIR: ${{ runner.temp }}/hugo_cache
          HUGO_ENVIRONMENT: production
          TZ: America/Los_Angeles
        run: |
          hugo \
            --gc \
            --minify \
            --baseURL "${{ steps.pages.outputs.base_url }}/"          
      - name: Upload artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: ./public

  # Deployment job
  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: ubuntu-latest
    needs: build
    steps:
      - name: Deploy to GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
```

В настройках репозитория Pages указываем, использовать для сборки не бранч, а Action.

![Успешный пайплайн в GitHub](github-actions.gif)

Теперь при пуше в Github, он сам будет создавать директорию public и отправлять ее в сервис Pages.

## Свой домен

GitHub бесплатно предоставляет нам свой субдомен с именем пользователя: [malchikovma.github.io](malchikovma.github.io). Но что, если мы хотим иметь свой, да еще и с сертификатом? У нас есть такая возможность. У нашего провайдера домена (в моем случае это рег.ру), в разделе "DNS-серверы и управление зоной" указываем следующие значения:

|Тип записи|Префикс|Значение             |
|----------|-------|---------------------|
|A         |@      |185.199.108.153      |
|A         |@      |185.199.109.153      |
|A         |@      |185.199.110.153      |
|A         |@      |185.199.111.153      |
|CNAME     |www    |malchikovma.github.io|

`@` здесь означает сам наш домен, malchikovma.ru, а www - его субдомен, www.malchikovma.ru.

Предполагаю, что 4 IP адреса требуются для распределения нагрузки по 4 серверам, а www-запись для получения сертификата Let's Encrypt. Подобную систему мы использовали в проекте "Конструктор витрин"

После этого, на странице настроек Pages, в разделе "Custom domain" указываем наш домен. После проверки DNS, можно указать "Enforce HTTPS". Теперь наш сайт доступен по адресу [malchikovma.ru](https://malchikovma.ru).

