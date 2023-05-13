<?php
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{

  public function timeline()
  {
    session_start();

    if (
      !empty($_SESSION['id']) && !empty($_SESSION['nome'])
    ) {
      $this->render('timeline');
    } else {
      header('location: /?login=error');
    }



    // $usuario = Container::getModel('Usuario');

    // $usuario->__set('email', $_POST['email']);
    // $usuario->__set('senha', $_POST['senha']);



    // $retorno = $usuario->autenticar();

    // if (!empty($usuario->__get('id')) && !empty($usuario->__get('nome'))) {

    //   session_start();

    //   $_SESSION['id'] = $usuario->__get('id');
    //   $_SESSION['nome'] = $usuario->__get('nome');

    //   header('location: /timeline');
    // } else {
    //   header('location: /?login=error');
    // }


  }


}
?>