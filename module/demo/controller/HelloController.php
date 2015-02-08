<?php namespace Module\Demo\Controller;

use Illuminate\Routing\Controller as BaseController;

class HelloController extends BaseController {

    public function index()
    {
        return 'hello, world';
    }
}
