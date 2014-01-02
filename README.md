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

__Challenge 2__: Write a script that builds anywhere from 1 to 3 512MB cloud servers (the number is based on user input). Inject an SSH public key into the server for login. Return the IP addresses for the server. The servers should take their name from user input, and add a numerical identifier to the name. For example, if the user inputs "bob", the servers should be named bob1, bob2, etc... This must be done in PHP with php-opencloud. 

__Challenge 3__: Write a script that prints a list of all of the DNS domains on an account. Let the user select a domain from the list and add an "A" record to that domain by entering an IP Address TTL, and requested "A" record text. This must be done in PHP with php-opencloud. 

__Challenge 4__: Write a script that creates a Cloud Files Container. If the container already exists, exit and let the user know. The script should also upload a directory from the local filesystem to the new container, and enable CDN for the new container. The script must return the CDN URL. This must be done in PHP with php-opencloud. 
