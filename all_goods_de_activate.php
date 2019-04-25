<?php
# выдергиваем все ozon_id из БД и в зависимости от параметра - деактивируем или активируем все товары в цикле
ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
ini_set('memory_limit', '2000000000');

# Определяем объекты для работы с базой
$modx->runSnippet('classes');
$record = new good_bd;
$ozon = new ozon_api;

# Получаем массив id продуктов c остатками больше нуля

$arr_product_ids=$record->get_all_ozon_id();


# Получаем данные по тому, что делать

if (isset($_GET['activate']) &&  $_GET['activate']==0){
    echo "Сейчас все будет - деактивируем";
    $com=$ozon->deactivate_all_good($arr_product_ids);
} else  if(isset($_GET['activate']) &&  $_GET['activate']==1) {
    echo "Сейчас все будет - активируем";
    $com=$ozon->activate_all_good($arr_product_ids);
    
} else {
    echo "No Update - ";
    die ("No required param 'activate' with correct value 1 or 0");
}



# Пробегаем по всем элементам и деактивируем



//$com=$ozon->curl_ozon($comand_url,$json);

print_r ($com);