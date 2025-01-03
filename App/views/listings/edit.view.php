<?php loadPartial('head'); ?>
<?php loadPartial('navbar'); ?>
<?php loadPartial('top-banner'); ?>

<?php loadView('listings/form', [
    'listing' => $listing,
    'title' => 'Edit Job Listing',
    'errors' => $errors ?? [],
    'action' => '/listings/' . $listing->id,
    '_method' => 'PUT',
    'cancel_action' => '/listings/' . $listing->id
]) ?>

<?php loadPartial('bottom-banner'); ?>
<?php loadPartial('footer'); ?>