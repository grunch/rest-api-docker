<?php

namespace Test\Rest\Model;

class User
{
  protected $id;
  protected $name;
  protected $picture;
  protected $address;
  protected $container;

  public function __construct($container)
  {
    $this->container = $container;
    $this->_conn = $this->container['db'];
  }
  /**
   * queryBuilder wrapper
   */
  protected function queryBuilder()
  {
      return $this->_conn->createQueryBuilder();
  }

  public function getAll()
  {
      $users = $this->queryBuilder()
        ->select("*")
        ->from("users")
        ->execute()
        ->fetchAll()
      ;

      return $users;
  }

  public function get($id)
  {
    $users = $this->queryBuilder()
      ->select("*")
      ->from("users")
      ->where("id = ?")
      ->setParameter(0, $id)
      ->execute()
      ->fetch()
    ;

    return $users;
  }

  public function set($id, $params)
  {
    if(isset($params['name'])) {
      $set[] = "name=:name";
    }
    if(isset($params['picture'])) {
      $set[] = "picture=:picture";
    }
    if(isset($params['address'])) {
      $set[] = "address=:address";
    }
    $set = implode(",", $set);
    $query = $this->_conn->prepare("UPDATE users set {$set} WHERE id =:id");

    if(isset($params['name'])) {
      $query->bindValue(":name", $params['name']);
    }
    if(isset($params['picture'])) {
      $query->bindValue(":picture", $params['picture']);
    }
    if(isset($params['address'])) {
      $query->bindValue(":address", $params['address']);
    }
    $query->bindValue(":id", $id);
    $query->execute();

    return $query->rowCount();
  }

  public function add($params)
  {
    if(!isset($params['name'])) {
      return false;
    }
    $set[] = "name=:name";
    if(isset($params['address'])) {
      $set[] = "address=:address";
    }

    $set = implode(",", $set);
    $query = $this->_conn->prepare("INSERT users set {$set}");

    if(isset($params['name'])) {
      $query->bindValue(":name", $params['name']);
    }

    if(isset($params['address'])) {
      $query->bindValue(":address", $params['address']);
    }
    if($query->execute()) {
      return $this->_conn->lastInsertId();
    } else {
      return false;
    }
  }

  public function del($id)
  {
    $d = $this->_conn->delete("users", array('id' => $id));

    return $d;
  }
}
