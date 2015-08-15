---
id: expr.name
title: name & iname
layout: docs
category: Expression Terms
permalink: docs/expr/name.html
---

The `name` expression performs exact matches against file names.  By default it
is scoped to the basename of the file:

    ["name", "Makefile"]

You may specify multiple names to match against by setting the second argument
to an array:

    ["name", ["foo.txt", "Makefile"]]

This second form can be accelerated and is preferred over an `anyof`
construction.

You may change the scope of the match via the optional third argument:

    ["name", "path/to/file.txt", "wholename"]
    ["name", ["path/to/one", "path/to/two"], "wholename"]

Finally, you may specify case insensitive evaluation by using `iname` instead
of `name`.

Starting in version 2.9.9, on OS X systems where the watched root is a case
insensitive filesystem (this is the common case for OS X), `name` is equivalent
to `iname`.
