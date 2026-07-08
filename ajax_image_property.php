<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');
use Bitrix\Main\Application;

\Bitrix\Main\Loader::includeModule('sale');
use \Bitrix\Sale\Order,
    \Bitrix\Sale;

use \Bitrix\Main\Web\Uri;

        $arResult = [];


$usePageNavigation = true;
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
$query = $uri->getQuery();
$number = 0;
$rest = 0;
  
if(!empty($request['page'])){
    $rest = $request['page'];
}
 

if(is_null($rest)){
    // $rest = 0;
    return false;
}

global $USER;



if($_GET['company']){
    $company_id=$_GET['company'];
}



if(!empty($company_id)){



    function getOrders($rest){


        $dateF = DateTime::createFromFormat('d.m.Y H:i:s', date('d.m.Y H:i:s'));
        $dateT = DateTime::createFromFormat('d.m.Y H:i:s', date('d.m.Y H:i:s'));
      
        $dateF->modify("-".$rest." month"); 


        $formattedDate = $dateF->modify('last day of this month');
        $formattedDate = $dateF->format('d.m.Y 00:00:00');  
 
        // $rest = $rest + 1;  
        $rest2 = $rest+1;         

        $dateT->modify("-".(int)$rest2. " month");  
        // $formattedDate2 = $dateT->modify('last day of this month');
        $formattedDate2 = $dateT->format('01.m.Y 00:00:00');
        $dateToCheck = strtotime($formattedDate2);
        $limitDateTimestamp = strtotime('01.01.2020 00:00:00');

 
        // $dateT->modify("-".(int)$rest2. " month");  
        // // $formattedDate2 = $dateT->modify('last day of this month');
        // $formattedDate2 = $dateT->format('01.m.Y H:i:s');  
      


        if ($dateToCheck > $limitDateTimestamp) {


        AddMessage2Log('wewerwrwrr');
        AddMessage2Log($dateToCheck);
        AddMessage2Log($limitDateTimestamp); 
    

 

            $dbRes = \Bitrix\Sale\Order::getList([  
                'select' => ['ID','DATE_INSERT','ACCOUNT_NUMBER','STATUS_ID','PRICE'],
      
                'filter' => [
                    '=PROPERTY_VAL.CODE' => 'URID',
                    '=PROPERTY_VAL.VALUE' => $_GET['company'],
                    // '>=DATE_INSERT' => $formattedDate2,
                    // '<=DATE_INSERT' => $formattedDate
                ],
                'runtime' => [ 
                    new \Bitrix\Main\Entity\ReferenceField( 
                        'PROPERTY_VAL',
                        '\Bitrix\sale\Internals\OrderPropsValueTable',
                        ["=this.ID" => "ref.ORDER_ID"],
                        ["join_type"=>"inner"]
                    ),
                ],
                'cache' => [
                    'ttl' => 10000
                ],

                'order' => ['ID' => 'DESC'], 

                //  'offset' => $rest*20,
                // 'limit' => 10,   


            ]);                    
      
            return $dbRes;
        }

        return false;
      


    }



function adjustRest($rest) {
 
    $count = 0;
    $dbRes = false;

    for ($i = 0; $count < 1; $i++) {   
        $dbRes = getOrders($rest);

        if ($dbRes && method_exists($dbRes, 'getSelectedRowsCount')) {
            $count = $dbRes->getSelectedRowsCount();
        } else {
            $count = 10; // Прерываем цикл в случае ошибки
            $dbRes = false; // Сбрасываем результат, чтобы вернуть false при ошибке
            break; // Выходим из цикла
        }

        if ($count < 1) {
            $rest++;
        }
    }

         ?><script>
                 
                pageNumber = parseInt(<?php echo json_encode($rest); ?>)+2;  
                 sessionStorage.setItem('myNumber', pageNumber);    
                if(pageNumber != null){ 
                    $(".arrow-7").attr("data-page-number", pageNumber);  
                }
            console.log('4444444444444gggggg1'); 
            console.log(pageNumber);   
        </script><?

 
    return $dbRes; // Возвращаем последний полученный результат
}


    $dbRes = getOrders($rest); 
    // $dbRes = adjustRest($rest);

     AddMessage2Log('sdfsaud9fg9w7gfe222');
        AddMessage2Log($rest);
        AddMessage2Log($request['page']); 
    // $count = $dbRes->getSelectedRowsCount(); 
    // if($count == 0){
    //     die(\Bitrix\Main\Web\Json::encode(['success' => false, 'message' => 'ordersareover']));
    // }
  
    //     if($count < 3){ 
    //     $rest++;
    //     $dbRes = getOrders($rest);
    // }  

    if($dbRes){
        
    // Получение даты первого заказа
    while ($order = $dbRes->fetch())
    { 

        AddMessage2Log('sdfsaud9fg9w7gfe');
        AddMessage2Log($order['ID']);



        $cacheTime = 10000000000; // Время кеширования в секундах (30 дней)
        $cacheManager = Application::getInstance()->getManagedCache();
        $cacheId = 'OrderListPersonal' . $order['ID'];
        // $cacheManager->clean($cacheId);  

        if($cacheManager->read($cacheTime, $cacheId)){ 
        
             $arResult['ORDERS'] = $cacheManager->get($cacheId);

        }else{
   
        $basket = Sale\Order::load($order['ID'])->getBasket();
        $arQuantityList = $basket->getQuantityList();

        $basketItems = [];



        if($basket) {
            foreach ($basket as $key => $item) {
                $fields = $item->getFieldValues();
                $basketItems[] = [
                    'ID' => $item->getProductId(),
                    'NAME' => $fields["NAME"],
                    'PRICE' => (int) $fields["PRICE"] * (int)$fields["QUANTITY"]
                ];
            }
        }

        $arResult['ORDERS'][$order['ID']]['ORDER_ITEMS'] = $basketItems;
        $arResult['ORDERS'][$order['ID']]['ACCOUNT_NUMBER'] = $order['ACCOUNT_NUMBER']; 
        $arResult['ORDERS'][$order['ID']]['DATE_INSERT'] = $order['DATE_INSERT'];  
        $arResult['ORDERS'][$order['ID']]['ID'] = $order['ID'];
        $arResult['ORDERS'][$order['ID']]['STATUS_ID'] = $order['STATUS_ID'];

        foreach ($basket as $item) { 
            $arr = [
                "ID" => $item->getProductId(),
                "COUNT" => $item->getQuantity(),
                "SUM" => $item->getFinalPrice(),
                "PRICE" => $item->getPrice(),
            ];

            $arResult['ORDERS'][$order['ID']]['COUNT_ELEMENT'][] = $arr;
            $arResult['ORDERS'][$order['ID']]['COUNT_ELEMENT_2'][$item->getProductId()] = $arr;

        }

         $arResult['ORDERS'][$order['ID']]['PRICE'] = $order['PRICE'];
       

        $cacheManager->set($cacheId,$arResult['ORDERS']);
 
        } 
    }
      
    }



} 


