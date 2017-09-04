<?php

namespace app\models;

use Yii;
use yii\base\model;

class ValidarFormularioAjax extends model
{

    public $nombre;
    public $email;

    public function rules()
    {

        return [
            ['nombre', 'required', 'message' => 'Campo Requerido'],
            ['nombre', 'match', 'pattern' => "/^.{3,50}$/", 'message' => "Mínimo de 3, Máximo de 50"],
            ['nombre', 'match', 'pattern' => "/^[0-9a-z]+$/i", 'message' => "Solo se aceptan letras y numeros"],
            ['email', 'required', 'message' => 'Campo Requerido'],
            ['email', 'match', 'pattern' => "/^.{5,80}$/", 'message' => "Mínimo de 5, Máximo de 80"],
            ['email', 'email', 'message' => "Formato no valido"],
            ['email', 'email_existe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre' => 'Nombre:',
            'email' => 'Email:',

        ];
    }

    public function email_existe($attribute)
    {
        $email = ["alix@mail.com", "shayanna@mail.com"];

        foreach ($email as $value) {
            if ($this->email == $value) {
                $this->addError($attribute, "Email ya existe");
                return true;
            } else {
                return false;
            }
        }
    }


}
