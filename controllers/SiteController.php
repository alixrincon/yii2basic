<?php

namespace app\controllers;

use Symfony\Component\DomCrawler\Form;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ValidarFormulario;
//modelos necesarios para trabajar via Ajax
use app\models\ValidarFormularioAjax;
use yii\widgets\ActiveForm;
use yii\web\Response;
use app\models\FormAlumnos;
use app\models\Alumnos;
use app\models\FormSearch;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\helpers\Url;
//Registro de usuario
use app\models\FormRegister;
use app\models\Users;

class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    public function actionFormulario($mensaje = NULL) {

        return $this->render("formulario", ["mensaje" => $mensaje]);
    }

    public function actionRequest() {

        $mensaje = NULL;
        if (isset($_REQUEST['nombre'])) {
            $mensaje = "Has enviado correctamente el nombre: " . $_REQUEST["nombre"];
        }
        $this->redirect(['site/formulario', "mensaje" => $mensaje]);
    }

    public function actionValidarformulario() {

        $model = new ValidarFormulario();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                //realizar las operaciones del caso
            } else {
                $model->getErrors();
            }
        }
        return $this->render("validarformulario", ["model" => $model]);
    }

    public function actionValidarformularioajax() {

        $model = new ValidarFormularioAjax();
        $msg = NULL;

        if ($model->load(Yii::$app->request->post())) {
            $isvalid = ActiveForm::validate($model);
            if (count($isvalid) == 0) {
                //realizar las operaciones necesarias
                $msg = "Registro Exitoso";
                $model = new ValidarFormularioAjax;
                Yii::$app->response->refresh();
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $isvalid;
            }
        }
        return $this->render("validarformularioajax", ["msg" => $msg, "model" => $model]);
    }

    public function actionCreate() {
        $model = new FormAlumnos();
        $msg = NULL;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = new Alumnos;

                $table->setAttributes($model->attributes, false);
                if ($table->insert()) {
                    $msg = "Registro Exitoso";
                    $model = new FormAlumnos();
                } else {
                    $model->getErrors();
                }
            }
        }
        return $this->render("create", ["model" => $model, "msg" => $msg]);
    }

    /* public function actionView()
      {
      $table = new Alumnos;
      $model = $table->find()->all();

      $form = new FormSearch();
      $search = null;
      if ($form->load(Yii::$app->request->get())) {
      if ($form->validate()) {
      $search = Html::encode($form->q);  //metodo encode no va a permitir ataques de tipo XSS
      $query = "SELECT * FROM alumnos WHERE id_alumno LIKE '%$search%' OR ";
      $query .= "nombres LIKE '%$search%' OR apellidos LIKE '%$search%' ";
      $model = $table->findBySql($query)->all();  //cambiamos el valor de la variable model
      } else {
      $model->getErrors();
      }
      }
      return $this->render("view", ["model" => $model, "form" => $form, "search" => $search]);
      } */

    public function actionView() {
        $form = new FormSearch();
        $search = null;

        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $search = Html::encode($form->q);
                $table = Alumnos::find()
                        ->where(["like", "id_alumno", $search])
                        ->orWhere(["like", "nombres", $search])
                        ->orWhere(["like", "apellidos", $search]);
                $count = clone $table;  //clonamos el query
                $pages = new Pagination([
                    'totalCount' => $count->count(),
                    'pageSize' => 1,
                ]);

                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            } else {
                $form->getErrors();
            }
        } else {
            $table = Alumnos::find();
            $count = clone $table;
            $pages = new Pagination([
                "pageSize" => 1,
                "totalCount" => $count->count(),
            ]);
            $model = $table
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }


        return $this->render("view", ["model" => $model, "form" => $form, "search" => $search, "pages" => $pages]);
    }

    public function actionDelete() {

        if (Yii::$app->request->post()) {
            $id_alumno = Html::encode($_POST['id_alumno']);
            if ((int) $id_alumno) {
                if (Alumnos::deleteAll("id_alumno = :id_alumno", [":id_alumno" => $id_alumno])) {
                    echo "Alumno con id " . $id_alumno . " Eliminado satisfactoriamente. Redireccionando...";
                    echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("site/view") . "'>";
                } else {
                    echo "Error al eliminar el registro";
                    echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("site/view") . "'>";
                }
            } else {
                echo "Error al eliminar el registro";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("site/view") . "'>";
            }
        } else {
            return $this->redirect("site/view");
        }
    }

    public function actionUpdate() {
        $model = new FormAlumnos();
        $msg = null;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $table = Alumnos::findOne($model->id_alumno);
                if ($table) {
                    $table->nombres = $model->nombres;
                    $table->apellidos = $model->apellidos;
                    $table->clase = $model->clase;
                    $table->nota_final = $model->nota_final;
                    if ($table->update()) {
                        $msg = "El alumno ha sido editado correctamente...";
                    } else {
                        $msg = "El alumno no ha podido ser actualizado correctamente";
                    }
                } else {
                    $msg = "El alumno seleccionado no ha sido encontrado";
                }
            } else {
                $model->getErrors();
            }
        }
        if (Yii::$app->request->get("id_alumno")) {
            $id_alumno = Html::encode($_GET["id_alumno"]);
            if ((int) $id_alumno) {
                $table = Alumnos::findOne($id_alumno);
                if ($table) {
                    $model->id_alumno = $table->id_alumno;
                    $model->nombres = $table->nombres;
                    $model->apellidos = $table->apellidos;
                    $model->clase = $table->clase;
                    $model->nota_final = $table->nota_final;
                } else {
                    return $this->redirect(["site/view"]);
                }
            } else {
                return $this->redirect(["site/view"]);
            }
        } else {
            return $this->redirect(["site/view"]);
        }


        return $this->render("update", ["model" => $model, "msg" => $msg]);
    }

    /*
     * Código para el controlador con la acción Register que utilizaremos para registrar al usuario, la acción Confirm que permitirá 
     * activar al usuario cuando haga click en el enlace adjunto en el correo electrónico
     *  y el método privado randKey($str, $long) para generar claves aleatorias para las columnas authKey y accessToken 
     */

    private function randKey($str = '', $long = 0) {
        $key = null;
        $str = str_split($str);
        $start = 0;
        $limit = count($str) - 1;
        for ($x = 0; $x < $long; $x++) {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }

    public function actionConfirm() {
        $table = new Users;
        if (Yii::$app->request->get()) {

            //Obtenemos el valor de los parámetros get
            $id = Html::encode($_GET["id"]);
            $authKey = $_GET["authKey"];

            if ((int) $id) {
                //Realizamos la consulta para obtener el registro
                $model = $table
                        ->find()
                        ->where("id=:id", [":id" => $id])
                        ->andWhere("authKey=:authKey", [":authKey" => $authKey]);

                //Si el registro existe
                if ($model->count() == 1) {
                    $activar = Users::findOne($id);
                    $activar->activate = 1;
                    if ($activar->update()) {
                        echo "Enhorabuena registro llevado a cabo correctamente, redireccionando ...";
                        echo "<meta http-equiv='refresh' content='8; " . Url::toRoute("site/login") . "'>";
                    } else {
                        echo "Ha ocurrido un error al realizar el registro, redireccionando ...";
                        echo "<meta http-equiv='refresh' content='8; " . Url::toRoute("site/login") . "'>";
                    }
                } else { //Si no existe redireccionamos a login
                    return $this->redirect(["site/login"]);
                }
            } else { //Si id no es un número entero redireccionamos a login
                return $this->redirect(["site/login"]);
            }
        }
    }

    public function actionRegister() {
        //Creamos la instancia con el model de validación
        $model = new FormRegister;

        //Mostrará un mensaje en la vista cuando el usuario se haya registrado
        $msg = null;

        //Validación mediante ajax
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //Validación cuando el formulario es enviado vía post
        //Esto sucede cuando la validación ajax se ha llevado a cabo correctamente
        //También previene por si el usuario tiene desactivado javascript y la
        //validación mediante ajax no puede ser llevada a cabo
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                //Preparamos la consulta para guardar el usuario
                $table = new Users;
                $table->username = $model->username;
                $table->email = $model->email;
                //Encriptamos el password
                $table->password = crypt($model->password, Yii::$app->params["salt"]);
                //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
                //clave será utilizada para activar el usuario
                $table->authKey = $this->randKey("abcdef0123456789", 200);
                //Creamos un token de acceso único para el usuario
                $table->accessToken = $this->randKey("abcdef0123456789", 200);

                //Si el registro es guardado correctamente
                if ($table->insert()) {
                    //Nueva consulta para obtener el id del usuario
                    //Para confirmar al usuario se requiere su id y su authKey
                    $user = $table->find()->where(["email" => $model->email])->one();
                    $id = urlencode($user->id);
                    $authKey = urlencode($user->authKey);

                    $subject = "Confirmar registro";
                    $body = "<h1>Haga click en el siguiente enlace para finalizar tu registro</h1>";
                    $body .= "<a href='http://yii.local/index.php?r=site/confirm&id=" . $id . "&authKey=" . $authKey . "'>Confirmar</a>";

                    //Enviamos el correo
                    Yii::$app->mailer->compose()
                            ->setTo($user->email)
                            ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                            ->setSubject($subject)
                            ->setHtmlBody($body)
                            ->send();

                    $model->username = null;
                    $model->email = null;
                    $model->password = null;
                    $model->password_repeat = null;

                    $msg = "Enhorabuena, ahora sólo falta que confirmes tu registro en tu cuenta de correo";
                } else {
                    $msg = "Ha ocurrido un error al llevar a cabo tu registro";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render("register", ["model" => $model, "msg" => $msg]);
    }

}
