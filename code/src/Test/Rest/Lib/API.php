<?php

namespace Test\Rest\Lib;

abstract class API
{
  /**
  * Property: method
  * The HTTP method this request was made in, either GET, POST, PUT or DELETE
  */
  protected $method = '';
  /**
  * Property: endpoint
  * The Model requested in the URI. eg: /files
  */
  protected $endpoint = '';
  /**
  * Property: verb
  * An optional additional descriptor about the endpoint, used for things that can
  * not be handled by the basic methods. eg: /files/process
  */
  protected $verb = '';
  /**
  * Property: args
  * Any additional URI components after the endpoint and verb have been removed, in our
  * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
  * or /<endpoint>/<arg0>
  */
  protected $args = Array();
  /**
  * Property: params
  * Stores the input of the PUT request
  */
  protected $params = null;
  /**
  * Property: request
  * Stores the request from the client
  */
  protected $request = null;
  /**
  * Property: container
  * Stores the Dependency Injection Container
  */
  protected $container;
  /**
   * Status code of the response
   */
  protected $status = 200;

  /**
  * Constructor: __construct
  * Allow for CORS, assemble and pre-process the data
  */
  public function __construct($container)
  {
    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    $this->container = $container;
    $this->request = $this->_cleanInputs($_SERVER['REQUEST_URI']);
    $this->args = explode('/', trim($this->request, '/'));
    $this->args = array_filter($this->args);
    $this->endpoint = array_shift($this->args);
    if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
      $this->verb = array_shift($this->args);
    }

    $this->method = $_SERVER['REQUEST_METHOD'];

    switch($this->method) {
      case 'POST':
        $this->params = $this->_cleanInputs($_POST);
        break;
      case 'DELETE':
      case 'PUT':
        parse_str(file_get_contents("php://input"), $this->params);
        break;
      case 'GET':
        break;
      default:
        $this->_response('Invalid Method', 405);
        break;
    }
  }

  public function processAPI()
  {
    if (method_exists($this, $this->endpoint)) {
      $data = $this->{$this->endpoint}($this->args);
      return $this->_response($data, $this->status);
    }

    return $this->_response(array("status" => "error", "msg" => "No Endpoint: " .$this->endpoint), 404);
  }

  private function _response($data, $status = 200)
  {
    header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));

    return json_encode($data, JSON_UNESCAPED_SLASHES);
  }

  private function _cleanInputs($data)
  {
    $clean_input = Array();
    if (is_array($data)) {
      foreach ($data as $k => $v) {
        $clean_input[$k] = $this->_cleanInputs($v);
      }
    } else {
      $clean_input = trim(strip_tags($data));
    }

    return $clean_input;
  }

  private function _requestStatus($code)
  {
    $status = array(
      200 => 'OK',
      400 => 'Bad Request',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      500 => 'Internal Server Error',
    );

    return ($status[$code])?$status[$code]:$status[500];
  }
}
