package org.mbs3.rax.devops.cycle1

import org.jclouds.compute.options.RunScriptOptions
import scala.collection.immutable.List
import scala.collection.JavaConverters._
import org.jclouds.ContextBuilder
import com.google.common.reflect.TypeToken
import java.util.HashSet
import com.google.inject.Module
import org.jclouds.openstack.trove.v1.TroveApi
import org.jclouds.openstack.trove.v1.utils.TroveUtils
import jline.console.completer.StringsCompleter
import org.jclouds.openstack.trove.v1.domain.Instance
import java.util.Properties
import org.jclouds.compute.ComputeService
import org.jclouds.compute.ComputeServiceContext
import org.jclouds.sshj.config.SshjSshClientModule
import org.jclouds.compute.config.ComputeServiceProperties.POLL_INITIAL_PERIOD
import org.jclouds.compute.config.ComputeServiceProperties.POLL_MAX_PERIOD
import org.jclouds.openstack.nova.v2_0.domain.zonescoped.ZoneAndId
import java.util.UUID
import org.jclouds.predicates.SocketOpen
import com.google.common.net.HostAndPort
import org.jclouds.scriptbuilder.ScriptBuilder
import org.jclouds.util.Predicates2._
import org.jclouds.scriptbuilder.domain.Statements._
import org.jclouds.scriptbuilder.domain.OsFamily
import org.jclouds.rackspace.cloudloadbalancers.v1.domain.VirtualIP.Type.PUBLIC
import org.jclouds.rackspace.cloudloadbalancers.v1.domain.internal.BaseLoadBalancer.Algorithm.WEIGHTED_LEAST_CONNECTIONS
import org.jclouds.rackspace.cloudloadbalancers.v1.domain.internal.BaseNode.Condition.ENABLED
import org.jclouds.rackspace.cloudloadbalancers.v1.CloudLoadBalancersApi
import org.jclouds.rackspace.cloudloadbalancers.v1.domain._
import org.jclouds.rackspace.cloudloadbalancers.v1.predicates.LoadBalancerPredicates

/**
 *  Write a script that creates 2 Cloud Servers and a Cloud Load Balancer. Add
 *  the 2 servers Private IP Addresses to the Load Balancer for port 80. For a
 *  bonus point, add an Error page served via the Load Balancer for when none
 *  of your nodes are available.
 */
object Challenge7 extends Challenge {
  def doChallenge(args: List[String]): Unit = {

    val modules = Set[Module](new SshjSshClientModule());
    val numServers = 2

    // These properties control how often jclouds polls for a status update
    var overrides = new Properties();
    overrides.setProperty(POLL_INITIAL_PERIOD, "20");
    overrides.setProperty(POLL_MAX_PERIOD, "20");

    val context = ContextBuilder.newBuilder(COMPUTEPROVIDER)
      .credentials(getUsername, getAPIKey)
      .overrides(overrides)
      .modules(modules.asJava)
      .buildView(classOf[ComputeServiceContext]);

    var computeService = context.getComputeService();
    var zoneAndId = ZoneAndId.fromZoneAndId(ZONE, "performance1-1");
    var template = computeService.templateBuilder()
      .locationId(ZONE)
      .osDescriptionMatches(".*CentOS 6.4.*")
      .hardwareId(zoneAndId.slashEncode())
      .build();

    val prefixName = "devops-challenge7-" + UUID.randomUUID().toString().substring(0, 8)
    println("Creating " + numServers + " cloud servers from template with names like " + prefixName)
    // hangs, polling
    val nodes = computeService.createNodesInGroup(prefixName, numServers, template).asScala

    println("Nodes created! Configuring webservers...")

    nodes.foreach(nodeMetadata => {
      val publicAddress = nodeMetadata.getPublicAddresses().iterator().next();
      val privateAddress = nodeMetadata.getPrivateAddresses().iterator().next();

      println("Waiting for SSH to become available on " + nodeMetadata.getName())
      val socketOpen = computeService.getContext().utils().injector().getInstance(classOf[SocketOpen])
      val socketTester = retry(socketOpen, 300, 5, 5, java.util.concurrent.TimeUnit.SECONDS)
      socketTester.apply(HostAndPort.fromParts(publicAddress, 22))

      val message = new StringBuilder()
        .append("Hello from ").append(nodeMetadata.getHostname())
        .append(" @ ").append(publicAddress).append("/").append(privateAddress)
        .append(" in ").append(nodeMetadata.getLocation().getParent().getId())
        .toString();

      val script = new ScriptBuilder().addStatement(exec("yum -y install httpd"))
        .addStatement(exec("/usr/sbin/apachectl start"))
        .addStatement(exec("iptables -I INPUT -p tcp --dport 80 -j ACCEPT"))
        .addStatement(exec("echo '" + message + "' > /var/www/html/index.html"))
        .render(OsFamily.UNIX);

      val options = RunScriptOptions.Builder.blockOnComplete(true);

      computeService.runScriptOnNode(nodeMetadata.getId(), script, options);

      println(" Login: ssh %s@%s%n".format(nodeMetadata.getCredentials().getUser(), publicAddress))
      println(" Password: %s%n".format(nodeMetadata.getCredentials().getPassword()))
      println(" Go to http://%s%n".format(publicAddress));
      
    })

    val clbApi = ContextBuilder.newBuilder(LBPROVIDER)
            .credentials(getUsername, getAPIKey)
            .buildApi(classOf[CloudLoadBalancersApi]);
    
    val lbApi = clbApi.getLoadBalancerApiForZone(ZONE);
    
    val addNodes = nodes.map(node => AddNode.builder().address(node.getPublicAddresses().asScala.head)
    		.condition(ENABLED)
            .port(80)
            .weight(20)
            .build()
      )
      
    println("Creating load balancer with existing nodes " + nodes.map(_.getName()).mkString(", "))
    val createLB = CreateLoadBalancer.builder()
            .name("lb-"+prefixName)
            .protocol("HTTP")
            .port(80)
            .algorithm(WEIGHTED_LEAST_CONNECTIONS)
            .nodes(addNodes.asJavaCollection)
            .virtualIPType(PUBLIC)
            
            .build();
    
    val loadBalancer = lbApi.create(createLB)
    LoadBalancerPredicates.awaitAvailable(lbApi).apply(loadBalancer)
    
    val publicIP = loadBalancer.getVirtualIPs().asScala
    					.filter(_.getType() == PUBLIC)
    					.filter(_.getIpVersion() == VirtualIP.IPVersion.IPV4)
    					.head.getAddress()
    
    println(" %s%n".format(loadBalancer))
    println(" Go to http://%s%n".format(publicIP))
    
    val errorApi = clbApi.getErrorPageApiForZoneAndLoadBalancer(ZONE, loadBalancer.getId())
    errorApi.create("BORKED IT")
    if(errorApi.get().contains("BORKED IT"))
        println("Set error page to BORKED")
    
  }
}