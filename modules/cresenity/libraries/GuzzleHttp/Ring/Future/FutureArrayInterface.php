<?php
/*
namespace GuzzleHttp\Ring\Future;
*/
/**
 * Future that provides array-like access.
 */
interface GuzzleHttp_Ring_Future_FutureArrayInterface extends
    GuzzleHttp_Ring_Future_FutureInterface,
    \ArrayAccess,
    \Countable,
    \IteratorAggregate {};
