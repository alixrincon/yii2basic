<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php
$form = ActiveForm::begin([
            "id" => "login-form",
            "method" => "post",
            "enableClientValidation" => true,
        ])
?>

<div class="form-group">
    <?= $form->field($model, "nombre")->input("text"); ?>            
</div>
<div class="form-group">
    <?= $form->field($model, "email")->input("email"); ?>        
</div>
<?= Html::submitButton("enviar", ["class" => "btn btn-primary"]) ?>
<?php //$form->end(); ?>
<?php ActiveForm::end(); ?>

<!-- 
<? //= $form->field($model, 'nombre')->textInput()->hint('Please enter your name')->label('Name')  ?> 

-->