<?php

/**
 * Class Vista
 *
 * Handles the comunication with Vista RESTful API
 */
class Vista {

    /**
     * The file that contains
     */
    const ConfigFile = 'config.json';

    /**
     * The Rest Client Object
     *
     * @var     RestClient
     */
    private $restClient;

    /***
     * The client configuration
     *
     * @var     array
     */
    private $config = array();

    /**
     * The Vista Rest Method
     *
     * @var     string
     */
    private $vistaMethod;

    /**
     * ( GET | POST | PUT | DELETE )
     *
     * @var
     */
    private $requestMethod = 'get';

    /**
     * Parameter "pesquisa" or "cadastro" of Vista API
     *
     * @var     array
     */
    private $params = array();

    /**
     * Contains the method response
     *
     * @var array
     */
    private $result = array();

    /**
     * If request body must be empty
     *
     * @var bool
     */
    private $emptyBody = false;

    /**
     * Contains the method pagination
     *
     * @var array
     */
    private $pagination = array(
        'total'         => 0,
        'paginas'       => 0,
        'pagina'        => 0,
        'quantidade'    => 0
    );

    /**
     * Constructor
     *
     * Sets up the client
     */
    public function __construct() {

        $this->restClient = Services::get('restclient');
        $this->restClient->addHeader('Accept', 'application/json');
        $this->restClient->setFormat('json');

        $this->loadConfig();

        $this->restClient->setUrl($this->getURL());
        $this->restClient->addParam('key', $this->getConfig('key'));
    }

    /**
     * Loads the Vista API client configuration
     *
     * @throws  Zend_Exception
     */
    private function loadConfig() {

        $file = __DIR__ . '/' . self::ConfigFile;

        if (!is_file($file))
            throw new Zend_Exception('Vista configuration file missing');

        $config = file_get_contents($file);
        $config = json_decode($config, true);

        if (!$config || !isset($config['key']) || !isset($config['url']))
            throw new Zend_Exception('Vista configuration file is broken');

        $this->config = $config;

    }

    /**
     * Returns a client configuration parameter
     *
     * @param   string      $name       - The configuration name
     * @return  bool
     */
    public function getConfig($name) {

        if (!isset($this->config[$name])) return false;

        return $this->config[$name];
    }

    /**
     * Returns the processed URL
     *
     * @return  string
     */
    public function getURL() {

        $url = $this->getConfig('url');
        (strpos($url, 'http://') == 0 ||
            strpos($url, 'https://') == 0) || $url = 'http://' . $url;

        return trim($url, '/') . '/';

    }

    /**
     * Sets the method
     *
     * @param   string      $module     - ( imoveis | clientes | usuarios | agencias )
     * @param   string      $method     - ( listar | detalhes | buscaAvancada )
     */
    public function setVistaMethod($module, $method) {

        if (!$module) {
            $this->vistaMethod = $method;
            return;
        }
        $this->vistaMethod = $module . '/' . $method;
    }

    /**
     * Sets the request Method
     *
     * @param   string      $method     - ( get | post | put | delete )
     * @throws  Zend_Exception
     */
    public function setMethod($method) {

        if(!in_array($method, array('get', 'post', 'put', 'delete')))
            throw new Zend_Exception('Method ' . $method . ' is invalid. Must be "get", "post", "put" or "delete"');

        $this->requestMethod = $method;
    }

    /**
     * Sets the whole set of params
     *
     * @param   $params      - The parameters array
     */
    public function setParams(array $params) {

        $this->params = $params;
    }

    /**
     * Sets EmptyBody true|false
     *
     * @param $emptyBody
     */
    public function setEmptyBody($emptyBody) {

        if (!is_bool($emptyBody)) $emptyBody = false;
        $this->emptyBody = $emptyBody;
    }

    /**
     * Adds a field or set of fields
     *
     * Ex:  array( 'Foto' => array( 'Foto', 'Descricao' ) )
     * Ex2: 'Bairro'
     *
     * @param   string|array    $field      - A field or sets of field
     */
    public function addFieldParam($field) {

        $this->params['fields'][] = $field;
    }

    /**
     * Adds a filter
     *
     * Ex: array( 'Bairro' => array('Centro', 'Moema'), 'Codigo' => 10 )
     *
     * @param   array       $filter     - The filter set
     */
    public function addFilterParam(array $filter) {

        isset($this->params['filter']) || ($this->params['filter'] = array());
        $this->params['filter'] = array_merge($this->params['filter'], $filter);
    }

    /**
     * Adds an order
     *
     * @param   string  $field          - The field to sort by
     * @param   string  $direction      - ( asc | desc )
     */
    public function addOrderParam($field, $direction) {

        $this->params['order'][$field] = $direction;
    }

    /**
     * Sets the Pagination
     *
     * @param int   $page       - Current page
     * @param int   $ammount    - Results per page
     */
    public function setPaginationParam($page = 1, $ammount = 10) {

        $this->params['paginacao'] = array(
            'pagina'        => $page,
            'quantidade'    => $ammount
        );
    }

    /**
     * Returns the request Errors
     *
     * @return mixed
     */
    public function getErrors() {

        return $this->restClient->getErrors();
    }

    /**
     * Returns the Request Information
     *
     * @return mixed
     */
    public function getInfo() {

       return $this->restClient->getInfo();
    }

    /**
     * Returns the request result
     *
     * @return array
     */
    public function getResult() {

        return $this->result;
    }

    /**
     * Returns the request url
     *
     * @return mixed
     */
    public function getRequestURL() {

        return $this->restClient->getUrl();
    }

    /**
     * Returns the number of pages
     *
     * @return mixed
     */
    public function getTotalPages() {

        return $this->pagination['paginas'];
    }

    /**
     * Returns the number of total results
     *
     * @return mixed
     */
    public function getTotalItems() {

        return $this->pagination['total'];
    }

    /**
     * Returns the number of the current page
     *
     * @return mixed
     */
    public function getCurrentPage() {

        return $this->pagination['pagina'];
    }

    /**
     * Returns the number of results per page
     *
     * @return mixed
     */
    public function getResultsPerPage() {

        return $this->pagination['quantidade'];
    }

    /**
     * Reset the class state to its default
     */
    public function reset() {

        $this->restClient->reset();
        $this->restClient->setUrl($this->getURL());
        $this->restClient->addParam('key', $this->getConfig('key'));

        $this->params        = array();
        $this->result        = array();
        $this->vistaMethod   = '';
        $this->requestMethod = 'get';

    }

    /**
     * Executes the request to the API
     */
    public function execute() {

        $this->restClient->setUri($this->vistaMethod);
        $this->restClient->setMethod($this->requestMethod);

        !$this->requestMethod == 'get' ||
            $this->restClient->addParam('showtotal', 1);

        $requestParam = $this->requestMethod == 'get' ? 'pesquisa' : 'cadastro';

        if (!$this->emptyBody)
            $this->restClient->addParam($requestParam, json_encode($this->params));

        $this->restClient->execute();

        $this->result = $this->restClient->getResponse();

        foreach (array_keys($this->pagination) as $pagParam)
            if (isset($this->result[$pagParam])) {
                $this->pagination[$pagParam] = intval($this->result[$pagParam]);
                unset($this->result[$pagParam]);
            }

    }

    public function addParam($key, $value){
        $this->restClient->addParam($key, $value);
    }

}