<?php

use Phalcon\Mvc\Controller;

class OrderController extends Controller
{
    public function indexAction()
    {
        //redirect to view
    }


    public function addAction()
    {
        $data = [
            'c_name' => $this->escaper->escapeHtml($this->request->getPost('name')),
            'c_address' => $this->escaper->escapeHtml($this->request->getPost('address')),
            'zipcode' => $this->escaper->escapeHtml($this->request->getPost('zipcode')),
            'product' => $this->escaper->escapeHtml($this->request->getPost('product')),
            'qty' => $this->escaper->escapeHtml($this->request->getPost('qty'))
        ];
        $orders = new Orders();
        $orders->assign(
            $data,
            [
                'c_name', 'c_address', 'zipcode', 'product', 'qty'
            ]
        );
        $orders->save();
    }

    public function viewAction(){
        $order = $this->db->fetchAll(
            "SELECT * FROM orders",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $this->view->data=$order;
    }
}
