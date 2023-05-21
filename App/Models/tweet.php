<?php
namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{
  private $id;
  private $id_usuairo;
  private $tweet;
  private $data;

  public function __get($atributo)
  {
    return $this->$atributo;
  }

  public function __set($atributo, $valor)
  {
    $this->$atributo = $valor;
  }

  public function salvar()
  {
    $query = "INSERT INTO 
                tweets(
                  id_usuario,
                  tweet
                )values(
                :id_usuario,
                :tweet
                )";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
    $stmt->bindValue(':tweet', $this->__get('tweet'));
    $stmt->execute();

    return $this;
  }
  //recuperar 
  public function getAll()
  {
    $query = "SELECT
                tw.id,
                tw.id_usuario,
                tw.tweet,
                DATE_FORMAT(tw.data,'%d/%m/%Y %h:%i') data,
                us.nome
              FROM
                tweets tw
                left join usuarios us on (tw.id_usuario = us.id)
              WHERE
                tw.id_usuario = :id
              or 
                tw.id_usuario in (
                  select 
                    id_usuario_seguindo
                  from
                    usuario_seguidores 
                  where
                    id_usuario = :id
                )
                
              ORDER BY tw.data desc
                ";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':id', $this->__get('id_usuario'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
  public function getPorPagina($limit, $offset)
  {
    $query = "SELECT
                tw.id,
                tw.id_usuario,
                tw.tweet,
                DATE_FORMAT(tw.data,'%d/%m/%Y %h:%i') data,
                us.nome
              FROM
                tweets tw
                left join usuarios us on (tw.id_usuario = us.id)
              WHERE
                tw.id_usuario = :id
              or 
                tw.id_usuario in (
                  select 
                    id_usuario_seguindo
                  from
                    usuario_seguidores 
                  where
                    id_usuario = :id
                )
              ORDER BY tw.data desc
              limit
                  $limit
              offset
                  $offset
                ";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':id', $this->__get('id_usuario'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
  public function getTotalRegistros()
  {
    $query = "SELECT
                count(*) total
              FROM
                tweets tw
                left join usuarios us on (tw.id_usuario = us.id)
              WHERE
                tw.id_usuario = :id
              or 
                tw.id_usuario in (
                  select 
                    id_usuario_seguindo
                  from
                    usuario_seguidores 
                  where
                    id_usuario = :id
                )
                ";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':id', $this->__get('id_usuario'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function validarCadastro()
  {
    $valido = true;

    if (strlen($this->__get('nome')) < 3) {
      $valido = false;
    }
    if (strlen($this->__get('email')) < 3) {
      $valido = false;
    }
    if (strlen($this->__get('senha')) < 3) {
      $valido = false;
    }

    return $valido;
  }

  public function getUsuarioPorEmail()
  {
    $qry = "SELECT
              nome,
              email
            FROM
              usuarios
            WHERE
              email = :email";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
  public function autenticar()
  {
    $qry = "SELECT
              id,
              nome,
              email
            FROM
              usuarios
            WHERE
              email = :email
            and
              senha = :senha";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->bindValue(':senha', $this->__get('senha'));
    $stmt->execute();

    $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!empty($usuario['id']) && !empty($usuario['nome'])) {
      $this->__set('id', $usuario['id']);
      $this->__set('nome', $usuario['nome']);
    }

    return $this;
  }
  public function excluir()
  {
    $qry = "DELETE
            FROM
              tweets
            WHERE
              id = :id_tweet
    ";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':id_tweet', $this->__get('id'));
    $stmt->execute();

    return true;
  }

}



?>