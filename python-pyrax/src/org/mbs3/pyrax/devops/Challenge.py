
import pyrax
import ConfigParser

class Challenge:
    
    def getInifile(self):
        config  = ConfigParser.ConfigParser()
        
        from os.path import expanduser
        home = expanduser("~")
        
        config.read(home + "/.rackspace_cloud_credentials")
        return config
    
    def getUsername(self):
        d = self.getInifile()
        return d.get('credentials','username')
    
    def getApiKey(self):
        d = self.getInifile()
        return d.get('credentials','api_key')
    
    def getPyrax(self):
        cls = pyrax.utils.import_class('pyrax.identity.rax_identity.RaxIdentity')
        pyrax.identity = cls()
        pyrax.set_credentials(self.getUsername(), self.getApiKey(), region='IAD')
        return pyrax
