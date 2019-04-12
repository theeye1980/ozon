<?php
$path='index.php';
echo $path;
define('MODX_API_MODE', true);
require_once($path);

$modx=new modX();
$modx->initialize('web');

$modx->runSnippet('classes');

ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
ini_set('memory_limit', '2000000000');

# создаем объект для записи данных в базу

$record = new rec_bd;
$attr_convert = new attr_values;


# получаем входные данные - сколько товаров обновлять и надо ли вообще

$page=$record->get_up_page();
$page_size=200;




# формируем запрос на получение списка товаров, результат в массив $get_arr

$out='';
$url='/v1/product/list';

$json = $modx->getChunk('list_page_size',array('page'=>$page,'page_size'=>$page_size));

$get_ex=$modx->runSnippet('curl_ozon',array('url'=>$url,
                                            'json'=>$json
                        ));
$get_arr=json_decode($get_ex, true);

# Получаем цифру, до какой страницы мы должны дойти

$num_pages=round($get_arr['result']['total'] / $page_size);
if($num_pages<=(int)$page) exit("Обновление достигнуто, дальше не паримся");


echo  $get_arr['result']['total'] . " Надо пройти $num_pages страниц по $page_size, текущая - $page <pre>";
//    print_r ($get_arr);
echo "</pre>";
# Пробегаем по каждому товару в полученном массиве

foreach($get_arr['result']['items'] as $good){

    # Получить по товару из локальной таблицы
    
    $good_item = new good_bd;
    $good_item->get_good_by_art ($good['offer_id']);
    
    if($good_item->ozon_product_id == 0) {
        echo "<br> по товару" . $good['offer_id'] . "нет записи ozon_product_id <br>"; 
        # записываем в БД ozon_product_id
        $record->add_ozon_id($good['product_id'],$good['offer_id']);
    }
    
    
    echo $good['product_id'].'--'.$good['offer_id'].'<br>';
    $product_id=$good['product_id'];
    
    
    # Устанавливаем товару характеристики в Озоне
    
    $out = $modx->runSnippet('update_good_json',array(
                    'offer_id'=> $good['product_id'],
                    'record'=> $record,
                    'attr_convert'=>$attr_convert,
                    'good_item' => $good_item
        ));
   
    echo $out.'<br><hr>';
      
    # Записываем в БД  этот ответ
        
    $record->rec_update_good($good['product_id'],$out);
    
   
}
 # записываем в БД, что эта страница пройдена
    $page=(int)$page+1;
    echo "Устанавливаем $page";
    $record->inc_get_up_page($page);