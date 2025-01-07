<?php

use Framework\Session;
?>

<!-- Session Message -->
<?php if (Session::checkFlashMessage('success')): ?>
    <div class="message bg-green-100 p-3 my-3 text-green-700">
        <?= Session::getFlashMessage('success') ?>
    </div>
<?php endif; ?>

<?php if (Session::checkFlashMessage('error')): ?>
    <div class="message bg-red-100 p-3 my-3 text-red-700">
        <?= Session::getFlashMessage('error') ?>
    </div>
<?php endif; ?>