<?php

namespace App\Controllers;

use Framework\Database;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create()
    {
        loadView('listings/create');
    }

    public function show()
    {
        $listing_id = $_GET['id'] ?? '';

        if (!$listing_id) {
            // todo: render 404
        }

        $params = [
            'id' => $listing_id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            // todo: render 404
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }
}
