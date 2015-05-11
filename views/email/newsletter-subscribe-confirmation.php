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
                I'm delighted to hear from you, it's been too long since we last spoke.<br/>
                I haven't been feeling well lately, and we both know I'm not getting any younger.
            </p>
            <p>
                Complaints about my failing health aside, I'm going to have to ask a favor of you. <br/>
                I'll be sending additional instructions in a second letter, which should arrive in a week or two, if nothing goes awry.<br/>
                This is a matter of utmost importance, and may very well be the most difficult and confusing thing you've encountered yet.<br/>

            </p>
            <p>
                I have every confidence that you'll perform excellently, however.
            </p>
            <p>
                Warmly,<br/>
                Professor Grimalkin
            </p>
            <br/>
            <p>
                <small>
                    <em>
                        This email is part of an immersive game by <a href="http://solvethelabyrinth.com/">Red Thread Studios, LLC</a>. 
                        If you no longer wish to receive these emails, you may 
                        <a href="http://solvethelabyrinth.com/unsubscribe.php?optout=<?= $email_address ?>">unsubscribe</a> 
                        at any time.
                    </em>
                </small>
            </p>
        </div>
    </body>
</html>