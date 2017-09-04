<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1>Validar Formulario Ajax</h1>
<h3><?= $msg; ?></h3>
<?php
$form = ActiveForm::begin([
    "id" => "loginajax-form",
    "method" => "post",
    "enableClientValidation" => false,
    "enableAjaxValidation" => true,

])
?>

<div class="form-group">
    <?= $form->field($model, "nombre")->input("text"); ?>
</div>
<div class="form-group">
    <?= $form->field($model, "email")->input("email"); ?>
</div>
<?= Html::submitButton("enviar", ["class" => "btn btn-primary"]) ?>

<?php ActiveForm::end(); ?>

