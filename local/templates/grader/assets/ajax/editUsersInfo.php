<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;

if ($_REQUEST) {
    $lastName = $_REQUEST['lastName'];
    $firstName = $_REQUEST['firstName'];
    $middleName = $_REQUEST['middleName'];
    $userName = $_REQUEST['userName'];
    $birthDate = $_REQUEST['birthDate'];
    $gender = $_REQUEST['gender'] === 'male'?'M':'F';
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];

    $fields = array(
        'LAST_NAME' => $lastName,//no
        'NAME' => $firstName,
        'SECOND_NAME' => $middleName,
        'LOGIN' => $userName,//no
        'PERSONAL_BIRTHDAY' => $birthDate,//no
        'PERSONAL_GENDER' => $gender,
        'PERSONAL_PHONE' => $phone,
        'EMAIL' => $email,
    );
    $USER->update($USER->getId(), $fields);
    echo $USER->LAST_ERROR;
}