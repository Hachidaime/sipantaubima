<?php
namespace app\core;
/**
 * Class App
 *
 * Class ini akan menangani Aplikasi utama
 *
 * @author Hachidaime
 */
class App
{
    /**
     * Controller
     *
     * @var string
     * @access protected
     */
    protected $controller = DEFAULT_CONTROLLER;

    /**
     * Method
     *
     * @var string
     * @access protected
     */
    protected $method = DEFAULT_METHOD;

    /**
     * Params
     *
     * Parameter route
     *
     * @var array
     * @access protected
     */
    protected $params = [];

    /**
     * Name
     *
     * Nmma route
     *
     * @var string
     * @access protected
     */
    protected $name = DEFAULT_NAME;

    /**
     * function __construct
     *
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $router;

        // Todo: match current request
        $match = $router->match();

        $_SESSION['ACTIVE']['name'] = $match['name'];
        if ($this->checkPage($match) === true) {
            list($controller, $method) = explode('::', $match['target']);
            $this->name =
                $match['name'] ??
                str_replace('controller', '', strtolower($controller));

            if (!file_exists("app/controllers/{$controller}.php")) {
                $this->notFound();
            } else {
                $this->controller = $controller;
            }

            $_SESSION['ACTIVE']['controller'] = $this->controller;
            $_SESSION['ACTIVE']['name'] = $this->name;

            require_once "app/controllers/{$this->controller}.php";
            $this->controller = new $this->controller();

            (!method_exists($this->controller, $method)
                    ? $this->notFound()
                    : $this->method == 'error404')
                ? ($this->method = $this->method)
                : ($this->method = $method);
            $_SESSION['ACTIVE']['method'] = $this->method;

            $this->params = $match['params'];
        } else {
            $this->notFound();
            require_once "app/controllers/{$this->controller}.php";
            $this->controller = new $this->controller();
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * function checkPage
     *
     * Fungsi ini mengecek halaman valid
     *
     * @access private
     * @param mixed $match routing match
     */
    private function checkPage($match)
    {
        if ($match === false) {
            $this->notFound();
        } else {
            return true;
        }
    }

    /**
     * function notFound
     *
     * Fungsi ini mengeset method sebagai error 404
     *
     * @access private
     */
    private function notFound()
    {
        $this->method = 'error404';
        header('X-PHP-Response-Code: 404', true, 404);
    }
}
