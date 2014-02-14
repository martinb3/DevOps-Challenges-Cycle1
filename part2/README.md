DevOps-Challenges-Cycle1 - part 2
=================================

Your name: Martin Smith / martin@mbs3.org / martin.smith@rackspace.com

Support DevOps Challenges Cycle 1 - part 2

All of these examples below can be run / built using sbt, eclipse, or the commandline. For sbt, see:
```
mart6985@mart6985-laptop:~/src/DevOps-Challenges-Cycle1/part2$ sbt
[info] Loading project definition from /home/mart6985/src/DevOps-Challenges-Cycle1/part2/project
[info] Set current project to DevOps-Challenges-Cycle1 (in build file:/home/mart6985/src/DevOps-Challenges-Cycle1/part2/)
> compile
[success] Total time: 0 s, completed Feb 13, 2014 2:41:14 PM
> run challenge5
[info] Running org.mbs3.rax.devops.cycle1.Console challenge5
Please suggest a cloud database instance name:
```

~~__Challenge 5__~~: Write a script that creates a Cloud Database. If a CDB already exists with that name, suggest a new name like "name1" and give the user the choice to proceed or exit. The script should also create X number of Databases and X number of users in the new Cloud Database Instance. The script must return the Cloud DB URL. Choose your language and SDK!

```
Please suggest a cloud database instance name: martin1
martin1
The instance name 'martin1' is already taken. martin11 is available.
Please suggest a cloud database instance name: martin2
martin2
Creating instance, selected 1gb size, flavor Flavor{id=1, name=Optional.of(512MB Instance), ram=512}
Waiting for instance to become available... BUILD... 15.77 seconds elapsed
Waiting for instance to become available... BUILD... 31.17 seconds elapsed
Waiting for instance to become available... BUILD... 46.61 seconds elapsed
Waiting for instance to become available... BUILD... 62.01 seconds elapsed
Waiting for instance to become available... BUILD... 77.47 seconds elapsed
Waiting for instance to become available... BUILD... 92.87 seconds elapsed
Instance 29f7d90e-4fdb-48d6-8080-c8a8a6ff4b72 is now available, status ACTIVE
Instance is accessible via the following URLs:
- https://iad.databases.api.rackspacecloud.com/v1.0/869424/instances/29f7d90e-4fdb-48d6-8080-c8a8a6ff4b72 (SELF)
- https://iad.databases.api.rackspacecloud.com/instances/29f7d90e-4fdb-48d6-8080-c8a8a6ff4b72 (BOOKMARK)
Please suggest a number of databases to create: 3
3
Please suggest a number of users to create: 2
2
Created database database1 successfully
Creating user database1user1 with password bf42ff3e-3e90-4c7e-9eb9-47e7a3c34dd9 successfully
Creating user database1user2 with password 2a662d91-64a7-43dd-b6d0-5ef821890f54 successfully
Created database database2 successfully
Creating user database2user1 with password b5abadc1-0ddc-403c-833e-de5d8c6f22b1 successfully
Creating user database2user2 with password 8b8cf997-16e9-4712-83ae-bbbd078436c5 successfully
Created database database3 successfully
Creating user database3user1 with password 3ef2c556-a699-4d09-b9f0-33b33e424fe7 successfully
Creating user database3user2 with password a1f10a02-34cf-4c79-9f03-953a9d427375 successfully
```

~~__Challenge 6__~~: Write a script that enables and executes a backup for a Cloud Database. Pre-requisite is that the Cloud DB Instance must already exist with a valid database (with some data) and a username with access to the DB. The user executing the script should be able to choose the Instance, Database, and User via the command line arguments to execute the backup. Choose your language and SDK!

~~__Challenge 7__~~: Write a script that creates 2 Cloud Servers and a Cloud Load Balancer. Add the 2 servers Private IP Addresses to the Load Balancer for port 80. For a bonus point, add an Error page served via the Load Balancer for when none of your nodes are available. Choose your language and SDK!

```
> run challenge7
[info] Running org.mbs3.rax.devops.cycle1.Console challenge7
Creating 2 cloud servers from template with names like devops-challenge7-a09375c7
Nodes created! Configuring webservers...
Waiting for SSH to become available on devops-challenge7-a09375c7-cf3
 Login: ssh root@162.242.244.117

 Password: BY8TaTvgRNe7

 Go to http://162.242.244.117

Waiting for SSH to become available on devops-challenge7-a09375c7-d33
 Login: ssh root@162.242.244.22

 Password: uJm7A3oEuB67

 Go to http://162.242.244.22

Creating load balancer with existing nodes devops-challenge7-a09375c7-d33, devops-challenge7-a09375c7-cf3
 LoadBalancer{id=21233, region=IAD, status=BUILD, name=lb-devops-challenge7-a09375c7, protocol=HTTP, port=80, nodeCount=2, nodes=[Node{id=35393, address=162.242.244.117, port=80, condition=ENABLED, type=PRIMARY, weight=20, status=ONLINE, metadata={}}, Node{id=35395, address=162.242.244.22, port=80, condition=ENABLED, type=PRIMARY, weight=20, status=ONLINE, metadata={}}], timeout=30, algorithm=WEIGHTED_LEAST_CONNECTIONS, halfClosed=false, clusterName=ztm-n01.iad3.lbaas.rackspace.net, created=Fri Feb 14 15:19:36 CST 2014, updated=Fri Feb 14 15:19:36 CST 2014, contentCaching=false, connectionLogging=false, accessRules=[], metadata={}, uri=https://iad.loadbalancers.api.rackspacecloud.com/v1.0/869424/loadbalancers/21233, sourceAddresses=SourceAddresses{ipv6Public=2001:4802:7901::4/64, ipv4Public=162.209.114.4, ipv4Servicenet=10.189.254.4}, virtualIPs=[VirtualIPWithId{id=2691, address=162.242.199.112, ipVersion=IPV4, type=PUBLIC}, VirtualIPWithId{id=9016093, address=2001:4802:7901:0000:4e69:3be2:0000:0002, ipVersion=IPV6, type=PUBLIC}]}

 Go to http://162.242.199.112

Set error page to BORKED
[success] Total time: 173 s, completed Feb 14, 2014 3:20:34 PM
> 
```

~~__Challenge 8__~~: Write a script that creates a Cloud Performance 1GB Server. The script should then add a DNS "A" record for that server. Create a Cloud Monitoring Check and Alarm for a ping of the public IP address on your new server. Return to the STDOUT the IP Address, FQDN, and Monitoring Entity ID of your new server. Choose your language and SDK!

