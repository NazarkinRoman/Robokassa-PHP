<?php

/* ===================================
 * Author: Nazarkin Roman
 * -----------------------------------
 * Contacts:
 * email - roman@nazarkin.su
 * icq - 642971062
 * skype - roman444ik
 * -----------------------------------
 * GitHub:
 * https://github.com/NazarkinRoman
 * ===================================
*/

/* простой пример проверки оплаты у себя на сервере */
$kassa = new Robokassa('merchant_login', 'pass1', 'pass2');

/* назначение параметров */
$kassa->OutSum  = $_POST['OutSum'];
$kassa->InvId   = $_POST['InvId'];

/* добавление кастомных полей из запроса */
$kassa->addCustomValues(array(
    'shp_user' => $_POST['shp_user'],
    'shp_someData' => $_POST['shp_someData']
));

/* проверка цифровой подписи запроса */
if($kassa->checkHash($_POST['SignatureValue']))
    echo 'Оплата проведена успешно!';
else
    echo 'Валидация не пройдена';