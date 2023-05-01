<?php
// listener class for event handling
namespace handler\Listener;

use Phalcon\Di\Injectable;
use Phalcon\Mvc\Application;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory;

class Listener extends Injectable
{
    public function beforeAddProduct()
    {
        $res = $this->db->fetchAll(
            "SELECT * FROM settings",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );

        if ($res[0]['title'] == 'tag1') {
            $_POST['name'] = $_POST['name'] . "_" . $_POST['tags'];
        }
        if ($_POST['price'] == '' || $_POST['price'] == 0) {
            $_POST['price'] = $res[0]['price'];
        }
        if ($_POST['stock'] == '' || $_POST['stock'] == 0) {
            $_POST['stock'] = $res[0]['stock'];
        }
    }

    public function beforeOrderProduct()
    {
        $res = $this->db->fetchAll(
            "SELECT zipcode FROM settings",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        if ($_POST['zipcode'] == '') {
            $_POST['zipcode'] = $res[0]['zipcode'];
        }
    }

    public function beforeAccessChange()
    {
        $controllers = [];
        $c = 1;
        foreach (glob(APP_PATH . '/controllers/*Controller.php') as $controller) {
            $className = basename($controller, '.php');
            if ($c == 1) {
                $c++;
                continue;
            }
            $controllers[$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers[$className][] = strtolower($method->name);
                }
            }
        }
        $this->session->set('AccessControl', $controllers);
    }

    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dis)
    {
        $acl = new Memory();
        /**
         * Add the roles
         */
        if ($this->session->has('role')) {
            foreach ($this->session->get('role') as $key => $value) {
                $acl->addRole($value);
            }
        }
        //add components
        if ($this->session->has('AccessControl')) {
            foreach ($this->session->get('AccessControl') as $key => $value) {
                foreach ($value as $index => $action) {
                    $acl->addComponent(
                        strtolower(substr($key, 0, -10)),
                        [
                            strtolower(substr($action, 0, -6))
                        ]
                    );
                }
            }
        }
        $acl->addComponent(
            'access',
            [
                'index',
                'change',
                'save'
            ]
        );

        if ($this->session->has('role')) {
            foreach ($this->session->get('role') as $key => $value) {
                if ($this->session->has($value)) {
                    foreach ($this->session->get($value) as $controller => $action) {
                        $acl->allow($value, strtolower(substr($controller, 0, -10)), strtolower(substr($action, 0, -6)));
                    }
                }
            }
        }
        $acl->allow('admin', '*', '*');
        $role = "guest";
        $controller = "index";
        $action = "index";
        if (!empty($dis->getControllerName())) {
            $controller = $dis->getControllerName();
            $controller = strtolower($controller);
        }
        if (!empty($dis->getActionName())) {
            $action = $dis->getActionName();
            $action = strtolower($action);
        }
        if (!empty($app->request->get('role'))) {
            $role = $app->request->get('role');
        }
        if (true === $acl->isAllowed($role, $controller, $action)) {
            echo "permission granted";
        } else {
            echo "deny";
            // die;
        }
    }
}
