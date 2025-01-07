<?php

namespace App\Controllers;

use Framework\Controller;

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $listings = $this->db->query(
            'SELECT * FROM listings ORDER BY created_at DESC LIMIT 6'
        )->fetchAll();

        loadView('home', [
            'listings' => $listings
        ]);
    }
}
