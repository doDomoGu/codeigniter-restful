<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Book extends MY_Controller
{
    public function index_get()
    {
        echo 'index_get';exit;
        // Display all books
    }

    public function index_post()
    {
        echo 'index_post';
        // Create a new book
    }
}
