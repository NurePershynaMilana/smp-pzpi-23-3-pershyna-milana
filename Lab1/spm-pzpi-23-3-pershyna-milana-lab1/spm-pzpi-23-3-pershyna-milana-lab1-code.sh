#!/bin/bash

# Скрипт для перетворення CSV-файлу з розкладом CIST до формату для імпорту в Google Календар
# Назва скрипта: pershyna-milana-task2.sh

# Функція для відображення довідки
show_help() {
    echo "Використання: $0 [опції] [академічна_група] [файл_із_cist.csv]"
    echo ""
    echo "Опції:"
    echo "  --help     Показати довідку та вийти"
    echo "  --version  Показати версію та вийти"
    echo "  -q, --quiet  Не виводити інформацію в стандартний потік виведення"
    echo ""
    echo "Приклад використання:"
    echo "  $0 'ПЗПІ-23-1' TimeTable_16_03_2025.csv"
    echo "  $0 --help"
    echo "  $0 --version"
    echo ""
    exit 0
}

# Функція для відображення версії
show_version() {
    echo "Версія: 3.3"
    echo "Дата: $(date +%d.%m.%Y)"
    exit 0
}

# Функція для обробки помилок
handle_error() {
    local error_code=$1
    local error_message=$2
    
    echo "Помилка: $error_message" >&2
    exit $error_code
}

# Функція для анімації завантаження
show_animation() {
    local pid=$1
    local message=$2
    local symbols=('⣾' '⣽' '⣻' '⢿' '⡿' '⣟' '⣯' '⣷')
    local delay=0.1
    
    while kill -0 $pid 2>/dev/null; do
        for symbol in "${symbols[@]}"; do
            echo -ne "\r\033[K$symbol $message"
            sleep $delay
        done
    done
    echo -ne "\r\033[K"
}

# Функція для вибору файлу
select_file() {
    echo "Доступні CSV-файли:" >&2
    select file in $(ls *.csv 2>/dev/null); do
        if [ -n "$file" ]; then
            echo "$file"
            return 0
        else
            handle_error 2 "Невірний вибір. Спробуйте ще раз."
        fi
    done
}

# Функція для вибору академічної групи
select_group() {
    local file=$1
    
    # Перевірка наявності файлу
    if [ ! -f "$file" ]; then
        handle_error 2 "Файл '$file' не знайдено"
    fi
    
    # Отримання списку груп
    local groups=$(grep -o "ПЗПІ-[0-9][0-9]-[0-9]" "$file" | sort -u)
    
    if [ -z "$groups" ]; then
        handle_error 3 "У файлі '$file' не знайдено жодної академічної групи"
    fi
    
    echo "Доступні академічні групи:" >&2
    select group in $groups; do
        if [ -n "$group" ]; then
            echo "$group"
            return 0
        else
            handle_error 3 "Невірний вибір. Спробуйте ще раз."
        fi
    done
}

# Функція для створення CSV-файлу для Google Календаря
process_file() {
    local group=$1
    local input_file=$2
    local quiet=$3
    
    if [ "$quiet" != "true" ]; then
        echo "Починаю обробку файлу: $input_file"
        echo "Пошук даних для групи: $group"
    fi
    
    # Створюємо підпроцес для обробки даних
    {
        # Формування назви вихідного файлу
        local date_part=$(echo "$input_file" | grep -o '[0-9]\+_[0-9]\+_[0-9]\+' | head -1)
        local output_file="Google_TimeTable_${date_part}.csv"
        
        # Створення тимчасового файлу
        local temp_file=$(mktemp)
        
        # Додавання заголовків для Google Календаря
        echo "Subject,Start Date,Start Time,End Date,End Time,Description" > "$temp_file"
        
        # Підготовка лічильників для кожного типу заняття і дисципліни
        declare -A lesson_counters
        
        # Обробка файлу
        grep -i "$group" "$input_file" | while IFS= read -r line; do
            # Пропускаємо заголовок
            if [[ "$line" == *"Тема"* ]]; then
                continue
            fi
            
            # Видалення лапок
            line=$(echo "$line" | sed 's/"//g')
            
            # Розбиваємо рядок на частини
            IFS=, read -r subject start_date start_time end_date end_time description remainder <<< "$line"
            
            # Перевірка чи відповідає рядок групі
            if [[ "$subject" == *"$group"* ]]; then
                # Отримуємо частину після назви групи (дисципліна і тип заняття)
                clean_subject=$(echo "$subject" | sed "s/$group - //;s/ $group$//;s/ $group //")
                
                # Зберігаємо оригінальний предмет для опису
                original_subject=$clean_subject
                
                # Визначаємо тип заняття та дисципліну
                local lesson_type=""
                if [[ "$clean_subject" == *"Лб"* ]]; then
                    lesson_type="Лб"
                elif [[ "$clean_subject" == *"Пз"* ]]; then
                    lesson_type="Пз"
                elif [[ "$clean_subject" == *"Лк"* ]]; then
                    lesson_type="Лк"
                fi
                
                local discipline=$(echo "$clean_subject" | awk '{print $1}')
                local key="${discipline}_${lesson_type}"
                
                # Збільшуємо лічильник для цього типу заняття і дисципліни
                if [[ -z "${lesson_counters[$key]}" ]]; then
                    lesson_counters[$key]=1
                else
                    lesson_counters[$key]=$((${lesson_counters[$key]} + 1))
                fi
                
                # Формуємо назву для Subject
                clean_subject="${clean_subject} #${lesson_counters[$key]}"
                
                # Форматування дат (з "DD.MM.YYYY" в "MM/DD/YYYY")
                local formatted_start_date=$(echo "$start_date" | awk -F. '{print $2"/"$1"/"$3}')
                local formatted_end_date=$(echo "$end_date" | awk -F. '{print $2"/"$1"/"$3}')
                
                # Формуємо опис (Description)
                local full_description="${group} - ${original_subject} - ${description}"
                
                # Виведення форматованого рядка
                echo "$clean_subject,$formatted_start_date,$start_time,$formatted_end_date,$end_time,$full_description" >> "$temp_file"
            fi
            
            # Додаємо невелику затримку для імітації роботи
            sleep 0.01
        done
        
        # Перевірка чи є дані у тимчасовому файлі (більше ніж 1 рядок - тільки заголовок)
        if [ $(wc -l < "$temp_file") -le 1 ]; then
            rm "$temp_file"
            echo "Помилка: Групу '$group' не знайдено у файлі '$input_file'" >&2
            exit 4
        fi
        
        # Перенесення даних з тимчасового файлу у вихідний
        mv "$temp_file" "$output_file"
        
        # Чекаємо ще трохи для гарного ефекту завершення
        sleep 1
    } &
    
    # Отримуємо PID підпроцесу
    local pid=$!
    
    # Показуємо анімацію, поки підпроцес працює
    if [ "$quiet" != "true" ]; then
        show_animation $pid "Трансформую CSV-файл для Google Календаря..."
    fi
    
    # Чекаємо завершення підпроцесу
    wait $pid
    
    # Перевіряємо код завершення підпроцесу
    local exit_code=$?
    if [ $exit_code -ne 0 ]; then
        exit $exit_code
    fi
    
    # Формування назви вихідного файлу (для повідомлення)
    local date_part=$(echo "$input_file" | grep -o '[0-9]\+_[0-9]\+_[0-9]\+' | head -1)
    local output_file="Google_TimeTable_${date_part}.csv"
    
    if [ "$quiet" != "true" ]; then
        echo "Обробку успішно завершено!"
        echo "Файл '$output_file' успішно створено для групи '$group'"
    fi
    
    return 0
}

