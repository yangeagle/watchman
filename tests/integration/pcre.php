<?php
/* Copyright 2012-present Facebook, Inc.
 * Licensed under the Apache License, Version 2.0 */

class pcreTestCase extends WatchmanTestCase {

  function testPCRE() {
    $dir = new WatchmanDirectoryFixture();
    $root = $dir->getPath();

    touch("$root/foo.c");
    touch("$root/bar.txt");

    $this->watch($root);

    $this->assertFileList($root, array('bar.txt', 'foo.c'));

    $out = $this->watchmanCommand('find', $root, '-p', '.*c$');
    if (idx($out, 'error', '') == "unknown expression term 'pcre'") {
      $this->assertSkipped('no PCRE support');
    }
    $this->assertEqual('foo.c', $out['files'][0]['name']);

    $out = $this->watchmanCommand('find', $root, '-p', '.*txt$');
    $this->assertEqual('bar.txt', $out['files'][0]['name']);

    // Cleanup for invalid pcre
    $out = $this->watchmanCommand('find', $root, '-p', '(');
    $this->assertRegex(
      "/invalid i?pcre: code 14 missing \) at offset 1 in \(/",
      $out['error']
    );

    if ($this->isCaseInsensitive()) {
      // -p matches case sensitivity of filesystem
      $out = $this->watchmanCommand('find', $root, '-p', '.*C$');
      $this->assertEqual('foo.c', $out['files'][0]['name']);
    }

    // Test case insensitive mode
    $out = $this->watchmanCommand('find', $root, '-P', '.*C$');
    $this->assertEqual('foo.c', $out['files'][0]['name']);
  }

}
