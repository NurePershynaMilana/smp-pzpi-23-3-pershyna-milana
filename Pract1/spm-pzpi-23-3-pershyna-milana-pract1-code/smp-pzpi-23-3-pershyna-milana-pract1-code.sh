#!/bin/bash

# Перевіряємо, чи передано два аргументи
if [[ -z "$1" || -z "$2" ]]; then
    echo "Помилка: Вкажіть два параметри - висоту ялинки та ширину снігу!" >&2
    exit 1
fi

# Отримуємо числа з аргументів (тільки цілі позитивні значення)
if ! [[ "$1" =~ ^[0-9]+$ ]] || ! [[ "$2" =~ ^[0-9]+$ ]]; then
    echo "Помилка: Аргументи повинні бути додатними цілими числами!" >&2
    exit 1
fi

# Присвоюємо параметри змінним
height=$1
snow_width=$2

# Функція для друку одного ярусу ялинки
draw_tier() {
    local width=$1   # Ширина снігу
    local height=$2  # Висота одного ярусу
    local tier_width=$((width - 2)) # Ширина ярусу (на 2 менше, ніж сніг)
    local skip_first=$3 # Пропустити перший ряд (для другого ярусу)

    for ((i = 1; i <= height; i++)); do
        if [[ $skip_first -eq 1 && $i -eq 1 ]]; then
            continue  # Пропускаємо перший ряд для другого ярусу
        fi

        row_width=$((2 * i - 1)) # Кількість символів у рядку
        # Визначаємо символ ('*' або '#')
        if ((i % 2 == 1)); then
            char="*"
        else
            char="#"
        fi

        local padding=$(((tier_width - row_width) / 2)) # Відступи для центрування

        printf "%*s" "$padding" ""
        printf "%s" "$(printf "%${row_width}s" | tr ' ' "$char")"
        printf "\n"
    done
}

# Функція для друку стовбура
draw_trunk() {
    local width=$1
    local trunk_width=3
    local padding=$(((width - trunk_width) / 2))

    for ((i = 0; i < 2; i++)); do
        printf "%*s###\n" "$padding" ""
    done
}

# Функція для друку снігу
draw_snow() {
    local width=$1
    printf "%s\n" "$(printf "%${width}s" | tr ' ' '*')"
}

# Округлення до потрібних значень
height=$((height / 2 * 2))        # Округлення до парного числа
snow_width=$((snow_width / 2 * 2 + 1))  # Округлення до непарного числа

# Мінімальні допустимі значення
if [[ $height -lt 4 || $snow_width -lt 5 ]]; then
    echo "Помилка: Мінімальна висота - 4, мінімальна ширина снігу - 5!" >&2
    exit 1
fi

tier_height=$((height / 2))  # Кількість рядків у кожному ярусі

# ===== Вивід ялинки =====
draw_tier "$snow_width" "$tier_height" 0  # Перший ярус
draw_tier "$snow_width" "$tier_height" 1  # Другий ярус без першої зірки
draw_trunk "$snow_width"  # Стовбур
draw_snow "$snow_width"   # Сніг

exit 0
