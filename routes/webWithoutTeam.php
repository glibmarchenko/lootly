<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::post('/settings/invitations/{invitation}/accept', 'Api\Settings\Merchants\PendingInvitationController@accept');

        Route::post('/settings/invitations/{invitation}/reject', 'Api\Settings\Merchants\PendingInvitationController@reject');
    });
});