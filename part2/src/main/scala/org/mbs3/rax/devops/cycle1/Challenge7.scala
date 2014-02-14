package org.mbs3.rax.devops.cycle1

import scala.collection.immutable.List
import scala.collection.JavaConverters._
import org.jclouds.ContextBuilder
import com.google.common.reflect.TypeToken
import org.jclouds.openstack.trove.v1.TroveApi
import org.jclouds.openstack.trove.v1.utils.TroveUtils
import jline.console.completer.StringsCompleter
import org.jclouds.openstack.trove.v1.domain.Instance

/** 
 *  Write a script that creates 2 Cloud Servers and a Cloud Load Balancer. Add
 *  the 2 servers Private IP Addresses to the Load Balancer for port 80. For a
 *  bonus point, add an Error page served via the Load Balancer for when none 
 *  of your nodes are available.
 */
object Challenge7 extends Challenge {
	def doChallenge(args: List[String]): Unit = {
	    val troveApi = ContextBuilder.newBuilder(PROVIDER)
	      .credentials(getUsername, getAPIKey)
	      .buildApi(TypeToken.of(classOf[TroveApi]))
	}
}