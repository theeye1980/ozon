<?php
define('MODX_API_MODE', true);
require_once('/var/www/www-root/data/mail.ftp-technolight.ru/index.php');

$modx=new modX();
$modx->initialize('web');

ini_set('max_execution_time', 1800); //300 seconds = 5 minutes
ini_set('memory_limit', '1200000000');



# Подключаем классы и создаем объекты для их использования

$modx->runSnippet('classes');

$my_snip = new useful;
$record = new rec_bd;

echo "<pre>";

# выдернули все товары с сайта? На выходе $vendorCode_bd
    
$query = "select id,vendorCode from goods";
$statement_tv = $modx->query($query);
$result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
$i=1;
foreach($result_tv as $r_tv){
        $vendorCode_bd[$i]= $r_tv['vendorCode'];
        $id_bd[$i]= $r_tv['id'];
        $i++;
}

# задаем адрес xml

$all_xml_url='https://fandeco.ru/media/brand_update_stat/teleport_xml/all_teleport_files.xml';
$all_xml= simplexml_load_file($all_xml_url); // грузим файлик
$arr_all_xml=objectToArray($all_xml);

print_r($arr_all_xml);

foreach($arr_all_xml['file'] as $url_xml){
    
    $xml= simplexml_load_file($url_xml); // грузим файлик
    $obj=objectToArray($xml);
     
    # Выцепляем кодовое название производителя и получаем имя бренда в таблице БД
     
    $vendor_code =  $obj['@attributes']['vendor'];
    $vendor=vendor_code($vendor_code);
     
    echo $vendor.' - '.$vendor_code;
     
    # Сбрасываем все остатки в базе на 0 с этим производителем
    
     $record->set_stock_zero_to_brand($vendor);


    # Пробегаем по всем товарам и устанавливаем нужные значения
     foreach($xml->item as $i){
         
         # ищем id в таблице бд по товару
         $index=array_search($e['article'],$vendorCode_bd);
         
         $e=objectToArray($i);
         
         # устанавливаем значения цены и остатка в БД
         $record->stocks_prices_from_teleport($id_bd[$index],$e['price'],$e['stock'],$e['action_price'],$e['sale']);
         echo '<br>';
         //print_r ($e);
     }
    
}



 
function objectToArray($object) {
        if( !is_object($object) && !is_array($object)) {
            return $object;
        }
        if( is_object($object )) {
            $object = get_object_vars($object);
        }
        return array_map('objectToArray', $object);
}
function vendor_code($str){
			$b2=array(
			'album'=>'Album',
            'ambiente_by_brizzi'=>'AMBIENTE by Brizzi',
            'anthologie_quartett'=>'Anthologie Quartett',
            'ares'=>'Ares',
            'arlight'=>'Arlight',
            'aromas'=>'Aromas',
            'arte_lamp'=>'ArteLamp',
            'artemide'=>'Artemide',
            'arti_lampadari'=>'Arti Lampadari',
            'avmazzega'=>'AVmazzega',
            'avonni'=>'Avonni',
            'axo_light'=>'AXO Light',
            'baga'=>'Baga',
            'beby'=>'Beby',
            'bieffe'=>'Bieffe',
            'b_lux'=>'B.Lux',
            'bohemia_ivele'=>'Bohemia Ivele',
            'britop'=>'Britop',
            'brizzi_modern'=>'Brizzi Modern',
            'catellani_smith'=>'Catellani&Smith',
            'ceelite'=>'Ceelite',
            'ceramiche_carlesso'=>'Ceramiche Carlesso',
            'chiaro'=>'Chiaro',
            'citilux'=>'Citilux',
            'crystal_lux'=>'Crystal Lux',
            'dark'=>'Dark',
            'delta_light'=>'Delta Light',
            'dio_d_arte'=>'Dio D\'Arte',
            'divinare'=>'Divinare',
            'eglo'=>'Eglo',
            'egoluce'=>'Egoluce',
            'elvan'=>'Elvan',
            'esedra'=>'Esedra',
            'eurolampart'=>'Eurolampart',
            'fabbian'=>'Fabbian',
            'fagerhult'=>'Fagerhult',
            'faldi'=>'Faldi',
            'faustig'=>'Faustig',
            'favourite'=>'Favourite',
            'florian_light'=>'FlorianLight',
            'flos'=>'Flos',
            'fontana_arte'=>'Fontana Arte',
            'foscarini'=>'Foscarini',
            'gamma_delta'=>'Gamma Delta Group',
            'gauss'=>'Gauss',
            'globo_new'=>'Globo',
            'globo'=>'Globo Акция',
            'ideal_lux'=>'Ideal Lux',
            'idee_design'=>'Idee Design Light',
            'idl'=>'IDL',
            'idlamp'=>'IDLamp',
            'iguzzini'=>'Iguzzini',
            'il_fanale'=>'Il Fanale',
            'ilfari'=>'Ilfari',
            'imas'=>'Imas',
            'ingo_maurer'=>'Ingo Maurer',
            'intra_lighting'=>'Intra Lighting',
            'itre'=>'Itre',
            'ivalo'=>'Ivalo',
            'karman'=>'Karman',
            'kolarz'=>'Kolarz',
            'lamp_international'=>'Lamp International',
            'leds'=>'Leds',
            'leucos'=>'Leucos',
            'lightstar'=>'Lightstar',
            'linea_light'=>'Linea Light',
            'luceplan'=>'LucePlan',
            'lucia_tucci'=>'Lucia Tucci',
            'lucide'=>'Lucide',
            'lumen_center'=>'Lumen Center Italia',
            'lussole'=>'Lussole',
            'luxit'=>'Luxit',
            'luxona'=>'Luxona',
            'mantra'=>'Mantra',
            'marchetti'=>'Marchetti',
            'mar_illuminazione'=>'Mar Illuminazione',
            'markslojd'=>'Markslojd',
            'marset'=>'Marset',
            'martini'=>'Martini',
            'masca'=>'Masca',
            'masiero'=>'Masiero',
            'maytoni'=>'Maytoni',
            'milan_iluminacion'=>'Milan Iluminacion',
            'lampadari'=>'M.M.Lampadari',
            'modular'=>'Modular',
            'molto_luce'=>'Molto Luce',
            'moooi'=>'Moooi',
            'morosini'=>'Morosini',
            'mw_light'=>'Mw-Light',
            'neon_night'=>'Neon-Night',
            'nervilamp'=>'Nervilamp',
            'novotech'=>'Novotech',
            'nowodvorski'=>'Nowodvorski',
            'odeon_light'=>'Odeon Light',
            'ole'=>'OLE',
            'oligo'=>'Oligo',
            'orion'=>'Orion',
            'osgona'=>'Osgona',
            'pallucco_italia'=>'Pallucco Italia',
            'panzeri'=>'Panzeri',
            'passeri'=>'Passeri',
            'penta'=>'Penta',
            'prearo'=>'Prearo',
            'prisma'=>'Prisma',
            'riperlamp'=>'Riperlamp',
            'rotaliana'=>'Rotaliana',
            'royal_botania'=>'Royal Botania',
            'sil_lux'=>'Sil Lux',
            'slamp'=>'Slamp',
            'slv'=>'SLV',
            'sonex'=>'Sonex',
            'spot_light'=>'Spotlight',
            'stil_lux'=>'STIL LUX',
            'st_luce'=>'ST Luce',
            'studio_italia'=>'Studio Italia Design',
            'sylcom'=>'Sylcom',
            'sylvania'=>'Sylvania',
            'terzani'=>'Terzani',
            'tobias_grau'=>'Tobias Grau',
            'toplight'=>'Toplight',
            'traddel'=>'Traddel',
            'vele_luce'=>'Vele Luce',
            'vibia'=>'Vibia',
            'vintage'=>'Vintage',
            'vistosi'=>'Vistosi',
            'wever_ducre'=>'Wever&Ducre',
            'avrora'=>'Аврора',

		);
		return strtr($str, $b2);
	}