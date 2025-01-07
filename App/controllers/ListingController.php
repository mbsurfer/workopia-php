<?php

namespace App\Controllers;

use Framework\Controller;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

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
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

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

        $listing = $this->fetchByID('listings', $listingId);

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
        $newListingData['user_id'] = Session::get('user')['id'];
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

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $queryParams);

        Session::setFlashMessage('success', 'Listing deleted successfully');

        redirect('/listings');
    }

    public function edit($params)
    {
        $listingId = $params['id'] ?? '';

        if (!$listingId) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        $listing = $this->fetchByID('listings', $listingId);

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error', 'You are not authorized to edit this listing');
            return redirect('/listings/' . $listing->id);
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

        $listing = $this->fetchByID('listings', $listingId);

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error', 'You are not authorized to edit this listing');
            return redirect('/listings/' . $listing->id);
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
            Session::setFlashMessage('success', 'Listing updated successfully');

            redirect('/listings/' . $listingId);
        }
    }
}
