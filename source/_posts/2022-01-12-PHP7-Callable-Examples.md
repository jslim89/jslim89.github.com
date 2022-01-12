---
title: PHP7 Callable Examples
date: 2022-01-12 15:08:04
tags:
- php
---

### A callable as class property

```php
<?php
class Processor
{
    private $postHook;

    public function __construct(callable $postHook)
    {
        $this->postHook = $postHook;
    }

    public function process()
    {
        // some statements here
        ($this->postHook)($param1, $param2, $param3);
    }
}

class Client
{
    public function main()
    {
        $processor = new Processor($this->postProcess());
        $processor->process();
    }

    private function postProcess()
    {
        return function ($arg1, $arg2, $arg3) {
            // some statements
        };
    }
}
```

## References:

- [Calling closure assigned to object property directly](https://stackoverflow.com/questions/4535330/calling-closure-assigned-to-object-property-directly/4535383#4535383)