# Перевірка та обробка параметрів командного рядка
QUIET=false

# Якщо немає параметрів, показати допомогу
if [ $# -eq 0 ]; then
    show_help
fi

# Обробка параметрів
while [ $# -gt 0 ]; do
    case "$1" in
        --help)
            show_help
            ;;
        --version)
            show_version
            ;;
        -q|--quiet)
            QUIET=true
            shift
            ;;
        -*)
            handle_error 1 "Невідома опція: $1"
            ;;
        *)
            # Якщо це перший позиційний параметр, це група
            if [ -z "$GROUP" ]; then
                GROUP="$1"
                shift
            # Якщо це другий позиційний параметр, це файл
            elif [ -z "$FILE" ]; then
                FILE="$1"
                shift
            else
                handle_error 1 "Зайвий параметр: $1"
            fi
            ;;
    esac
done

# Якщо файл не вказано, запропонувати вибір
if [ -z "$FILE" ]; then
    if [ "$QUIET" != "true" ]; then
        FILE=$(select_file)
    else
        # У тихому режимі спробуємо знайти найновіший файл
        FILE=$(ls -t *.csv 2>/dev/null | head -1)
        if [ -z "$FILE" ]; then
            handle_error 2 "Не знайдено жодного файлу розкладу"
        fi
    fi
fi

# Перевірка існування файлу
if [ ! -f "$FILE" ]; then
    handle_error 2 "Файл '$FILE' не знайдено"
fi

# Якщо група не вказана, запропонувати вибір
if [ -z "$GROUP" ]; then
    if [ "$QUIET" != "true" ]; then
        GROUP=$(select_group "$FILE")
    else
        # У тихому режимі спробуємо знайти першу групу
        GROUP=$(grep -o "ПЗПІ-[0-9][0-9]-[0-9]" "$FILE" | sort -u | head -1)
        if [ -z "$GROUP" ]; then
            handle_error 3 "У файлі '$FILE' не знайдено жодної академічної групи"
        fi
    fi
fi

# Перевірка на наявність однієї групи
if [ -z "$GROUP" ]; then
    GROUP_COUNT=$(grep -o "ПЗПІ-[0-9][0-9]-[0-9]" "$FILE" | sort -u | wc -l)
    if [ "$GROUP_COUNT" -eq 1 ]; then
        GROUP=$(grep -o "ПЗПІ-[0-9][0-9]-[0-9]" "$FILE" | sort -u | head -1)
        if [ "$QUIET" != "true" ]; then
            echo "В файлі присутня лише одна група: $GROUP. Використовуємо її для обробки."
        fi
    fi
fi

# Показуємо початок роботи скрипту
if [ "$QUIET" != "true" ]; then
    echo "Починаю роботу скрипту..."
    echo "Вибрана група: $GROUP"
    echo "Вхідний файл: $FILE"
fi

# Обробка файлу
process_file "$GROUP" "$FILE" "$QUIET"

# Повідомлення про завершення роботи скрипту
if [ "$QUIET" != "true" ]; then
    echo "Скрипт успішно завершив роботу!"
fi

exit 0