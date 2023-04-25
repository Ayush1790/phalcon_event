<?php

use Phalcon\Mvc\Controller;


class ProductController extends Controller
{
    public function indexAction()
    {
        //redirect to view
    }

    public function addAction()
    {
        $data = [
            'name' => $this->escaper->escapeHtml($this->request->getPost('name')),
            'desc' => $this->escaper->escapeHtml($this->request->getPost('desc')),
            'tags' => $this->escaper->escapeHtml($this->request->getPost('tags')),
            'price' => $this->escaper->escapeHtml($this->request->getPost('price')),
            'stock' => $this->escaper->escapeHtml($this->request->getPost('stock'))
        ];
        $products = new Products();
        $products->assign(
            $data,
            [
                'name', 'desc', 'tags', 'price', 'stock'
            ]
        );
        $products->save();
    }
    public function viewAction()
    {
        $product = $this->db->fetchAll(
            "SELECT * FROM products",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $this->view->data=$product;
    }
}
