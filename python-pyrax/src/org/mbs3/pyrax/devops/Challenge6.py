from org.mbs3.pyrax.devops.Challenge import Challenge

from pprint import pprint
from time import time
from pyrax import utils

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

        