DevOps-Challenges-Cycle1 - part 2
=================================

Your name: Martin Smith / martin@mbs3.org / martin.smith@rackspace.com

Support DevOps Challenges Cycle 1 - part 2

All of these examples below can be run / built using python and pyrax
```
mart6985@mart6985-laptop:~/src/DevOps-Challenges-Cycle1/python-pyrax/console

```

~~__Challenge 6__~~: Write a script that enables and executes a backup for a Cloud Database. Pre-requisite is that the Cloud DB Instance must already exist with a valid database (with some data) and a username with access to the DB. The user executing the script should be able to choose the Instance, Database, and User via the command line arguments to execute the backup. Choose your language and SDK!

```
/home/mart6985/src/DevOps-Challenges-Cycle1/python-pyrax/src/Console.py challenge6 martin-inst martin-db
You have selected instance martin-inst and database martin-db
Creating backup martin-inst-backup-1392656366(<CloudDatabaseBackup description=None, id=7fc589e8-e7dc-4273-ab74-2079b9778780, instance_id=23c1c9a8-0039-49b2-8b1b-4c762314fbd2, name=martin-inst-backup-1392656366, size=None, status=NEW>)
```

~~__Challenge 8__~~: Write a script that creates a Cloud Performance 1GB Server. The script should then add a DNS "A" record for that server. Create a Cloud Monitoring Check and Alarm for a ping of the public IP address on your new server. Return to the STDOUT the IP Address, FQDN, and Monitoring Entity ID of your new server. Choose your language and SDK!

