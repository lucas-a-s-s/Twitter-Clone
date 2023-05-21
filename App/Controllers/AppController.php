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

    $total_registro_pagina = 5;
    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
    $deslocamento = (($pagina - 1) * $total_registro_pagina);

    $this->view->tweets = $tweet->getPorPagina($total_registro_pagina, $deslocamento);
    $total_tweets = $tweet->getTotalRegistros();
    $this->view->totalPaginas = ceil($total_tweets['total'] / $total_registro_pagina);
    $this->view->pagina_ativa = $pagina;


    $usuario = Container::getModel('Usuario');
    $usuario->__set('id', $_SESSION['id']);
    $this->view->infoUsuario = $usuario->getInfoUsuario();
    $this->view->totalTweets = $usuario->getTotalTweets();
    $this->view->totalSeguindo = $usuario->getTotalSeguindo();
    $this->view->totalSeguidores = $usuario->getTotalSeguidores();



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
      $usuario->__set('id', $_SESSION['id']);
      $usuarios = $usuario->getAll();
    }
    $this->view->usuarios = $usuarios;
    $this->render('quemSeguir');

  }
  public function acao()
  {
    $this->validaAutenticacao();

    $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';

    $usuario = container::getModel('Usuario');
    $usuario->__set('id', $_SESSION['id']);

    if ($acao == 'seguir') {
      $usuario->seguirUsuario($id_usuario_seguindo);
    } elseif ($acao == 'deixar_de_seguir') {
      $usuario->deixarSeguirUsuario($id_usuario_seguindo);
    }
    header('Location: /quem_seguir');

  }

}
?>