<?php

namespace app\home\controller;

use gophp\cache;
use gophp\controller;
use gophp\cookie;
use gophp\db;
use gophp\session;

class demo extends controller {

    public function index(){


        print_r(input());

    }


}