// $price = $order->getPrice();

if (!empty($arResult['ORDERS'])) {

    // Получить массив id товаров
    $arOfferIds = [];
    foreach ($arResult['ORDERS'] as $key => $arOrder) {
        if (!empty($arOrder['COUNT_ELEMENT']) && is_array($arOrder['COUNT_ELEMENT'])) {
            foreach($arOrder['COUNT_ELEMENT'] as $arItem) {
                if (!in_array($arItem['ID'], $arOfferIds)) {
                    $arOfferIds[] = $arItem['ID'];
                }
            }
        }
    }
//закешировать
    if (!empty($arOfferIds)) {
        // По ТП получить товары
        $arOfferProdocts = \CCatalogSKU::getProductList($arOfferIds);
    }

    foreach ($arResult['ORDERS'] as $key => $arOrder) {
        if (!empty($arOrder['COUNT_ELEMENT']) && is_array($arOrder['COUNT_ELEMENT'])) {
            $arProductIds = array_merge($arOfferIds, array_column($arOrder['COUNT_ELEMENT'], 'ID'));

            foreach ($arProductIds as $keyId => $id) {
                if (!empty($arOfferProdocts[$id]['ID'])) {
                    $arProductIds[$keyId] = $arOfferProdocts[$id]['ID'];
                }
            }
            $arResult['ORDERS'][$key]['PRODUCTS_ID'] = array_unique($arProductIds);
        }
    }
}
?>
