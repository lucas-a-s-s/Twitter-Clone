<?php
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{

  public function timeline()
  {
    $this->validaAutenticacao();
    $tweet = Container::getModel('tweet');
    $tweet->__set('id_usuario', $_SESSION['id']);
    $this->view->tweets = $tweet->getAll();
    $this->render('timeline');

  }
  public function tweet()
  {
    $this->validaAutenticacao();

    $tweet = Container::getModel('tweet');
    $tweet->__set('tweet', $_POST['tweet']);
    $tweet->__set('id_usuario', $_SESSION['id']);

    $tweet->salvar();
    header('location: /timeline');
  }
  public function validaAutenticacao()
  {
    session_start();

    if (
      !isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])
    ) {
      header('location: /?login=error');
    } else {

    }
  }
  public function quemSeguir()
  {
    $this->validaAutenticacao();

    $usuarios = array();
    $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

    if (!empty($pesquisarPor)) {

      $usuario = container::getModel('Usuario');
      $usuario->__set('nome', $pesquisarPor);
      $usuarios = $usuario->getAll();
    }
    $this->view->usuarios = $usuarios;
    $this->render('quemSeguir');

  }

}
?>