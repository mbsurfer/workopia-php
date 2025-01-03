<?php

namespace App\Controllers;

use Framework\Controller;
use Framework\Validation;

class ListingController extends Controller
{
    private $allowedFields = ['title', 'description', 'salary', 'city', 'state', 'tags', 'company', 'email', 'phone', 'requirements', 'benefits'];
    private $requiredFields = ['title', 'description', 'salary', 'city', 'state', 'email'];

    public function __construct()
    {
        parent::__construct();
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
        $listing = array_intersect_key($_POST, array_flip($this->allowedFields));

        loadView('listings/create', [
            'listing' => $listing
        ]);
    }

    public function show($params)
    {
        $listingId = $params['id'] ?? '';

        if (!$listingId) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $listing = $this->fetchByID('listinngs', $listingId);

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
        $newListingData = array_intersect_key($_POST, array_flip($this->allowedFields));

        // todo: replace id with logged in user id
        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $errors = [];

        foreach ($this->requiredFields as $field) {
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
            $this->createRecord('listings', $newListingData);
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

        // set flash message
        $_SESSION['success_message'] = 'Listing deleted successfully';

        redirect('/listings');
    }

    public function edit($params)
    {
        $listingId = $params['id'] ?? '';

        if (!$listingId) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $listing = $this->fetchByID('listinngs', $listingId);

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    public function update($params = [])
    {
        $listingId = $params['id'] ?? '';

        if (!$listingId) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $listing = $this->fetchByID('listinngs', $listingId);

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $updateListingData = array_intersect_key($_POST, array_flip($this->allowedFields));
        $updateListingData = array_map('sanitize', $updateListingData);
        $updateListingData['id'] = $listingId;

        $errors = [];

        foreach ($this->requiredFields as $field) {
            if (empty($updateListingData[$field]) || !Validation::string($updateListingData[$field])) {
                $errors[$field] = ucfirst($field . ' is required');
            }
        }

        if (!empty($errors)) {

            // Reload view with errors
            loadView('listings/edit', [
                'errors' => $errors,
                'listing' => (object)$updateListingData
            ]);
        } else {
            // Prepare update query
            $fieldsString = implode(', ', array_map(fn($field) => "$field = :$field", array_keys($updateListingData)));
            $sql = "UPDATE listings SET $fieldsString WHERE id = :id";

            // Replace empty strings with null
            $sanitizedData = array_map(fn($value) => $value === '' ? null : $value, $updateListingData);

            // Execute update query
            $this->db->query($sql, $sanitizedData);

            // set flash message
            $_SESSION['success_message'] = 'Listing updated successfully';

            redirect('/listings/' . $listingId);
        }
    }
}
