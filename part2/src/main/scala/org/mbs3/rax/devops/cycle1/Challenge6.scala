package org.mbs3.rax.devops.cycle1

import scala.collection.JavaConverters._
import org.jclouds.ContextBuilder
import com.google.common.reflect.TypeToken
import org.jclouds.openstack.trove.v1.TroveApi
import org.jclouds.openstack.trove.v1.utils.TroveUtils
import jline.console.completer.StringsCompleter
import org.jclouds.openstack.trove.v1.domain.Instance

/**
 * Write a script that enables and executes a backup for a Cloud Database. Pre-
 * requisite is that the Cloud DB Instance must already exist with a valid
 * database (with some data) and a username with access to the DB. The user
 * executing the script should be able to choose the Instance, Database, and
 * User via the command line arguments to execute the backup.
 */
object Challenge6 extends Challenge {
  def doChallenge(args: List[String]): Unit = {

    val troveApi = ContextBuilder.newBuilder(PROVIDER)
      .credentials(getUsername, getAPIKey)
      .buildApi(TypeToken.of(classOf[TroveApi]))

    val instanceApi = troveApi.getInstanceApiForZone(ZONE);

    var selectedInstance : Instance = null
    while (selectedInstance == null) {
      val instances = instanceApi.list().asScala
    		  .filter(_.getStatus() == Instance.Status.ACTIVE)

      if (instances.isEmpty) {
        println("No instances found, cannot proceed")
        sys.exit(1)
      }
      val instanceCompleter = new StringsCompleter(instances.map(_.getName()).asJavaCollection)
      
      println(); println("Instances to choose from:")
      instances.foreach(i => println(i.getName()))
      val instanceName = prompt("Please choose an instance (autocomplete available): ", instanceCompleter)
      
      val results = instances.filter(_.getName().equals(instanceName.trim()))
      if(!results.isEmpty)
        selectedInstance = results.toList.head
    }

    println("Selected " + selectedInstance)
    
    // spoke with Evan Ochs, he said there isn't anything to do with db or user
    // needed to do a backup, so omitting that here

    // UGH! jclouds doesn't support the backup api. 
    // How does the official JVM SDK not support it? :-(
    
    // http://docs.rackspace.com/cdb/api/v1.0/cdb-getting-started/content/Backup_Instance_Trove.html
    // https://issues.apache.org/jira/browse/JCLOUDS-404
    
  }
}