<?php
namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{
  private $id;
  private $nome;
  private $email;
  private $senha;

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
                usuarios(
                  nome,
                  email,
                  senha
                )values(
                :nome,
                :email,
                :senha
                )";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':nome', $this->__get('nome'));
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->bindValue(':senha', $this->__get('senha'));
    $stmt->execute();

    return $this;
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
  public function seguirUsuario($id_usuario_seguindo)
  {
    $qry = "INSERT INTO
              usuario_seguidores
              (
                id_usuario,
                id_usuario_seguindo
              )values(
                :id_usuario,
                :id_usuario_seguindo
              )";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':id_usuario', $this->__get('id'));
    $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
    $stmt->execute();

    return true;
  }
  public function deixarSeguirUsuario($id_usuario_seguindo)
  {
    $qry = "DELETE
            FROM
              usuario_seguidores
            WHERE
              id_usuario = :id_usuario
            AND
              id_usuario_seguindo = :id_usuario_seguindo
    ";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':id_usuario', $this->__get('id'));
    $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
    $stmt->execute();

    return true;
  }
  public function getAll()
  {
    $qry = "SELECT
              us.id,
              us.nome,
              us.email,
              (
                SELECT 
                  COUNT(*)
                FROM
                  usuario_seguidores usg
                WHERE
                  usg.id_usuario = :id_usuario
                and
                  us.id = usg.id_usuario_seguindo
              ) seguindo_sn
            FROM
              usuarios us
            WHERE
              us.nome like :nome
            AND
              us.id <> :id_usuario ";
    $stmt = $this->db->prepare($qry);
    $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');
    $stmt->bindValue(':id_usuario', $this->__get('id'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

}



?>