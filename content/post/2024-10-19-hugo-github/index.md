+++
title = 'Как хостить сайт на GitHub с Hugo'
date = 2024-10-18T16:33:17+03:00
draft = true
image = 'hugo-logo.webp'
+++

## Введение

GitHub позволяет использовать репозиторий как статический веб-сайт. Существует такой инструмент, как генератор статического сайт (SSG). Так почему бы не подружить их?

> GitHub из коробки работает с Jekyll, но я уже потратил вечер на изучание Hugo.

Для этого нам понадобится знание git! А при работе с репозиторием по ssh, еще и умение работать с ключами шифрования.

Описываю опыт как бы от своего лица, так что все упоминания malchikovma нужно осознанно заменить на свои собственные.

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

Переходим в новую директорию, открываем ~~vim~~ свой любимый редактор и создаем файл `index.html` с содержимым "Hello GitHub Pages!".

Публикуем изменения и в течение минуты наше приветствие будет доступно по адресу [malchikovma.github.io](https://malchikovma.github.io).

```sh
git add .
git commit -m 'Initial commit'
git push -u origin main
```

При коммите git может запросить ваши данные. Я завел удобный скрипт на такой случай. Выполняем команды по очереди

```sh
git config user.email 'malchikovma@gmail.com'
git config user.name 'Mikhail Malchikov'
```

## Настройка Hugo

## GitHub Actions

## Свой домен
