<?php

if (!Route::cache()) {

    Route::set('newsletter-subscribe', 'newsletter/subscribe')
            ->defaults(array(
                'controller' => 'newsletter',
                'action' => 'subscribe'
    ));

    Route::set('newsletter-unsubscribe', 'newsletter/unsubscribe')
            ->defaults(array(
                'controller' => 'newsletter',
                'action' => 'unsubscribe'
    ));

    Route::set('newsletter-list', 'newsletter/list(/<filter>)')
            ->defaults(array(
                'controller' => 'newsletter',
                'action' => 'list'
    ));
}