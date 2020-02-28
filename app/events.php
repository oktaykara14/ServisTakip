<?php

/** @noinspection PhpUndefinedClassInspection */
Event::listen('generic.event',function() {
    return BrainSocket::message('generic.event',array('message'=>'A message from a generic event fired in Laravel!'));
});

/** @noinspection PhpUndefinedClassInspection */
Event::listen('app.success',function() {
    return BrainSocket::success(array('There was a Laravel App Success Event!'));
});

/** @noinspection PhpUndefinedClassInspection */
Event::listen('app.error',function() {
    return BrainSocket::error(array('There was a Laravel App Error!'));
});