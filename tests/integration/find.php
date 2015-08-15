<?php
/* Copyright 2012-present Facebook, Inc.
 * Licensed under the Apache License, Version 2.0 */

class findTestCase extends WatchmanTestCase {
  function testFind() {
    $dir = new WatchmanDirectoryFixture();
    $root = $dir->getPath();

    touch("$root/foo.c");
    touch("$root/bar.txt");

    $out = $this->watch($root);
    $this->assertFileList($root, array('bar.txt', 'foo.c'));

    // Make sure we correctly observe deletions
    $this->assertEqual(true, unlink("$root/bar.txt"));
    $this->assertFileList($root, array('foo.c'));

    // touch -> delete -> touch, should show up as exists
    $this->assertEqual(true, touch("$root/bar.txt"));
    $this->assertFileList($root, array('bar.txt', 'foo.c'));

    $this->assertEqual(true, unlink("$root/bar.txt"));

    // A moderately more complex set of changes
    $this->assertEqual(true, mkdir("$root/adir"));
    $this->assertEqual(true, mkdir("$root/adir/subdir"));
    $this->assertEqual(true, touch("$root/adir/subdir/file"));
    $this->assertEqual(true,
      rename("$root/adir/subdir", "$root/adir/overhere"));

    $this->assertFileList($root, array(
      'adir',
      'adir/overhere',
      'adir/overhere/file',
      'foo.c',
    ));

    $this->assertEqual(true, rename(
          $root . DIRECTORY_SEPARATOR . 'adir',
          $root . DIRECTORY_SEPARATOR . 'bdir'));

    $this->assertFileList($root, array(
      'bdir',
      'bdir/overhere',
      'bdir/overhere/file',
      'foo.c',
    ));

    $list = $this->watchmanCommand('watch-list');
    $this->assertTrue(w_is_file_in_file_list($root, $list['roots']));

    $del = $this->watchmanCommand('watch-del', $root);

    $watches = $this->waitForWatchman(
      array('watch-list'),
      function ($list) use ($root) {
        return !w_is_file_in_file_list($root, $list['roots']);
      }
    );
    $this->assertFalse(
      w_is_file_in_file_list($root, $watches['roots']),
      "watch deleted"
    );
  }
}
