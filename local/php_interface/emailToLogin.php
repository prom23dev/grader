<?php

AddEventHandler("main", "OnBeforeUserLogin", "DoBeforeUserLoginHandler");
function DoBeforeUserLoginHandler(&$arFields)
{
    $userLogin = $_POST["USER_LOGIN"];
    if (isset($userLogin))
    {
        $isEmail = strpos($userLogin,"@");
        if ($isEmail>0)
        {
            $arFilter = Array("EMAIL"=>$userLogin);
            $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $arFilter);
            if($res = $rsUsers->Fetch())
            {
                if($res["EMAIL"]==$arFields["LOGIN"])
                    $arFields["LOGIN"] = $res["LOGIN"];
            }
        }
    }
}