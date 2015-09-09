<?php
if (!isset($email_address)) {
    $email_address = 'testing@solvethelabyrinth.co.uk';
    if (!isset($name)) {
        $name = '{name}';
    }
}

$exploded = explode('@', filter_var($email_address, FILTER_SANITIZE_EMAIL));
$user     = '';
$domain   = '';

if (isset($exploded[0]) && isset($exploded[1])) {
    $user   = $exploded[0];
    $domain = str_replace('.', 'DOT', $exploded[1]);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Labyrinth Newsletter Subscription Confirmation</title>
    </head>
    <body>
        <div>
            <p>
                <?php if (isset($name)) : ?>
                    <?php if (!empty($name)) : ?>
                        Dear <?= $name ?>,<br/>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($text)) : ?>
                    <?php echo $text; ?>
                <?php else: ?>
                    My deepest apologies, but it would appear that something has gone awry.<br/>
                    Please <a href="mailto:webmaster@solvethelabyrinth.com">contact the webmaster</a> immediately.
                <?php endif; ?>
            </p>
            <br/>
            <p>
                <small>
                    <em>
                        This email is part of an immersive game by <a href="http://solvethelabyrinth.com/">Red Thread Studios, LLC</a>.
                        If you no longer wish to receive these emails, you may
                        <a href="http://solvethelabyrinth.com/unsubscribe/<?= urlencode($user); ?>/<?= urlencode($domain); ?>">unsubscribe</a>
                        at any time.
                    </em>
                </small>
            </p>
        </div>
    </body>
</html>