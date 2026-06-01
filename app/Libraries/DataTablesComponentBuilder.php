<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;

class DataTablesComponentBuilder 
{
    public static function actionButton(array $route, $module, $customButtons = [])
    {
        $container = "<div class='d-flex gap-1 justify-content-center'>";
        
        foreach($customButtons as $customButton)
        {
            if((Session::get('permissions')[$customButton['module']][$customButton['modulePermission']] ?? false) && isset($customButton['html']))
            {
                $container .= $customButton['html'];
            }
        }

        if((Session::get('permissions')[$module]['read'] ?? false) && isset($route['show']))
        {
            $showButton = "
                <a href='" . $route['show'] . "' type='button' class='btn btn-sm btn-light-primary text-primary'>
                    <i class='ti ti-eye fs-4'></i>
                </a>
            ";
            $container .= $showButton;
        }

        if((Session::get('permissions')[$module]['update'] ?? false) && isset($route['edit']))
        {
            $editButton = "
                <a href='" . $route['edit'] . "' type='button' class='btn btn-sm btn-light-secondary text-secondary'>
                    <i class='ti ti-edit fs-4'></i>
                </a>
            ";
            $container .= $editButton;
        }

        if((Session::get('permissions')[$module]['delete'] ?? false) && isset($route['delete']))
        {
            $deleteButton = "
                <form action='" . $route['delete'] . "' method='POST' class='btn btn-sm btn-light-danger text-danger delete-button'>
                    <input type='hidden' name='_method' value='DELETE'></input>
                    <input type='hidden' name='_token' value='".csrf_token()."'></input>
                    <i class='ti ti-trash fs-4'></i>
                </form>
            ";
            $container .= $deleteButton;
        }
        
        if((Session::get('permissions')[$module]['restore'] ?? false) && isset($route['restore']))
        {
            $restoreButton = "
                <form action='" . $route['restore'] . "' method='POST' class='btn btn-sm btn-light-danger text-danger restore-button'>
                    <input type='hidden' name='_method' value='PATCH'></input>
                    <input type='hidden' name='_token' value='".csrf_token()."'></input>
                    <i class='ti ti-history fs-4'></i>
                </form>
            ";
            $container .= $restoreButton;
        }

        return $container . "</div>";
    }
}
