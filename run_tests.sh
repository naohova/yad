#!/bin/bash

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "Запуск тестов..."

# Проверяем наличие директории vendor
if [ ! -d "vendor" ]; then
    echo "Устанавливаем зависимости..."
    composer install
fi

# Проверяем наличие директории bootstrap
if [ ! -d "bootstrap" ]; then
    echo "Создаем директорию bootstrap..."
    mkdir bootstrap
fi

# Запускаем тесты
./vendor/bin/phpunit --testdox

# Проверяем результат выполнения тестов
if [ $? -eq 0 ]; then
    echo -e "\nВсе тесты пройдены успешно!"
else
    echo -e "\nНеудачные тесты:"
    echo -e "\nСтатистика:"
    ./vendor/bin/phpunit --testdox
fi 