<?php

test('mail config defines a default global reply-to for the shop inbox', function () {
    expect(config('mail.reply_to.address'))->toBe('info@sirateqghana.com');
});
