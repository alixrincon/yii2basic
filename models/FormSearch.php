<?php

namespace app\models;

use Yii;
use yii\base\model;

class FormSearch extends model
{

    public $q;

    public function rules()
    {
        return [
            ["q", "match", "pattern" => "/^[0-9a-záéíóúñ\s]+$/i", "message" => "Solo se permite numeros y letras"],

        ];
    }

    public function attributeLabels()
    {
        return [
            'q' => 'Buscar: ',
        ];
    }
}


