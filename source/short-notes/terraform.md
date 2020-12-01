---
layout: page
title: Terraform
permalink: /short-notes/terraform/
date: 2020-12-01 14:13:51
comments: false
sharing: true
footer: true
---

https://www.terraform.io/

#### `terraform apply` input array parameters

**variables.tf**

```tf
variable "brands" {
  type = "list"
  default = []
}
```

To pass in variable via command line

```
$ terraform apply -var='brands_list=["apple","samsung","huawei"]'
```

Need to append suffix `_list`

##### References:

- [Terraform - Input Variables](https://www.terraform.io/docs/configuration/variables.html#variables-on-the-command-line)
