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
$ terraform apply -var='brands=["apple","samsung","huawei"]'
```

##### References:

- [Terraform - Input Variables](https://www.terraform.io/docs/configuration/variables.html#variables-on-the-command-line)

----

#### Run terraform directly from docker container

Pull the image

```
$ docker pull hashicorp/terraform:light
```

Then run it this way

```
$ cd /path/to/tf_scripts
$ docker run -i -t -v "$PWD:/tf_scripts" hashicorp/terraform:light plan /tf_scripts/
```

##### References:

- [terraform Docker Container](https://hub.docker.com/r/hashicorp/terraform/)
- [Terraform as Docker returns error on plan](https://stackoverflow.com/questions/60366661/terraform-as-docker-returns-error-on-plan/60366859#60366859)

----

#### Run terraform in detached state

Can be used for debugging

```
$ docker run -d -it --name terraform --entrypoint "/usr/bin/tail" -v $(pwd):/workspace -w /workspace hashicorp/terraform:${TAG} sh tail -f /dev/null
$ docker exec -it terraform sh
```

To remove the container

```
$ docker stop terraform
$ docker rm terraform
```

##### References:

- [Running Terraform in Docker Locally](https://www.mrjamiebowman.com/software-development/docker/running-terraform-in-docker-locally/)
