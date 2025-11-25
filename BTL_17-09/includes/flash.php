<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
if (!empty($_SESSION['flash']) && is_array($_SESSION['flash'])):
    foreach ($_SESSION['flash'] as $type => $messages):
        foreach ((array) $messages as $msg):
            $cls = 'alert-info';
            if ($type === 'success')
                $cls = 'alert-success';
            if ($type === 'error' || $type === 'danger')
                $cls = 'alert-danger';
            if ($type === 'warning')
                $cls = 'alert-warning';
            ?>
            <div class="alert <?php echo $cls; ?> auto-dismiss" role="alert">
                <?php echo htmlspecialchars($msg); ?>
            </div>
            <?php
        endforeach;
    endforeach;
    // clear flash
    unset($_SESSION['flash']);
endif;
?>