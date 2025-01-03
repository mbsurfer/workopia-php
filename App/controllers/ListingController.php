<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

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

    public function show($params)
    {
        $listing_id = $params['id'] ?? '';

        if (!$listing_id) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $queryParams = [
            'id' => $listing_id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $queryParams)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'city', 'state', 'tags', 'company', 'email', 'phone', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        // todo: replace id with logged in user id
        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'salary', 'city', 'state', 'email'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field . ' is required');
            }
        }

        if (!empty($errors)) {
            // Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            // Prepare insert query
            $fieldsString = implode(', ', array_keys($newListingData));
            $valuesString = ':' . implode(', :', array_keys($newListingData));
            $sql = "INSERT INTO listings ($fieldsString) VALUES ($valuesString)";

            // Replace empty strings with null
            $sanitizedData = array_map(fn($value) => $value === '' ? null : $value, $newListingData);

            // Execute insert query
            $this->db->query($sql, $sanitizedData);

            redirect('/listings');
        }
    }

    public function destroy($params = [])
    {
        $listing_id = $params['id'] ?? '';

        if (!$listing_id) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $queryParams = [
            'id' => $listing_id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $queryParams)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $queryParams);

        redirect('/listings');
    }
}
