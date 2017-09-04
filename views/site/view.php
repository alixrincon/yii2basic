<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\widgets\LinkPager;

?>

<a href="<?= Url::toRoute("site/create") ?>">Ir a Crear Alumnos</a>

<?php
$f = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("site/view"),
    "enableClientValidation" => true,
]);
?>

<div class="form-group">
    <?= $f->field($form, "q")->input("search") ?>

    <?= Html::submitButton("Buscar", ["class" => "btn btn-primary"]) ?>
</div>
<?php $f->end(); ?>

<h3> <?= $search ?></h3>


<h3>Lista de Alumnos</h3>

<table class="table table-bordered">
    <tr>
        <th>id Alumno</th>
        <th>Nombres</th>
        <th>Apellidos</th>
        <th>Clase</th>
        <th>Nota Final</th>
        <th></th>
        <th></th>
        <?php foreach ($model as $row): ?>
    <tr>
        <td><?= $row->id_alumno ?></td>
        <td><?= $row->nombres ?></td>
        <td><?= $row->apellidos ?></td>
        <td><?= $row->clase ?></td>
        <td><?= $row->nota_final ?></td>
        <td><a href="<?= Url::toRoute(["site/update", "id_alumno" => $row->id_alumno]) ?>">Editar</a>
        </td>
        <td>
            <a href="#" data-toggle="modal" data-target="#id_alumno_<?= $row->id_alumno ?>">Eliminar</a>
            <div class="modal fade" role="dialog" id="id_alumno_<?= $row->id_alumno ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Eliminar Alumno</h4>
                        </div>
                        <div class="modal-body">
                            <p>Estas seguro de Eliminar el almuno con Nro. <?= $row->id_alumno ?>?</p>
                        </div>
                        <div class="modal-footer">
                            <?= Html::beginForm(Url::toRoute('site/delete'), "POST") ?>
                            <input type="hidden" name="id_alumno" value="<?= $row->id_alumno ?>">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Eliminar</button>
                            <?= Html::endForm() ?>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </td>
    </tr>
    <?php endforeach; ?>
    </tr>
</table>


<?= LinkPager::widget([
    "pagination" => $pages,
]) ?>




