# vim:ts=4:sw=4:et:
# Copyright 2012-present Facebook, Inc.
# Licensed under the Apache License, Version 2.0
import WatchmanTestCase
import tempfile
import os
import os.path
import json
from pywatchman import bser
import subprocess
import WatchmanInstance
import unittest


class TestDashJCliOption(unittest.TestCase):

    def getSockPath(self):
        return WatchmanInstance.getSharedInstance().getSockPath()

    def doJson(self, addNewLine):
        sockname = self.getSockPath()
        watchman_cmd = json.dumps(['get-sockname'])
        if addNewLine:
            watchman_cmd = watchman_cmd + "\n"

        cli_cmd = [
            'watchman',
            '--sockname={}'.format(sockname),
            '--no-spawn',
            '--no-local',
            '-j',
        ]
        proc = subprocess.Popen(cli_cmd,
                                stdin=subprocess.PIPE,
                                stderr=subprocess.PIPE,
                                stdout=subprocess.PIPE)

        stdout, stderr = proc.communicate(input=watchman_cmd)
        self.assertEqual(proc.poll(), 0, stderr)
        # the response should be json because that is the default
        result = json.loads(stdout)
        self.assertEqual(result['sockname'], sockname)

    def test_jsonInputNoNewLine(self):
        self.doJson(False)

    def test_jsonInputNewLine(self):
        self.doJson(True)

    def test_bserInput(self):
        sockname = self.getSockPath()
        watchman_cmd = bser.dumps(['get-sockname'])
        cli_cmd = [
            'watchman',
            '--sockname={}'.format(sockname),
            '--no-spawn',
            '--no-local',
            '-j',
        ]
        proc = subprocess.Popen(cli_cmd,
                                stdin=subprocess.PIPE,
                                stderr=subprocess.PIPE,
                                stdout=subprocess.PIPE)

        stdout, stderr = proc.communicate(input=watchman_cmd)
        self.assertEqual(proc.poll(), 0, stderr)
        # the response should be bser to match our input
        result = bser.loads(stdout)
        self.assertEqual(result['sockname'], sockname, stdout.encode('hex'))
