---
id: expr.suffix
title: suffix
layout: docs
category: Expression Terms
permalink: docs/expr/suffix.html
---

The `suffix` expression evaluates true if the file suffix matches the second
argument.  This matches files name `foo.php` and `foo.PHP` but not `foophp`:

    ["suffix", "php"]

Suffix expression matches are case insensitive.
