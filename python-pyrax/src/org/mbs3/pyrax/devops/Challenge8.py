# Write a script that creates a Cloud Performance 1GB Server. The script should
# then add a DNS "A" record for that server. Create a Cloud Monitoring Check 
# and Alarm for a ping of the public IP address on your new server. Return to 
# the STDOUT the IP Address, FQDN, and Monitoring Entity ID of your new server. 

from org.mbs3.pyrax.devops.Challenge import Challenge
from time import time

class Challenge8(object):
    def __init__(self):
        self.foo="bar"
        self.c = Challenge()
        self.pyrax = self.c.getPyrax()
    
    def challenge(self, argv):
        # print("Do it %s" % argv)
        if(len(argv) < 1):
            raise RuntimeError, "Missing arguments 'server name' to create server and create DNS/monitors/alarms"
        
        serverName=argv[0]
        
        pyrax = self.pyrax
        cs = pyrax.cloudservers
        dns = pyrax.cloud_dns
        cm = pyrax.cloud_monitoring
        
        # helps to do this first, so it can error out
        domain = [ dom for dom in dns.list() if dom.name == 'rax.mbs3.org' ][0]
        
        serverName += ("-" + str(long(time())))
        print("Creating server " + serverName + ", waiting for it to go ACTIVE")
        
        ubu_image = [img for img in cs.images.list() if "Ubuntu 12.04" in img.name][0]
        flavor_1GB = [flavor for flavor in cs.flavors.list() if flavor.ram == 1024][0]
        server = cs.servers.create(serverName, ubu_image.id, flavor_1GB.id)
        
        completedServer = pyrax.utils.wait_until(server, "status", ["ACTIVE", "ERROR"])
        network = [ net for net in completedServer.networks['public'] if '.' in net ][0]
        print("Server is active, now creating DNS entry %s pointing to %s" % (completedServer.name+".rax.mbs3.org", network))
        
        fullname = completedServer.name+".rax.mbs3.org"
        records = [{
        "type": "A",
        "name": fullname,
        "data": network,
        "ttl": 6000,
        }] 
        
        domain.add_records(records)
        print("Added domain record " + str(records[0]) +", now building monitor")
        
        ent = cm.create_entity(name=completedServer.name, ip_addresses={"main": network}, metadata={"description": "Just a test entity"})
        
        chk = cm.create_check(ent, label="sample_check", check_type="remote.ping",
        details={}, period=900,
        timeout=20, monitoring_zones_poll=["mzdfw", "mzlon", "mzsyd"],
        target_hostname=fullname)
        
        emailaddr = "martin@mbs3.org"
        email = cm.create_notification("email", label="my_email_notification", details={"address": emailaddr})
        plan = cm.create_notification_plan(label="default-devops-np", warning_state=email, critical_state=email)
        cm.create_alarm(ent, chk, plan, "if (rate(metric['average']) > 10) { return new AlarmStatus(WARNING); } return new AlarmStatus(OK);")
        
        # Return to the STDOUT the IP Address, FQDN, and Monitoring Entity ID
        print("Finished, created server:")
        print("IP Address: " + network)
        print("FQDN: " + fullname) 
        print("Monitoring entity ID: " + ent.id)
        