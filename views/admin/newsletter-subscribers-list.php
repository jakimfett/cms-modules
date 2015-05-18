<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Labyrinth Newsletter Subscription Confirmation</title>
        <?= HTML::style('media/css/admin.css'); ?>
    </head>
    <body>
        <div>
            <h1>Subscriber List</h1>
            <?php if (count($subscribers) > 0): ?>

                <table style="width:80%">
                    <tr>
                        <th>ID</th>
                        <th>Name</th> 
                        <th>Email</th>
                        <th>Date</th>
                        <th colspan="2">Location</th>
                        <th>Opt Out</th>
                        <th>Note</th>
                    </tr>
                    <?php foreach ($subscribers as $subscriber): ?>
                        <tr id="<?= $subscriber['id']; ?>">
                            <td id="id-<?= $subscriber['id']; ?>"><?= $subscriber['id']; ?></td>
                            <td id="name-<?= $subscriber['id']; ?>"><?= $subscriber['name']; ?></td>
                            <td id="email-<?= $subscriber['id']; ?>"><?= $subscriber['email']; ?></td>
                            <td id="date-<?= $subscriber['id']; ?>"><?= $subscriber['date']; ?></td>
                            <td id="city-<?= $subscriber['id']; ?>">
                                <?php if (isset($subscriber['city'])) : ?>
                                    <?= $subscriber['city']; ?>
                                <?php endif; ?>
                            </td>
                            <td id="region-<?= $subscriber['id']; ?>">
                                <?php if (isset($subscriber['region'])) : ?>
                                    <?= $subscriber['region']; ?>
                                <?php endif; ?>
                            </td>
                            <td id="opt-out-<?= $subscriber['id']; ?>"><?= $subscriber['opt_out']; ?></td>
                            <td id="note-<?= $subscriber['id']; ?>"><?= $subscriber['note']; ?></td> 
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </body>
</html>