#!/bin/bash

if [[ -z "$1" || -z "$2" ]]; then
    echo "Помилка: Вкажіть два параметри - висоту ялинки та ширину снігу!" >&2
    exit 1
fi

if ! [[ "$1" =~ ^[0-9]+$ ]] || ! [[ "$2" =~ ^[0-9]+$ ]]; then
    echo "Помилка: Аргументи повинні бути додатними цілими числами!" >&2
    exit 1
fi

height=$1
snow_width=$2

draw_tier() {
    local width=$1   
    local height=$2  
    local tier_width=$((width - 2)) 
    local skip_first=$3 

    local i=1
    while [ $i -le $height ]; do
        if [[ $skip_first -eq 1 && $i -eq 1 ]]; then
            i=$((i + 1))
            continue  
        fi

        row_width=$((2 * i - 1)) 
        if ((i % 2 == 1)); then
            char="*"
        else
            char="#"
        fi

        local padding=$(((tier_width - row_width) / 2)) 

        printf "%*s" "$padding" ""
        
        local symbols=""
        for j in $(seq 1 $row_width); do
            symbols="${symbols}${char}"
        done
        printf "%s\n" "$symbols"
        
        i=$((i + 1))
    done
}

draw_trunk() {
    local width=$1
    local trunk_width=3
    local padding=$(((width - trunk_width) / 2 - 1)) 

    local i=0
    until [ $i -ge 2 ]; do
        printf "%*s###\n" "$padding" ""
        i=$((i + 1))
    done
}

draw_snow() {
    local width=$1
    local snow_line=""
    
    for ((j = 1; j <= width; j++)); do
        snow_line="${snow_line}*"
    done
    
    printf "%s\n" "$snow_line"
}

height=$((height / 2 * 2))        
snow_width=$((snow_width / 2 * 2 + 1))


if [[ $height -lt 4 || $snow_width -lt 5 ]]; then
    echo "Помилка: Мінімальна висота - 4, мінімальна ширина снігу - 5!" >&2
    exit 1
fi

tier_height=$((height / 2)) 

# ===== Вивід ялинки =====
draw_tier "$snow_width" "$tier_height" 0  # Перший ярус
draw_tier "$snow_width" "$tier_height" 1  # Другий ярус без першої зірки
draw_trunk "$snow_width"  # Стовбур
draw_snow "$snow_width"   # Сніг

exit 0
