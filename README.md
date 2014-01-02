DevOps-Challenges-Cycle1
========================

Your name: Martin Smith / martin@mbs3.org / martin.smith@rackspace.com

Support DevOps Challenges Cycle 1

~~__Challenge 1__~~: Write a script that builds a 512MB Cloud Server and returns the root password and IP address for the server. This must be done in PHP with php-opencloud 

Example:
```
martin@web1:~/src/DevOps-Challenges-Cycle1$ ./console challenge1 abc12345
Creating a server abc12345 per your request.
Creation of server abc12345 was a success.
Root password is: E56v3P7mooAz
Waiting for server to become active to get all network addresses assigned to it.
  80/100 [======================>-----]  80% Elapsed: 2 mins 
IP addresses are:
	public: 162.209.100.70
	public: 2001:4802:7800:0001:4e69:3be2:ff20:3580
	private: 10.176.8.41
```

~~__Challenge 2__~~: Write a script that builds anywhere from 1 to 3 512MB cloud servers (the number is based on user input). Inject an SSH public key into the server for login. Return the IP addresses for the server. The servers should take their name from user input, and add a numerical identifier to the name. For example, if the user inputs "bob", the servers should be named bob1, bob2, etc... This must be done in PHP with php-opencloud. 

Example:
```
$ ./console challenge2 foo 2 id_rsa_devops_challenge.pub 
Connecting to compute service.
Creating a server foo1 per your request.
Creating a server foo2 per your request.
Waiting for all servers to become active to get all network addresses assigned to it.
Waiting for foo1 to become active...
 100/100 [============================] 100% Elapsed: 2 mins 
foo1 has been created successfully.
Waiting for foo2 to become active...
 100/100 [============================] 100% Elapsed:  1 sec
foo2 has been created successfully.
Creation of server foo1 was a success.
Root password is: i7fQcQGSyNAN
IP addresses are:
	public: 2401:1801:7800:0101:4e69:3be2:ff18:01a7
	public: 119.9.13.93
	private: 10.176.2.187
Creation of server foo2 was a success.
Root password is: 53cv2isFr2ir
IP addresses are:
	public: 2401:1801:7800:0101:4e69:3be2:ff18:055b
	public: 119.9.45.85
	private: 10.176.11.136
All servers have been created to allow access with public id_rsa_devops_challenge.pub.

$ ssh -i id_rsa_devops_challenge -l root 162.242.217.31
The authenticity of host '119.9.13.93 (119.9.13.93)' can't be established.
ECDSA key fingerprint is 8c:6a:c6:29:6b:62:98:7c:82:d3:85:1c:07:48:f4:92.
Are you sure you want to continue connecting (yes/no)? yes
Warning: Permanently added '119.9.13.93' (ECDSA) to the list of known hosts.
Authenticated to 119.9.13.93 ([119.9.13.93]:22).

The programs included with the Ubuntu system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Ubuntu comes with ABSOLUTELY NO WARRANTY, to the extent permitted by
applicable law.

root@foo1:~#

```

~~__Challenge 3__~~: Write a script that prints a list of all of the DNS domains on an account. Let the user select a domain from the list and add an "A" record to that domain by entering an IP Address TTL, and requested "A" record text. This must be done in PHP with php-opencloud. 

Example:
```
$ ./console challenge3  
Connecting to DNS service.
The following domains were found:
 rax.mbs3.org

What domain do you want to operate on? (autocomplete)  rax.mbs3.org

You selected rax.mbs3.org
Please enter a valid IP address: 8.8.8.8
Please enter a valid TTL: 6000
Please enter a valid text value: pleasedontexist.rax.mbs3.org
pleasedontexist.rax.mbs3.org 6000 8.8.8.8 created successfully
```

__Challenge 4__: Write a script that creates a Cloud Files Container. If the container already exists, exit and let the user know. The script should also upload a directory from the local filesystem to the new container, and enable CDN for the new container. The script must return the CDN URL. This must be done in PHP with php-opencloud. 
