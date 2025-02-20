---
layout: page
title: TypeScript - Jest
permalink: /short-notes/typescript-jest/
date: 2024-07-04 18:13:51
comments: false
sharing: true
footer: true
---

#### Mock promisified function

The code

```ts
import MyClass from 'my-module';

const myObject = new MyClass('param-1', 'param-2');
const createPromise = promisify(myObject.create).bind(myObject);

const result = await createPromise('some-param');
```

The test

```ts
const mockCreate = jest.fn((_params, callback) => {
	callback(null, { foo: 'bar' });
});

jest.mock('my-module', () => {
	return jest.fn().mockImplementation(() => ({
		create: mockCreate,
	}));
});

expect(mockCreate).toHaveBeenCalledTimes(1);
```

#### Mock non-class module

```ts
import * as someModule from 'my-module';

jest.mock('my-module');

const mockFunc1 = jest.spyOn(someModule, 'func1');

// alternative of mockFunc1.mockReturnValue('non promise result')
mockFunc1.mockImplementation(() => 'non promise result');

// alternative of mockFunc1.mockResolvedValue('promise result')
mockFunc1.mockImplementation(() => Promise.resolve('promise result'));

// alternative of mockFunc1.mockRejectedValue(new Error('unknown error'))
mockFunc1.mockImplementation(() => { throw new Error('unknown error'); });
```

