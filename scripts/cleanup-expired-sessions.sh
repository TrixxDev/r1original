#!/bin/bash
# Удалить просроченные file-сессии (по умолчанию старше 120 минут).
# Запуск на сервере: bash scripts/cleanup-expired-sessions.sh
# Или: bash scripts/cleanup-expired-sessions.sh 180

cd "$(dirname "$0")/.." || exit 1

MINUTES="${1:-120}"
DIR="storage/framework/sessions"

if [ ! -d "$DIR" ]; then
  echo "Каталог не найден: $DIR"
  exit 1
fi

echo "Удаляю file-сессии старше ${MINUTES} минут в $DIR ..."
BEFORE=$(du -sh "$DIR" 2>/dev/null | cut -f1)
find "$DIR" -type f -mmin +"$MINUTES" ! -name '.gitignore' ! -name '.*' -delete
AFTER=$(du -sh "$DIR" 2>/dev/null | cut -f1)
echo "Было: $BEFORE → стало: $AFTER"
echo "Готово."
