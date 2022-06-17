---
title: 'Vim DB client: vim-dadbod-ui connection via SSH tunnel'
date: 2022-06-17 18:39:53
tags:
- vim
- mysql
- ssh
---

I'm using [vim-dadbod-ui](https://github.com/kristijanhusak/vim-dadbod-ui) as my MySQL client.

I was trying to connect to RDS via SSH tunnel, finally figure out how to do that.

### 1. Create an SSH tunnel

```
ssh -oStrictHostKeyChecking=no -i ~/.ssh/your-server-key.pem \
    -L 3307:your-db.cluster-ro-abcdefghijkl.ap-southeast-1.rds.amazonaws.com:3306 -N \
    ec2-user@13.12.123.123 &
```

This command will forward your local connection in port **3307** to the RDS in port 3306.
The `&` to make it run on background.


### 2. Add a connection to the vim-dadbod-ui client

Edit the file **~/.local/share/db_ui/connections.json**

```json
[{
	"url": "mysql://user:pass@127.0.0.1:3306/my_local_db",
	"name": "localhost-db"
}, {
	"url": "mysql://someuser:somepass@127.0.0.1:3307/my_staging_db?ssl_mode=DISABLED",
	"name": "staging-db"
}]
```

The first connection is my local DB, and I use port **3306**.  
Since the port **3306** already in used, I map **3307** to the SSH tunnel in step 1,
in the second connection I just refer to `127.0.0.1`, but with port **3307**.

The command is equivalent to

```sh
mysql -h 127.0.0.1 --port 3307 -u someuser -p --ssl-mode=DISABLED
```

_(Some how I need the `--ssl-mode=DISABLED`, otherwise it throws error)_

In case if you need to add more remote connection, you can use the port 3308 or any other unused port.

### 3. Open the client

```sh
vim +DBUI
```

Start using it now

![vim-dadbod-ui](/images/posts/2022-06-17-Vim-DB-client-vim-dadbod-ui-connection-via-SSH-tunnel/db-ui.png)


## References:

- [Create an SSH Tunnel for MySQL Remote Access](https://www.linode.com/docs/guides/create-an-ssh-tunnel-for-mysql-remote-access/)
