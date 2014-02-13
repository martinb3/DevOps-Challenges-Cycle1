package org.mbs3.rax.devops.cycle1

import org.jclouds.ContextBuilder
import org.jclouds.openstack.trove.v1.TroveApi
import com.google.common.reflect.TypeToken
import scala.collection.JavaConverters._
import org.jclouds.openstack.trove.v1.utils.TroveUtils
import org.jclouds.openstack.trove.v1.domain.Flavor
import org.jclouds.openstack.trove.v1.domain.Instance
import com.google.common.util.concurrent.Uninterruptibles
import java.util.concurrent.TimeUnit
import java.util.UUID

/**
 * Write a script that creates a Cloud Database. If a CDB already exists with 
 * that name, suggest a new name like "name1" and give the user the choice to 
 * proceed or exit. The script should also create X number of Databases and X 
 * number of users in the new Cloud Database Instance. The script must return 
 * the Cloud DB URL.
 */
object Challenge5 extends Challenge {
  override def doChallenge(args: List[String]): Unit = {
    //println(args.mkString(","))
    
    val troveApi = ContextBuilder.newBuilder(PROVIDER)
            .credentials(getUsername, getAPIKey)
            .buildApi(TypeToken.of(classOf[TroveApi]))

    val flavorApi = troveApi.getFlavorApiForZone(ZONE)
    val instanceApi = troveApi.getInstanceApiForZone(ZONE);
    val utils = new TroveUtils(troveApi)
    
    
    var invalidName = true
    var sDbInstanceName = ""
    do {
      sDbInstanceName = prompt("Please suggest a cloud database instance name: ")
      
      val dbInstances = instanceApi.list().asScala
      invalidName = dbInstances.exists( db => db.getName().equals(sDbInstanceName))
      if(invalidName) {
        var counter = 1
        while(dbInstances.exists( db => db.getName().equals(sDbInstanceName+counter))) { counter+= 1 }
        println("The instance name '"+sDbInstanceName+"' is already taken. " + (sDbInstanceName+counter) + " is available.")  
      }
    } while (invalidName)

    val selectedFlavor = flavorApi.list().asScala.head
    println("Creating instance, selected 1gb size, flavor " + selectedFlavor)
    
    
    val t1 = System.nanoTime();
    var instance = instanceApi.create(selectedFlavor.getId().toString, 1, sDbInstanceName)
    val instanceID = instance.getId()
    while(instance.getStatus() != Instance.Status.ACTIVE) {
      Uninterruptibles.sleepUninterruptibly(15, TimeUnit.SECONDS);
      val t = System.nanoTime()
      val elapsed = Math.round(100.0*((t - t1)/1000000000.0))/100.0
      println("Waiting for instance to become available... " + instance.getStatus() + "... " + elapsed + " seconds elapsed")
      instance = instanceApi.get(instanceID)
    }
    val t2 = System.nanoTime()
    
    println("Instance " + instance.getId()+ " is now available, status " + instance.getStatus())
    println("Instance is accessible via the following URLs:")
    instance.getLinks().asScala.foreach(inst => println("- " +inst.getHref() + " ("+inst.getRelation()+")"))
    
    val sDbCount = prompt("Please suggest a number of databases to create: ")
    val sDbUserCount = prompt("Please suggest a number of users to create: ")

    val userApi = troveApi.getUserApiForZoneAndInstance(ZONE, instance.getId())
    val databaseApi = troveApi.getDatabaseApiForZoneAndInstance(ZONE, instance.getId())

    for(i <- 1 to Integer.valueOf(sDbCount)) {
      val DBNAME = "database"+i
      val successDatabase = databaseApi.create(DBNAME)
      println("Created database " + DBNAME + " " + (if(successDatabase) "successfully" else "unsuccessfully"))
      
      for(i <- 1 to Integer.valueOf(sDbUserCount)) {
    	  val NAME = DBNAME+"user"+i
    	  val PASSWORD = UUID.randomUUID().toString();
    	  val successUser = userApi.create(NAME, PASSWORD, DBNAME);
    	  println("Creating user " + NAME + " with password " + PASSWORD + " " + (if(successUser) "successfully" else "unsuccessfully"))
      }
    }
  }
}