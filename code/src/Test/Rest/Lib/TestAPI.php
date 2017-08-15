<?php
namespace Test\Rest\Lib;

use Test\Rest\Lib\API;
use Test\Rest\Model\User;

class TestAPI extends API
{
  protected $user;

  public function __construct($container)
  {
    parent::__construct($container);
    $this->user = New User($container);
  }

  protected function user($args)
  {
    if ($this->method === 'GET') {
      if(isset($args[0])) {
        if($this->user->get($args[0])) {
          return $this->user->get($args[0]);
        } else {
          $this->status = 400;
          $message = array('status' => 'error', 'message' => 'User not found!');
          return $message;
        }
      }

      return $this->user->getAll();
    } elseif ($this->method === 'POST') {

      if($userId = $this->user->add($this->params)) {
        $message = array('status' => 'OK', 'message' => 'User created!', 'user_id' => $userId);

        return $message;
      } else {
        $this->status = 400;
        $message = array('status' => 'error', 'message' => 'User NOT created!');

        return $message;
      }
    } elseif ($this->method === 'PUT') {
      if($this->user->set($args[0], $this->params) === 1) {
        $message = array('status' => 'OK', 'message' => 'User updated!');

        return $message;
      } else {
        $this->status = 400;
        $message = array('status' => 'error', 'message' => 'User NOT updated!');

        return $message;
      }
    } elseif ($this->method === 'DELETE') {
      if($this->user->del($args[0]) === 1) {
        $message = array('status' => 'OK', 'message' => 'User deleted!');

        return $message;
      } else {
        $this->status = 400;
        $message = array('status' => 'error', 'message' => 'User NOT deleted!');

        return $message;
      }
    }
  }

  protected function userImage($args)
  {
    if ($this->method === 'POST') {
      $target_dir = BASE_DIR . "/files/";
      $target_file = $target_dir . basename($_FILES["file"]["name"]);
      $uploadOk = 1;
      $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
      $check = getimagesize($_FILES["file"]["tmp_name"]);
      if($check !== false) {
          $uploadOk = 1;
      } else {
          $msg = "File is not an image.";
          $uploadOk = 0;
      }

      // Check file size
      if ($_FILES["file"]["size"] > 2000000) {
          $msg = "Sorry, your file is too large.";
          $uploadOk = 0;
      }

      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
      }

      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
          $this->status = 400;
          $message = array('status' => 'error', 'message' => $msg);

          return $message;
      // if everything is ok, try to upload file
      } else {
          if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
              $msg = shell_exec('curl -XPOST "https://api.olx.com/v1.0/users/images" -F "file=@' . $target_file . '"');
              $msg = json_decode($msg);
              $url = 'https://images01.olx-st.com/' . $msg->url;
              $r = $this->user->set($args[0], array('picture' => $url));
              if($r === 1) {
                $message = array('status' => 'OK', 'message' => 'Image uploaded!', 'url' => $url);

                return $message;
              } else {
                $this->status = 400;
                $message = array('status' => 'error', 'message' => 'There is a problem updating the new image url in database!');

                return $message;
              }
          } else {
              $this->status = 400;
              $message = array('status' => 'error', 'message' => $msg);
          }
      }

      return $message;
    } else {
      $this->status = 400;
      return array('status' => 'error', 'message' => 'To upload files you should use http POST method');
    }
  }
}
