<?php
// listener class for event handling
namespace handler\Listener;

use Phalcon\Di\Injectable;

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
}
