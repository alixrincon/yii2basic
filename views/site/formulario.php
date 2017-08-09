<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>
<h1>Formulario</h1>
<h3><?= $mensaje; ?><h3>
        <?=
        Html::beginForm(
                Url::toRoute('site/request'), //action
                "get", //method  
                ["class" => 'form-inline']      //options
        );
        ?>
        <div class =" form-group">
            <?= Html::label("Introduce tu nombre", "nombre"); ?>
            <?= Html::textInput("nombre", NULL, ["clas" => "form-control"]); ?>    
        </div>

        <?= Html::submitInput("Enviar", ["class" => "btn btn-primary"]) ?>

        <?=
        Html::endForm();
        ?>