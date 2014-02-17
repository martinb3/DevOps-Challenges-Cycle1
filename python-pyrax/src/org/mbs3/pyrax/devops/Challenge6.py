# Write a script that enables and executes a backup for a Cloud Database. Pre-
# requisite is that the Cloud DB Instance must already exist with a valid 
# database (with some data) and a username with access to the DB. The user 
# executing the script should be able to choose the Instance, Database, and 
# User via the command line arguments to execute the backup.

from org.mbs3.pyrax.devops.Challenge import Challenge
from time import time

class Challenge6(object):
    def __init__(self):
        self.foo="bar"
        self.c = Challenge()
        self.pyrax = self.c.getPyrax()

    def challenge(self, argv):
        #print("Do it %s" % argv)
        if(len(argv) < 2):
            raise RuntimeError, "Missing arguments '<instance> <database>' to execute backup"
            
        instanceName = argv[0]
        databaseName = argv[1]
        
        pyrax = self.pyrax
        cdb = pyrax.cloud_databases
        
        selectedInstance = 0
        selectedDatabase = 0
        
        for inst in cdb.list():
            if(inst.name==instanceName):
                selectedInstance = inst
            for db in inst.list_databases():
                if(db.name==databaseName):
                    selectedDatabase = db
                
        if(selectedInstance == 0 or selectedDatabase == 0):
            print("Could not find desired instance %s and desired database %s in your account" % (instanceName, databaseName))
        
        print("You have selected instance %s and database %s" % (instanceName, databaseName))
        
        backupName = instanceName+"-backup-"+str(long(time()))
        selectedInstance.create_backup(backupName)
        selectedBackup = 0
        for bk in inst.list_backups():
            if(bk.name==backupName):
                selectedBackup = bk
        
        print("Creating backup " + backupName + "("+str(selectedBackup)+")")

        