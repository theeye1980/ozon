<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/good_bd.php';

if(! isset($offer_id)) $offer_id=$_POST['offer_id'];

# создаем объекты для записи данных в базу и конвертации id свойств

$record = new rec_bd;
$attr_convert = new attr_values;

# полуаем данные по товару в таблице
  $good_item = new good_bd;
  $good_item->get_good($offer_id);
  
# получаем дополнительные данные по товару
    $attributes='';
    if($good_item->arm_color) {
        $arm_color_id=$attr_convert->armature_color_value($good_item->arm_color);
        $attributes .= $modx->getChunk('attr_armature_color',array('armature_color_id' => $arm_color_id));
    }
    if($good_item->soket) {
        $lamp_socket_id=$attr_convert->lamp_socket_collection($good_item->soket);
        $attributes .= $modx->getChunk('attr_lamp_socket',array('lamp_socket_id' => $lamp_socket_id));
    }   
    if($good_item->col_plafond) {
        $plafond_color_id=$attr_convert->plafond_color_value($good_item->col_plafond);
        $attributes .= $modx->getChunk('attr_plafond_color',array('plafond_color_id' => $plafond_color_id));
    }
    if($good_item->mat_plafond) {
        $plafond_material_id=$attr_convert->plafond_material_value($good_item->mat_plafond);
        $attributes .= $modx->getChunk('attr_plafond_material',array('plafond_material_id' => $plafond_material_id));
    }

# получаем дополнительные изображения
    $dop_image='';
    $arr_dop_image=json_decode($good_item->dop_picture, true);
    $arr_dop_image_count=count($arr_dop_image);
    if($arr_dop_image_count>1){
        for($m=1;$m<=$arr_dop_image_count-1;$m++){
             
              $dop_image .= $modx->getChunk('dop_image',array('dop_image' => $arr_dop_image[$m]));   
            
        }
    }
    
    
# формируем json для обновления товара
$out=$modx->getChunk('update_good_json',array(
                    'product_id' => $good_item->ozon_product_id,
                    'barcode' => $good_item->barcode,
                    'description' => $good_item->description_gen,
                    'name' => $good_item->name,
                    'vendor' => $good_item->vendor,
                    'vendor_code' => $good_item->vendorCode,
                    'height' => $good_item->height_box,
                    'depth' => $good_item->length_box,
                    'width' => $good_item->width_box,
                    'weight' => $good_item->weight,
                    'main_img' => $good_item->main_picture,
                    'type_of' => $good_item->type_of[$good_item->cat],
                    'attributes' => $attributes,
                    'dop_image' => $dop_image
                    ));
# вызываем метод обновления товара


$url='/v1/products/update';
$json = $out;
echo $json.'<br>';
//echo $good_item->dop_picture.'<br>';
//echo "всего изображений $arr_dop_image_count <br>";
//echo "код доп. изображени $dop_image";


print_r($arr_dop_image);
$get_ex=$modx->runSnippet('curl_ozon',array('url'=>$url,
                                            'json'=>$json
                        ));

# записываем в БД ошибку, статус и дату пробы записи, если запись делалась более, чем час назад
        $today = getdate();
		$today_ts=$today[0];
        $delta=(int)$today_ts-$good_item->last_ozon_up;
        //echo "delta - $delta";
        if($delta>3600) $record->rec_update_good($offer_id,$get_ex);

return $get_ex;